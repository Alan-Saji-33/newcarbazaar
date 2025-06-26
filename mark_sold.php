<?php
require 'header.php';
redirectIfNotSeller();

if (!isset($_GET['id'])) {
    header("Location: my_cars.php");
    exit();
}

$car_id = $_GET['id'];

// Verify that the car belongs to the current seller
$stmt = $pdo->prepare("SELECT id FROM cars WHERE id = ? AND seller_id = ?");
$stmt->execute([$car_id, $_SESSION['user_id']]);
$car = $stmt->fetch();

if (!$car) {
    header("Location: my_cars.php");
    exit();
}

// Mark car as sold
$stmt = $pdo->prepare("UPDATE cars SET is_sold = 1 WHERE id = ?");
$stmt->execute([$car_id]);

$_SESSION['success'] = "Car marked as sold successfully!";
header("Location: my_cars.php");
exit();
?>
