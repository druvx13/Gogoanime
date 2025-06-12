<?php
require_once __DIR__ . '/php/info.php'; // Loads config, sets up Twig (via ConfigLoader)

$apiClient = \App\Utils\ConfigLoader::getApiClient();

// --- Data Fetching using ApiClient (Placeholders for now) ---
$recent_release_items = [];
$recently_added_series = [];
$ongoing_series = [];
$pagination_html = "<ul class='pagination-list'><li class='selected'><a href='?page=1' data-page='1'>1</a></li><li><a href='?page=2' data-page='2'>2</a></li></ul>"; // Static placeholder for now
$popular_ongoing_content = "<!-- Popular and ongoing content section (JS loaded or static) -->";


if ($apiClient) {
    $recent_release_items = $apiClient->getRecentReleases();
    // TODO: Add getRecentlyAddedSeries and getOngoingSeries to ApiClient and call them here
    // For now, using placeholders from ApiClient if they were added, or keeping them empty
    if (method_exists($apiClient, 'getRecentlyAddedSeries')) {
        $recently_added_series = $apiClient->getRecentlyAddedSeries();
    } else {
        // Fallback placeholder if method doesn't exist yet
        $recently_added_series = [
            ['animeId' => 'placeholder-added-1', 'animeName' => 'Newly Added Series X (Fallback)'],
            ['animeId' => 'placeholder-added-2', 'animeName' => 'Newly Added Series Y (Fallback)'],
        ];
    }
    if (method_exists($apiClient, 'getOngoingSeries')) {
        $ongoing_series = $apiClient->getOngoingSeries();
    } else {
        // Fallback placeholder
        $ongoing_series = [
            ['animeId' => 'placeholder-ongoing-1', 'animeName' => 'Ongoing Series Alpha (Fallback)'],
            ['animeId' => 'placeholder-ongoing-2', 'animeName' => 'Ongoing Series Beta (Fallback)'],
        ];
    }
} else {
    error_log("ApiClient not available in home.php. Using fallback static placeholders.");
    // Fallback static placeholders if ApiClient fails
    $recent_release_items = [ /* static array from before */ ];
    $recently_added_series = [ /* static array from before */ ];
    $ongoing_series = [ /* static array from before */ ];
}
// --- End Data Fetching ---

// Static includes (sidebar_genre, sub-category) are now handled directly in Twig templates.

// Prepare variables for Twig
$template_vars = [
    'BASE_URL' => BASE_URL,
    'WEBSITE_NAME' => WEBSITE_NAME,
    'recent_release_items' => $recent_release_items,
    'pagination_html' => $pagination_html, // This will be refactored when API provides pagination
    'popular_ongoing_content' => $popular_ongoing_content,
    'recently_added_series' => $recently_added_series,
    'ongoing_series' => $ongoing_series,
    // Other variables like API_LINK might be needed by JS if not refactored
];

// Render the Twig template
try {
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('home.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader in home.php.");
        echo "Error: Could not initialize the templating engine.";
    }
} catch (\Throwable $e) { // Catch any generic error/exception
    error_log("Error in home.php: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
    echo "An error occurred while loading the page. Please try again later.";
}

?>