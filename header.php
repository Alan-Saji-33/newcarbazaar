<?php
session_start();
require 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarBazaar - <?php echo $page_title ?? 'Used Car Marketplace'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php">CarBazaar</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="search.php">Browse Cars</a></li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isSeller()): ?>
                            <li><a href="seller_dashboard.php">Seller Dashboard</a></li>
                        <?php elseif (isBuyer()): ?>
                            <li><a href="buyer_dashboard.php">Buyer Dashboard</a></li>
                        <?php endif; ?>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="signup.php" class="btn btn-primary">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <div class="mobile-menu">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="search.php">Browse Cars</a></li>
            <?php if (isLoggedIn()): ?>
                <?php if (isSeller()): ?>
                    <li><a href="seller_dashboard.php">Seller Dashboard</a></li>
                <?php elseif (isBuyer()): ?>
                    <li><a href="buyer_dashboard.php">Buyer Dashboard</a></li>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                    <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                <?php endif; ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </div>
