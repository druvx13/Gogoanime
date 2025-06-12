<?php
require_once __DIR__ . '/php/info.php'; // Loads config, sets up Twig

$url_parts = parse_url($_SERVER['REQUEST_URI']);
$page_path_segments = explode('/', trim($parts['path'] ?? '', '/')); // Added trim and null coalesce
$url_param = end($page_path_segments);
if (empty($url_param)) {
    // Handle case where URL param might be missing, e.g., redirect or show error
    // For now, using a default placeholder ID for ApiClient
    $url_param = 'default-anime-id';
    error_log("anime-details.php: URL parameter for anime ID is missing. Using default.");
}


// --- Data Fetching using ApiClient ---
$apiClient = \App\Utils\ConfigLoader::getApiClient();
$fetchDetails = [];
$fetchdetailss = []; // This corresponds to 'extended_details' from ApiClient

if ($apiClient) {
    $apiData = $apiClient->getAnimeDetails($url_param);
    $fetchDetails = [
        'name' => $apiData['name'] ?? 'N/A',
        'synopsis' => $apiData['synopsis'] ?? 'N/A',
        'othername' => $apiData['othername'] ?? 'N/A',
        'imageUrl' => $apiData['imageUrl'] ?? (defined('BASE_URL') ? BASE_URL : '') . '/img/default_anime_poster.jpg',
        'type' => $apiData['type'] ?? 'N/A',
        'episode_id' => $apiData['episode_id'] ?? [],
        'episode_info_html' => $apiData['episode_info_html'] ?? '<p>Episode info not available.</p>',
        'episode_page' => $apiData['episode_page'] ?? '',
        // Fields that were originally in $anime variable in streaming.php, if needed here
        'anime_info' => $url_param, // Assuming the slug is the anime_info
    ];
    $fetchdetailss = $apiData['extended_details'] ?? [
        'description' => $apiData['synopsis'] ?? 'N/A', // Fallback to main synopsis
        'genres' => ['N/A'],
        'releaseDate' => 'N/A',
        'status' => 'N/A',
        'otherName' => $apiData['othername'] ?? 'N/A',
    ];
} else {
    error_log("ApiClient not available in anime-details.php. Using fallback static placeholders.");
    // Static placeholders if ApiClient fails (simplified)
    $fetchDetails = [
        'name' => 'Error: Could not load details',
        'synopsis' => 'Please try again later.',
        'othername' => '', 'imageUrl' => '', 'type' => '', 'episode_id' => [],
        'episode_info_html' => '', 'episode_page' => '', 'anime_info' => $url_param,
    ];
    $fetchdetailss = ['description' => '', 'genres' => [], 'releaseDate' => '', 'status' => '', 'otherName' => ''];
}
// --- End Data Fetching ---

// Capture content of includes
// popup_content is no longer captured here, it's included in Twig template

$recent_release_content = '';
// For recentRelease.php, we'll need to execute it and capture its output if it renders a Twig template itself later.
// For now, if it's still procedural HTML:
if (file_exists(__DIR__ . '/php/include/recentRelease.php')) {
    ob_start();
    include __DIR__ . '/php/include/recentRelease.php';
    $recent_release_content = ob_get_clean();
}

// $subcategory_content is no longer captured here, it's included in Twig template
// footer_content is no longer captured here, it's included in Twig template

// Prepare variables for Twig
$template_vars = [
    'BASE_URL' => BASE_URL,
    'WEBSITE_NAME' => WEBSITE_NAME,
    'request_uri' => $_SERVER['REQUEST_URI'],
    'fetchDetails' => $fetchDetails,
    'fetchdetailss' => $fetchdetailss,
    // 'popup_content' => $popup_content, // Removed
    'recent_release_content' => $recent_release_content,
    // 'subcategory_content' => $subcategory_content, // Removed
    // 'footer_content' => $footer_content, // Removed
    // TODO: Add DISQUS_SHORTNAME from a config
    'DISQUS_SHORTNAME' => 'YOUR_DISQUS_SHORTNAME_HERE',
];

// Render the Twig template
try {
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('anime-details.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader in anime-details.php.");
        echo "Error: Could not initialize the templating engine.";
    }
} catch (\Twig\Error\Error $e) {
    error_log("Twig Error in anime-details.php: " . $e->getMessage());
    $paths_info = "Template paths could not be determined.";
    if (isset($twig) && $twig->getLoader() instanceof \Twig\Loader\FilesystemLoader) {
      $paths_info = "Template lookup paths: " . implode(", ", $twig->getLoader()->getPaths());
    }
    echo "Twig Error: " . $e->getMessage() . " - " . $paths_info;
}

?>
