<?php
require_once __DIR__ . '/php/info.php'; // Loads config, sets up Twig

$parts = parse_url($_SERVER['REQUEST_URI']);
$page_url = explode('/', trim($parts['path'] ?? '', '/')); // Added trim and null coalesce
$streaming_url_param = end($page_url); // e.g., "naruto-episode-112"

if (empty($streaming_url_param)) {
    // Handle missing episode identifier (e.g., redirect or error)
    error_log("streaming.php: Episode identifier (streaming_url_param) is missing.");
    // For now, use a default to prevent further errors, though this page wouldn't make sense.
    $streaming_url_param = 'default-anime-episode-1';
}

// --- Data Fetching using ApiClient ---
$apiClient = \App\Utils\ConfigLoader::getApiClient();
$anime = []; // From getEpisodeStreamingLinks
$fetchDetails = []; // From getAnimeDetails

$anime_slug_for_details = preg_replace('/-episode-[0-9]+$/', '', $streaming_url_param);


if ($apiClient) {
    $anime = $apiClient->getEpisodeStreamingLinks($streaming_url_param);

    // Use the derived anime_info (slug) from streaming links to get general anime details
    $anime_info_slug = $anime['anime_info'] ?? $anime_slug_for_details;
    if (!empty($anime_info_slug)) {
        $detailsData = $apiClient->getAnimeDetails($anime_info_slug);
        // We only need a subset of what getAnimeDetails provides for $fetchDetails here
        $fetchDetails = [
            'name' => $detailsData['name'] ?? 'N/A',
            'synopsis' => $detailsData['synopsis'] ?? 'N/A',
            'imageUrl' => $detailsData['imageUrl'] ?? (defined('BASE_URL') ? BASE_URL : '') . '/img/default_anime_poster.jpg',
            'type' => $detailsData['type'] ?? 'N/A',
        ];
    } else {
        error_log("streaming.php: Could not determine anime_info_slug from streaming_url_param.");
         $fetchDetails = [ /* fallback static placeholders if needed */ ];
    }

} else {
    error_log("ApiClient not available in streaming.php. Using fallback static placeholders.");
    // Fallback static placeholders if ApiClient fails
    $anime = [ 'animeNameWithEP' => 'Error loading episode', /* other keys with defaults */ ];
    $fetchDetails = [ 'name' => 'Error', /* other keys */ ];
}

// Disqus shortname (should ideally come from a config file or environment variable)
$DISQUS_SHORTNAME = 'YOUR_DISQUS_SHORTNAME_HERE';
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
    'anime' => $anime,
    'fetchDetails' => $fetchDetails,
    'streaming_url_param' => $streaming_url_param, // The part of URL like "naruto-episode-112"
    'DISQUS_SHORTNAME' => $DISQUS_SHORTNAME,
    'recent_release_content' => $recent_release_content,
    // sub_category_content is no longer needed here
];

// Render the Twig template
try {
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('streaming.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader in streaming.php.");
        echo "Error: Could not initialize the templating engine.";
    }
} catch (\Throwable $e) {
    error_log("Error in streaming.php: " . $e->getMessage() . " Trace: " . $e->getTraceAsString());
    echo "An error occurred while loading the page. Please try again later.";
}

?>
