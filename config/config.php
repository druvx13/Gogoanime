<?php

define('BASE_URL', "//" . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
define('WEBSITE_NAME', 'GogoAnime');

// Define the new prxy URL
define('AAPXY_URL', "https://gogo.druvx13.workers.dev/?u=");

// Default API link
define('DEFAULT_API_LINK', "https://animeapi-9qlo.onrender.com");

// Determine API link based on the day
if (date("d") > 15) {
    define('API_LINK', DEFAULT_API_LINK); // Same API for the second condition for now
} else {
    define('API_LINK', DEFAULT_API_LINK);
}

// Apply the prxy to the API link (the URL needs to be encoded for the prxy to work correctly)
define('AAPXYED_API_URL', AAPXY_URL . urlencode(API_LINK));

// Twig Templating Engine Setup
require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    // 'cache' => __DIR__ . '/../cache/twig', // Optional: configure Twig cache
    'debug' => true // Optional: enable Twig debug mode
]);

// Make Twig environment globally available (simple approach for now)
global $twig;

?>
