<?php
require_once __DIR__ . '/php/info.php'; // Loads config, sets up Twig

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Data Fetching using ApiClient ---
$apiClient = \App\Utils\ConfigLoader::getApiClient();
$popular_anime_items = [];
$pagination_html = '';

if ($apiClient) {
    $popular_anime_items = $apiClient->getPopularAnime($page);

    // Simple placeholder for pagination (ApiClient doesn't provide total pages yet)
    $pagination_html = "<ul class='pagination-list'>";
    $totalPagesMock = 4; // Assume 4 total pages for mock popular items
    for ($i = 1; $i <= $totalPagesMock; $i++) {
        $selected_class = ($i == $page) ? "selected" : "";
        $pagination_html .= "<li class='$selected_class'><a href='?page=$i' data-page='$i'>$i</a></li>";
    }
    $pagination_html .= "</ul>";
} else {
    error_log("ApiClient not available in popular.php.");
    $pagination_html = "<li>Could not load pagination.</li>";
}
// --- End Placeholder Data ---


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
    'popular_anime_items' => $popular_anime_items,
    'pagination_html' => $pagination_html,
    'recent_release_content' => $recent_release_content,
    // 'subcategory_content' => $subcategory_content, // Removed
];

// Render the Twig template
try {
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('popular.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader in popular.php.");
        echo "Error: Could not initialize the templating engine.";
    }
} catch (\Throwable $e) {
    error_log("Error in popular.php: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
    echo "An error occurred while loading the page. Please try again later.";
}

?>