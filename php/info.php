<?php

// Load the centralized configuration
require_once __DIR__ . '/../config/config.php';

// The variables like $base_url, $website_name, $apiLink, $aapxy_url, $aapxyed_api_url
// are now available as constants: BASE_URL, WEBSITE_NAME, API_LINK, AAPXY_URL, AAPXYED_API_URL.
// Any files that included info.php and used these variables will need to be updated
// to use the constant names instead.

// For convenience during transition, we can redefine them as global variables here,
// but the goal is to eventually phase out the use of these global variables.
global $base_url, $website_name, $apiLink, $aapxy_url, $aapxyed_api_url;

$base_url = BASE_URL;
$website_name = WEBSITE_NAME;
$apiLink = API_LINK;
$aapxy_url = AAPXY_URL;
$aapxyed_api_url = AAPXYED_API_URL;

?>
