<?php
require 'db_connect.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return isLoggedIn() && getUserRole() === 'admin';
}

function isSeller() {
    return isLoggedIn() && getUserRole() === 'seller';
}

function isBuyer() {
    return isLoggedIn() && getUserRole() === 'buyer';
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function redirectIfNotAdmin() {
    redirectIfNotLoggedIn();
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

function redirectIfNotSeller() {
    redirectIfNotLoggedIn();
    if (!isSeller()) {
        header("Location: index.php");
        exit();
    }
}

function redirectIfNotBuyer() {
    redirectIfNotLoggedIn();
    if (!isBuyer()) {
        header("Location: index.php");
        exit();
    }
}

function getRecentCars($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT c.*, ci.image_path 
                          FROM cars c 
                          LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
                          WHERE c.is_sold = 0
                          ORDER BY c.created_at DESC 
                          LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCarDetails($car_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT c.*, u.full_name, u.phone, u.location as seller_location, u.profile_pic 
                          FROM cars c 
                          JOIN users u ON c.seller_id = u.id
                          WHERE c.id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($car) {
        $stmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ?");
        $stmt->execute([$car_id]);
        $car['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $car;
}

function isFavorite($user_id, $car_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND car_id = ?");
    $stmt->execute([$user_id, $car_id]);
    return $stmt->fetch() ? true : false;
}
?>s
