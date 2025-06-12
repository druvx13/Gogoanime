<?php
require_once __DIR__ . '/php/info.php'; // Loads config, sets up Twig

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// Determine the active letter for the filter. Example: from a URL like /anime-list/A or /anime-list?aph=A
$active_char_from_uri = 'All'; // Default
if (isset($_SERVER['REQUEST_URI'])) {
    $path_parts = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
    if (count($path_parts) > 0 && preg_match('/^anime-list-([A-Z])$/i', $path_parts[0], $matches)) {
        $active_char_from_uri = strtoupper($matches[1]);
    } elseif (isset($_GET['aph'])) {
        $active_char_from_uri = strtoupper(trim($_GET['aph']));
        if (!preg_match('/^[A-Z]$/', $active_char_from_uri)) {
            $active_char_from_uri = 'All';
        }
    }
}


// --- Data Fetching using ApiClient ---
$apiClient = \App\Utils\ConfigLoader::getApiClient();
$anime_list_items = [];
$pagination_html = '';

if ($apiClient) {
    // TODO: ApiClient::getAllAnime currently doesn't support character filtering.
    // For now, we fetch all for the page, then filter if a character is selected.
    // This is inefficient and should be handled by the API in a real scenario.
    $allAnimeForPage = $apiClient->getAllAnime($page);

    if ($active_char_from_uri != 'All' && !empty($active_char_from_uri)) {
        $anime_list_items = array_filter($allAnimeForPage, function($item) use ($active_char_from_uri) {
            return stripos($item['animeTitle'], $active_char_from_uri) === 0;
        });
    } else {
        $anime_list_items = $allAnimeForPage;
    }

    // Simple placeholder for pagination (ApiClient doesn't provide total pages yet)
    $pagination_html = "<ul class='pagination-list'>";
    $totalPagesMock = 3; // Assume 3 total pages for mock
    for ($i = 1; $i <= $totalPagesMock; $i++) {
        $selected_class = ($i == $page) ? "selected" : "";
        $urlSuffix = ($active_char_from_uri != 'All') ? "&aph=$active_char_from_uri" : "";
        // Adjust href for pretty URLs if anime-list-X is used
        if ($active_char_from_uri != 'All' && preg_match('/^anime-list-([A-Z])$/i', $path_parts[0])) {
             $baseLink = "/anime-list-" . $active_char_from_uri;
             $pagination_html .= "<li class='$selected_class'><a href='$baseLink?page=$i' data-page='$i'>$i</a></li>";
        } else {
             $pagination_html .= "<li class='$selected_class'><a href='?page=$i$urlSuffix' data-page='$i'>$i</a></li>";
        }
    }
    $pagination_html .= "</ul>";

} else {
    error_log("ApiClient not available in anime-list.php.");
    $pagination_html = "<li>Could not load pagination.</li>";
}
// --- End Data Fetching ---


// --- Capture Dynamic/Static Includes ---
$recent_release_content = '';
if (file_exists(__DIR__ . '/php/include/recentRelease.php')) {
    ob_start();
    include __DIR__ . '/php/include/recentRelease.php';
    $recent_release_content = ob_get_clean();
}

// $subcategory_content is no longer captured here, it's included in Twig template
// --- End Capture Includes ---

// Prepare variables for Twig
$template_vars = [
    'BASE_URL' => BASE_URL,
    'WEBSITE_NAME' => WEBSITE_NAME,
    'request_uri' => $_SERVER['REQUEST_URI'],
    'anime_list_items' => array_values($anime_list_items), // Re-index array after filter
    'pagination_html' => $pagination_html,
    'active_char' => $active_char_from_uri,
    'recent_release_content' => $recent_release_content,
    // 'subcategory_content' => $subcategory_content, // Removed
];

// Render the Twig template
try {
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('anime-list.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader in anime-list.php.");
        echo "Error: Could not initialize the templating engine.";
    }
} catch (\Throwable $e) {
    error_log("Error in anime-list.php: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
    echo "An error occurred while loading the page. Please try again later.";
}

?>