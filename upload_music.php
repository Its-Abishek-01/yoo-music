<?php
ini_set('upload_max_filesize', '0'); // Unlimited upload size for chunks
ini_set('post_max_size', '0');      // Unlimited POST size for chunks
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Folder to save chunks and final files
$chunkDir = __DIR__ . '/chunks';
$targetDir = __DIR__ . '/music';

// Create directories if not exist
if (!is_dir($chunkDir)) mkdir($chunkDir, 0777, true);
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

// Handle POST upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chunkIndex = $_POST['chunk_index'];
    $totalChunks = $_POST['total_chunks'];
    $fileName = $_POST['file_name'];

    // Save current chunk to temporary folder
    $chunkPath = "$chunkDir/{$fileName}_chunk_{$chunkIndex}";
    if (move_uploaded_file($_FILES['file']['tmp_name'], $chunkPath)) {
        echo json_encode(['message' => "Chunk $chunkIndex uploaded successfully"]);
    } else {
        echo json_encode(['message' => "Error uploading chunk $chunkIndex"]);
        exit;
    }

    // Check if all chunks are uploaded
    if (allChunksUploaded($fileName, $totalChunks, $chunkDir)) {
        mergeChunks($fileName, $totalChunks, $chunkDir, $targetDir);
        echo json_encode(['success' => true, 'message' => 'Chunk uploaded successfully']);
    }
    exit;
}

// Function to check if all chunks are uploaded
function allChunksUploaded($fileName, $totalChunks, $chunkDir) {
    for ($i = 0; $i < $totalChunks; $i++) {
        if (!file_exists("$chunkDir/{$fileName}_chunk_$i")) {
            return false;
        }
    }
    return true;
}

// Function to merge all chunks into a single file
function mergeChunks($fileName, $totalChunks, $chunkDir, $targetDir) {
    $finalPath = "$targetDir/$fileName";
    $output = fopen($finalPath, 'w');

    for ($i = 0; $i < $totalChunks; $i++) {
        $chunkPath = "$chunkDir/{$fileName}_chunk_$i";
        $chunk = fopen($chunkPath, 'r');
        stream_copy_to_stream($chunk, $output);
        fclose($chunk);
        unlink($chunkPath); // Delete chunk after merging
    }

    fclose($output);
}
?>
