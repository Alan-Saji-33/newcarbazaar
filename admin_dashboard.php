<?php
$page_title = "Admin Dashboard";
require 'header.php';
redirectIfNotAdmin();

// Get pending verifications
$stmt = $pdo->prepare("SELECT sv.*, u.full_name, u.email, u.profile_pic 
                      FROM seller_verifications sv
                      JOIN users u ON sv.seller_id = u.id
                      WHERE sv.status = 'pending'
                      ORDER BY sv.submitted_at DESC");
$stmt->execute();
$pending_verifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent cars
$recent_cars = getRecentCars(5);

// Count stats
$stmt = $pdo->query("SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'buyer' THEN 1 ELSE 0 END) as buyers,
                    SUM(CASE WHEN role = 'seller' THEN 1 ELSE 0 END) as sellers,
                    (SELECT COUNT(*) FROM cars) as total_cars,
                    (SELECT COUNT(*) FROM cars WHERE is_sold = 1) as sold_cars
                    FROM users");
$stats = $stmt->fetch();
?>

<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-title">Admin Menu</div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="verify_sellers.php"><i class="fas fa-user-check"></i> Seller Verifications</a></li>
                <li><a href="manage_cars.php"><i class="fas fa-car"></i> Manage Cars</a></li>
                <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h2>Admin Dashboard</h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?php echo $stats['total_users']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Buyers</h3>
                <p><?php echo $stats['buyers']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Sellers</h3>
                <p><?php echo $stats['sellers']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Cars</h3>
                <p><?php echo $stats['total_cars']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Cars Sold</h3>
                <p><?php echo $stats['sold_cars']; ?></p>
            </div>
        </div>
        
        <div class="section-title">
            <h3>Pending Seller Verifications</h3>
            <a href="verify_sellers.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        
        <?php if (empty($pending_verifications)): ?>
            <div class="alert alert-info">
                No pending verifications at this time.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Email</th>
                            <th>Submitted On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_verifications as $verification): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <?php if ($verification['profile_pic']): ?>
                                            <img src="<?php echo $verification['profile_pic']; ?>" alt="<?php echo htmlspecialchars($verification['full_name']); ?>" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div><?php echo htmlspecialchars($verification['full_name']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($verification['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($verification['submitted_at'])); ?></td>
                                <td>
                                    <a href="view_verification.php?id=<?php echo $verification['id']; ?>" class="btn btn-outline btn-sm">Review</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="section-title" style="margin-top: 40px;">
            <h3>Recently Listed Cars</h3>
            <a href="manage_cars.php" class="btn btn-outline btn-sm">Manage Cars</a>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Car</th>
                        <th>Seller</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_cars as $car): 
                        $seller_stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                        $seller_stmt->execute([$car['seller_id']]);
                        $seller = $seller_stmt->fetch();
                    ?>
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
                            <td><?php echo htmlspecialchars($seller['full_name']); ?></td>
                            <td>₹<?php echo number_format($car['price'], 0); ?></td>
                            <td>
                                <?php if ($car['is_sold']): ?>
                                    <span class="status-badge status-rejected">Sold</span>
                                <?php else: ?>
                                    <span class="status-badge status-approved">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="car_details.php?id=<?php echo $car['id']; ?>" class="btn btn-outline btn-sm">View</a>
                                <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                                <a href="delete_car.php?id=<?php echo $car['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
