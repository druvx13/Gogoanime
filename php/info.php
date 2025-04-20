<?php 
$base_url = "//{$_SERVER['SERVER_NAME']}";
$website_name = "GogoAnime";

// Define your API link
$apiLink = "https://animeapi-9qlo.onrender.com"; // Add the API URL with a trailing slash

// Check the day and update the API link if needed
if (date("d") > 15) {
    $apiLink = "https://animeapi-9qlo.onrender.com"; // Same API for the second condition
}

// Define the new prxy URL
$aapxy_url = "https://gogo.druvx13.workers.dev/?u="; // Your new prxy URL

// Apply the prxy to the API link (the URL needs to be encoded for the prxy to work correctly)
$aapxyed_api_url = $aapxy_url . urlencode($apiLink);

echo "Prxied API URL: " . $aapxyed_api_url; // For debugging, print the final prxied URL
?>
