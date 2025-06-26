<?php
require 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$car_id = $_POST['car_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$car_id || !in_array($action, ['add', 'remove'])) {
    header("HTTP/1.1 400 Bad Request");
    exit();
}

try {
    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, car_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $car_id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND car_id = ?");
        $stmt->execute([$_SESSION['user_id'], $car_id]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
