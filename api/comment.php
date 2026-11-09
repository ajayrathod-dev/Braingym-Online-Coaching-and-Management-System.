<?php
session_start();
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id'], $_POST['gallery_id'], $_POST['comment'])) {
    http_response_code(400);
    echo "Missing parameters";
    exit;
}

$user_id = $_SESSION['user_id'];
$gallery_id = intval($_POST['gallery_id']);
$comment = trim($_POST['comment']);

if ($comment === '') {
    http_response_code(400);
    echo "Comment cannot be empty";
    exit;
}

$stmt = $conn->prepare("INSERT INTO gallery_comments (user_id, gallery_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $gallery_id, $comment);
$stmt->execute();

$username = $_SESSION['username'] ?? "User";

echo json_encode(["user" => $username, "comment" => htmlspecialchars($comment)]);
