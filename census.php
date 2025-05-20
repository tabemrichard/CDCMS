<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Census Data</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Census Data</h1>

    <?php
    // Function to fetch data from the API
    function fetchCensusData($url) {
        $ch = curl_init();
        
        // Set the URL and options for the request
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if(curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close the cURL session
        curl_close($ch);

        // Return the response as an array
        return json_decode($response, true);
    }

    // API URL
    $apiUrl = 'https://backend-api-5m5k.onrender.com/api/cencus';

    // Fetch census data
    $censusData = fetchCensusData($apiUrl);

    // Check if data was successfully fetched
    if ($censusData && isset($censusData['data'])) {
        foreach ($censusData['data'] as $item) {
            // Display each item in a card
            echo '<div class="bg-white shadow-md rounded-lg p-6 mb-6">';
            echo '<h2 class="text-xl font-semibold mb-2">' . htmlspecialchars($item['firstname']) . '</h2>';
            echo '<p class="text-gray-700">Population: ' . htmlspecialchars($item['middlename']) . '</p>';
            echo '<p class="text-gray-700">Area: ' . htmlspecialchars($item['lastname']) . '</p>';
            echo '</div>';
        }
    }