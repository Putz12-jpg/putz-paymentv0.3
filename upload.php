<?php
header('Content-Type: application/json');

$targetDir = "uploads/";

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['buktiTransfer'])) {
        echo json_encode(['error' => 'File tidak ditemukan']);
        exit;
    }

    $file = $_FILES['buktiTransfer'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'Upload error: ' . $file['error']]);
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['error' => 'Format file tidak didukung. Hanya jpg, png, gif.']);
        exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid('bukti_', true) . '.' . $ext;

    $targetFile = $targetDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
                 . "://$_SERVER[HTTP_HOST]/";
        $fileURL = $baseURL . $targetFile;

        echo json_encode(['url' => $fileURL]);
        exit;
    } else {
        echo json_encode(['error' => 'Gagal menyimpan file']);
        exit;
    }
} else {
    echo json_encode(['error' => 'Metode request tidak valid']);
    exit;
}
