<?php
// Adjusted path for php/info.php
require_once __DIR__ . '/../php/info.php'; // Loads config, sets up Twig

$parts = parse_url($_SERVER['REQUEST_URI']);
$page_url = explode('/', trim($parts['path'], '/'));
// Assuming structure is /genre/{genre-name} or /genre/id/{genre-name}
// For /genre/action -> $page_url[0] = 'genre', $page_url[1] = 'action'
// For /genre/id/action -> $page_url[0] = 'genre', $page_url[1] = 'id', $page_url[2] = 'action'
$genre_slug_from_url = end($page_url); // Takes the last part of the URL path

$genre_name_display = str_replace("+", " ", $genre_slug_from_url);
$genre_name_display = str_replace("-", " ", $genre_name_display); // Also replace hyphens for display
$genre_name_display = ucwords($genre_name_display); // Capitalize for display

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Data Fetching using ApiClient ---
$apiClient = \App\Utils\ConfigLoader::getApiClient();
$genre_anime_items = [];
$pagination_html = '';

if ($apiClient) {
    // Use the slugified version for API call, display version for title
    $genre_api_slug = str_replace(" ", "-", strtolower($genre_name_display)); // Ensure consistent slug for API
    $genre_anime_items = $apiClient->getAnimeByGenre($genre_api_slug, $page);

    // Simple placeholder for pagination (ApiClient doesn't provide total pages yet)
    $pagination_html = "<ul class='pagination-list'>";
    $totalPagesMock = 2; // Assume 2 total pages for mock genre items
    for ($i = 1; $i <= $totalPagesMock; $i++) {
        $selected_class = ($i == $page) ? "selected" : "";
        $pagination_html .= "<li class='$selected_class'><a href='../genre/$genre_slug_from_url?page=$i' data-page='$i'>$i</a></li>";
    }
    $pagination_html .= "</ul>";
} else {
    error_log("ApiClient not available in genre/id.php.");
    $pagination_html = "<li>Could not load pagination.</li>";
}
// --- End Placeholder Data ---


// --- Capture Dynamic Includes ---
// Adjusted path for recentRelease.php
$recent_release_content = '';
if (file_exists(__DIR__ . '/../php/include/recentRelease.php')) {
    ob_start();
    include __DIR__ . '/../php/include/recentRelease.php';
    $recent_release_content = ob_get_clean();
}
// Static include sub-category.html is now handled in Twig template directly
// --- End Capture Includes ---

// Prepare variables for Twig
$template_vars = [
    'BASE_URL' => BASE_URL, // BASE_URL is defined in config.php, included by info.php
    'WEBSITE_NAME' => WEBSITE_NAME, // Also from config.php
    'request_uri' => $_SERVER['REQUEST_URI'],
    'genre_name' => $genre_name_display,
    'genre_anime_items' => $genre_anime_items,
    'pagination_html' => $pagination_html,
    'recent_release_content' => $recent_release_content,
    // sub_category_content is no longer needed here
];

// Render the Twig template
try {
    // ConfigLoader path needs to be correct relative to vendor/autoload.php
    // The autoloader should handle App\Utils\ConfigLoader fine if vendor/autoload.php is included.
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('genre-listing.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader in genre/id.php.");
        echo "Error: Could not initialize the templating engine. Check php/info.php and src/Utils/ConfigLoader.php paths.";
    }
} catch (\Throwable $e) {
    error_log("Error in genre/id.php: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
    echo "An error occurred while loading the page. Please try again later.";
}

?>