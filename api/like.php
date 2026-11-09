<?php
session_start();
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['gallery_id'])) {
    http_response_code(400);
    echo "Missing parameters";
    exit;
}

$user_id = $_SESSION['user_id'];
$gallery_id = intval($_POST['gallery_id']);

$check = $conn->prepare("SELECT * FROM gallery_likes WHERE user_id = ? AND gallery_id = ?");
$check->bind_param("ii", $user_id, $gallery_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM gallery_likes WHERE user_id = ? AND gallery_id = ?");
    $stmt->bind_param("ii", $user_id, $gallery_id);
    $stmt->execute();
    $action = "unliked";
} else {
    $stmt = $conn->prepare("INSERT INTO gallery_likes (user_id, gallery_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $gallery_id);
    $stmt->execute();
    $action = "liked";
}

$count = $conn->query("SELECT COUNT(*) AS total FROM gallery_likes WHERE gallery_id = $gallery_id")->fetch_assoc()['total'];

echo json_encode(["action" => $action, "likes" => $count]);
