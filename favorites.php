<?php
$page_title = "Favorite Cars";
require 'header.php';
redirectIfNotBuyer();

// Get favorite cars
$stmt = $pdo->prepare("SELECT c.*, ci.image_path 
                      FROM favorites f
                      JOIN cars c ON f.car_id = c.id
                      LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
                      WHERE f.user_id = ? 
                      ORDER BY f.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-title">Buyer Menu</div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="buyer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="search.php"><i class="fas fa-search"></i> Browse Cars</a></li>
                <li><a href="favorites.php" class="active"><i class="fas fa-heart"></i> Favorites</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h2>Your Favorite Cars</h2>
        </div>
        
        <?php if (empty($favorites)): ?>
            <div class="alert alert-info">
                You haven't added any cars to your favorites yet. <a href="search.php">Browse cars</a> to find your perfect match.
            </div>
        <?php else: ?>
            <div class="cars-grid">
                <?php foreach ($favorites as $car): ?>
                    <div class="car-card">
                        <a href="car_details.php?id=<?php echo $car['id']; ?>">
                            <div class="car-img">
                                <img src="<?php echo $car['image_path'] ?: 'images/default-car.jpg'; ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                            </div>
                            <div class="car-info">
                                <h3><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h3>
                                <div class="car-meta">
                                    <span><i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['km_driven']); ?> km</span>
                                    <span><i class="fas fa-calendar-alt"></i> <?php echo $car['year']; ?></span>
                                    <span><i class="fas fa-cog"></i> <?php echo $car['transmission']; ?></span>
                                </div>
                                <div class="car-price">₹<?php echo number_format($car['price'], 0); ?></div>
                                <div class="car-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($car['location']); ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'footer.php'; ?>
