<?php 
$base_url = "//{$_SERVER['SERVER_NAME']}";
$website_name = "GogoAnime";

// Define your API link
$apiLink = "https://animeapi-9qlo.onrender.com"; // Add the API URL with a trailing slash

// Check the day and update the API link if needed
if (date("d") > 15) {
    $apiLink = "https://animeapi-9qlo.onrender.com"; // Same API for the second condition
}

// Define the new proxy URL
$proxy_url = "https://animedex-proxy.druvx13.workers.dev/?u="; // Your new proxy URL

// Apply the proxy to the API link (the URL needs to be encoded for the proxy to work correctly)
$proxied_api_url = $proxy_url . urlencode($apiLink);

echo "Proxied API URL: " . $proxied_api_url; // For debugging, print the final proxied URL
?>
