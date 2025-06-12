<?php
// This script assumes that php/info.php (which includes config/config.php and vendor/autoload.php)
// has already been included by the parent script (e.g., anime-details.php, home.php, etc.)

$recentReleasesData = [];
$errorMessage = '';

try {
    $apiClient = \App\Utils\ConfigLoader::getApiClient();
    if ($apiClient) {
        // Fetch data using ApiClient - for now, it's placeholder data from ApiClient itself
        // We can introduce pagination here if needed, e.g. $apiClient->getRecentReleases($currentPage);
        $recentReleasesData = $apiClient->getRecentReleases();
    } else {
        $errorMessage = "Error: API client not available.";
        error_log("Failed to get ApiClient instance in recentRelease.php.");
    }
} catch (\Throwable $e) {
    $errorMessage = "Error: Could not fetch recent releases data.";
    error_log("Error fetching recent releases in recentRelease.php: " . $e->getMessage());
}

// Prepare variables for Twig
$template_vars = [
    'recentReleases' => $recentReleasesData,
    'errorMessage' => $errorMessage, // Pass error message to template if any
];

try {
    $twig = \App\Utils\ConfigLoader::getTwig();
    if ($twig) {
        echo $twig->render('parts/recent_release.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance in recentRelease.php.");
        // Echo error directly if Twig itself isn't available
        echo !empty($errorMessage) ? "<p>$errorMessage</p>" : "<p>Error: Could not load recent releases template.</p>";
    }
} catch (\Throwable $e) {
    error_log("Error rendering recent_release.html.twig: " . $e->getMessage());
    echo "<p>Error: Could not display recent releases due to a templating error.</p>";
}

?>