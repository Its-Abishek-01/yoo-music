<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$musicFolder = __DIR__ . '/music';

// Get the filename from the query string (e.g., ?file=example.mp3)
$filename = isset($_GET['file']) ? $_GET['file'] : null;

if ($filename) {
    $filePath = $musicFolder . '/' . $filename;

    if (!file_exists($filePath)) {
        http_response_code(404);
        echo json_encode(['message' => 'File not found']);
        exit;
    }

    // Set headers for downloading the file
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));

    // Read the file and send it as a download
    readfile($filePath);
} else {
    http_response_code(400);
    echo json_encode(['message' => 'No file specified']);
}
?>
