<?php
$page_title = "My Listed Cars";
require 'header.php';
redirectIfNotSeller();

// Get seller's cars
$stmt = $pdo->prepare("SELECT c.*, ci.image_path 
                      FROM cars c 
                      LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
                      WHERE c.seller_id = ? 
                      ORDER BY c.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$my_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check verification status
$stmt = $pdo->prepare("SELECT status FROM seller_verifications WHERE seller_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$verification = $stmt->fetch();
?>

<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-title">Seller Menu</div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="seller_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="add_car.php"><i class="fas fa-plus-circle"></i> Add New Car</a></li>
                <li><a href="my_cars.php" class="active"><i class="fas fa-car"></i> My Cars</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h2>My Listed Cars</h2>
            <div>
                <a href="add_car.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Car</a>
            </div>
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
