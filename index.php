<?php
// Load configuration (which also initializes Twig and sets up global $twig)
require_once __DIR__ . '/php/info.php'; // This includes config/config.php

// Prepare variables for Twig
$template_vars = [
    'BASE_URL' => BASE_URL,
    'WEBSITE_NAME' => WEBSITE_NAME,
    // 'popup_content' is no longer needed here as it's directly included in the main Twig template
    // 'footer_content' is no longer needed here as it's directly included in the main Twig template
    // 'API_LINK' => API_LINK, // Example if needed
    // 'AAPXYED_API_URL' => AAPXYED_API_URL, // Example if needed
];

// Render the Twig template
try {
    $twigInstance = \App\Utils\ConfigLoader::getTwig();
    if ($twigInstance) {
        echo $twigInstance->render('index.html.twig', $template_vars);
    } else {
        error_log("Failed to get Twig instance from ConfigLoader.");
        echo "Error: Could not initialize the templating engine.";
    }
} catch (\Twig\Error\Error $e) { // Catching generic Twig error
    error_log("Twig Error in index.php: " . $e->getMessage());
    $paths_info = "Template paths could not be determined.";
    if (isset($twigInstance) && $twigInstance->getLoader() instanceof \Twig\Loader\FilesystemLoader) {
      $paths_info = "Template lookup paths: " . implode(", ", $twigInstance->getLoader()->getPaths());
    }
    echo "Twig Error: " . $e->getMessage() . " - " . $paths_info;
}

?>