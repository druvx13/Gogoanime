<?php
require_once __DIR__ . '/php/info.php'; // Loads config, sets up Twig

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search_keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$encoded_keyword = str_replace(' ', '%20', $search_keyword); // For use in API calls if needed

// --- Data Fetching using ApiClient ---
$apiClient = \App\Utils\ConfigLoader::getApiClient();
$search_results_items = [];
$pagination_html = '';

if ($apiClient) {
    if (!empty($search_keyword)) {
        $search_results_items = $apiClient->getSearchResults($search_keyword, $page);
    }

    // Simple placeholder for pagination (ApiClient doesn't provide total pages yet)
    $pagination_html = "<ul class='pagination-list'>";
    if (!empty($search_keyword) && count($search_results_items) > 0) {
        $totalPagesMock = 2; // Assume 2 total pages for mock search results for now
        for ($i = 1; $i <= $totalPagesMock; $i++) {
            $selected_class = ($i == $page) ? "selected" : "";
            $pagination_html .= "<li class='$selected_class'><a href='?keyword=$encoded_keyword&page=$i' data-page='$i'>$i</a></li>";
        }
    } else if (empty($search_keyword)) {
        $pagination_html = "<li>Please enter a search term.</li>";
    } else {
        $pagination_html = "<li>No results found.</li>"; // Changed from "No results to paginate"
    }
    $pagination_html .= "</ul>";

} else {
    error_log("ApiClient not available in search.php.");
    $pagination_html = "<li>Could not load pagination due to API client error.</li>";
}
// --- End Data Fetching ---


// --- Capture Dynamic Includes ---
$recent_release_content = '';
if (file_exists(__DIR__ . '/php/include/recentRelease.php')) {
    ob_start();
    include __DIR__ . '/php/include/recentRelease.php';
    $recent_release_content = ob_get_clean();
}
// Static include sub-category.html is now handled in Twig template directly
// --- End Capture Includes ---

// Prepare variables for Twig
$template_vars = [
    'BASE_URL' => BASE_URL,
    'WEBSITE_NAME' => WEBSITE_NAME,
    'request_uri' => $_SERVER['REQUEST_URI'],
    'search_keyword' => $search_keyword,
    'search_results_items' => $search_results_items,
    'pagination_html' => $pagination_html,
    'recent_release_content' => $recent_release_content,
    // sub_category_content is no longer needed here
];

// Render the Twig template
try {
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('search-results.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader in search.php.");
        echo "Error: Could not initialize the templating engine.";
    }
} catch (\Throwable $e) {
    error_log("Error in search.php: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
    echo "An error occurred while loading the page. Please try again later.";
}

?>