<?php
session_start();
require_once __DIR__ . '/connection.php';

if (!isset($_SESSION['user_id'])) die(json_encode(['error' => 'Not logged in']));

$galleryId = $_GET['gallery_id'];
$userId = $_SESSION['user_id'];

// Check existing like
$exists = $conn->query("SELECT * FROM gallery_likes 
                       WHERE user_id = $userId AND gallery_id = $galleryId")->num_rows;

if ($exists) {
    $conn->query("DELETE FROM gallery_likes 
                 WHERE user_id = $userId AND gallery_id = $galleryId");
    $liked = false;
} else {
    $conn->query("INSERT INTO gallery_likes (user_id, gallery_id) 
                 VALUES ($userId, $galleryId)");
    $liked = true;
}

$newCount = $conn->query("SELECT COUNT(*) FROM gallery_likes 
                         WHERE gallery_id = $galleryId")->fetch_row()[0];

echo json_encode(['newCount' => $newCount, 'liked' => $liked]);