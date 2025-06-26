<?php
$page_title = "Seller Dashboard";
require 'header.php';
redirectIfNotSeller();

// Get seller verification status
$stmt = $pdo->prepare("SELECT status FROM seller_verifications WHERE seller_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$verification = $stmt->fetch();

// Get seller's listed cars
$stmt = $pdo->prepare("SELECT c.*, ci.image_path 
                      FROM cars c 
                      LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
                      WHERE c.seller_id = ? 
                      ORDER BY c.created_at DESC 
                      LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$my_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count stats
$stmt = $pdo->prepare("SELECT 
                      COUNT(*) as total_cars,
                      SUM(CASE WHEN is_sold = 1 THEN 1 ELSE 0 END) as sold_cars
                      FROM cars 
                      WHERE seller_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$stats = $stmt->fetch();
?>

<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-title">Seller Menu</div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="seller_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="add_car.php"><i class="fas fa-plus-circle"></i> Add New Car</a></li>
                <li><a href="my_cars.php"><i class="fas fa-car"></i> My Cars</a></li>
                <?php if (!$verification || $verification['status'] != 'approved'): ?>
                    <li><a href="verify_seller.php"><i class="fas fa-check-circle"></i> Get Verified</a></li>
                <?php endif; ?>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h2>Seller Dashboard</h2>
            <div>
                <a href="add_car.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Car</a>
            </div>
        </div>
        
        <?php if ($verification): ?>
            <?php if ($verification['status'] == 'pending'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> Your verification is pending. You can list cars but they won't be visible to buyers until you're verified.
                </div>
            <?php elseif ($verification['status'] == 'rejected'): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Your verification was rejected. Please submit new documents for verification.
                    <a href="verify_seller.php" class="btn btn-outline btn-sm">Resubmit</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> You need to verify your account to sell cars. Unverified sellers can add cars but they won't be visible to buyers.
                <a href="verify_seller.php" class="btn btn-outline btn-sm">Get Verified</a>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Cars Listed</h3>
                <p><?php echo $stats['total_cars']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Cars Sold</h3>
                <p><?php echo $stats['sold_cars']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Listings</h3>
                <p><?php echo $stats['total_cars'] - $stats['sold_cars']; ?></p>
            </div>
        </div>
        
        <div class="section-title">
            <h3>Recently Added Cars</h3>
            <a href="my_cars.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        
        <?php if (empty($my_cars)): ?>
            <div class="alert alert-info">
                You haven't listed any cars yet. <a href="add_car.php">Add your first car</a> to get started.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Car</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($my_cars as $car): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <?php if ($car['image_path']): ?>
                                            <img src="<?php echo $car['image_path']; ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>" style="width: 60px; height: 40px; object-fit: cover; margin-right: 10px; border-radius: 4px;">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></strong><br>
                                            <small><?php echo $car['year']; ?> • <?php echo number_format($car['km_driven']); ?> km</small>
                                        </div>
                                    </div>
                                </td>
                                <td>₹<?php echo number_format($car['price'], 0); ?></td>
                                <td>
                                    <?php if ($car['is_sold']): ?>
                                        <span class="status-badge status-rejected">Sold</span>
                                    <?php elseif ($verification && $verification['status'] == 'approved'): ?>
                                        <span class="status-badge status-approved">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">Pending Verification</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="car_details.php?id=<?php echo $car['id']; ?>" class="btn btn-outline btn-sm">View</a>
                                    <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                                    <?php if (!$car['is_sold']): ?>
                                        <a href="mark_sold.php?id=<?php echo $car['id']; ?>" class="btn btn-danger btn-sm mark-sold-btn">Mark Sold</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'footer.php'; ?>
