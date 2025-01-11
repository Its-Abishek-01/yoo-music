<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json"); // Set content type as JSON

// Define the music folder location
$musicFolder = __DIR__ . '/music';

// Check if the folder exists
if (!file_exists($musicFolder)) {
    mkdir($musicFolder, 0777, true);  // Create the folder if it doesn't exist
}

// Get the list of music files from the folder
$files = scandir($musicFolder);
$musicFiles = array_filter($files, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'mp3'; // Filter out non-mp3 files
});

// Return the music list as JSON
echo json_encode(array_values($musicFiles));
?>
