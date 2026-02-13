<?php
// upload.php - handles image uploads
header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if file was sent
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['image'];

// Validate file type (allow only images)
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Only JPG, PNG, GIF, WEBP images are allowed']);
    exit;
}

// Generate a unique filename to avoid overwriting
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_', true) . '.' . $extension;
$destination = __DIR__ . '/images/' . $filename;

// Move the uploaded file to the images folder
if (move_uploaded_file($file['tmp_name'], $destination)) {
    // Return the relative path that can be stored in localStorage
    echo json_encode([
        'success' => true,
        'file_path' => 'images/' . $filename
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
}