<?php
require 'header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$car_id = $_GET['id'];
$car = getCarDetails($car_id);

if (!$car) {
    header("Location: index.php");
    exit();
}

$page_title = $car['make'] . ' ' . $car['model'];

// Check if car is favorited by current user
$is_favorite = false;
if (isLoggedIn() && isBuyer()) {
    $is_favorite = isFavorite($_SESSION['user_id'], $car_id);
}

// Get seller's other cars
$stmt = $pdo->prepare("SELECT COUNT(*) as total_cars FROM cars WHERE seller_id = ? AND id != ? AND is_sold = 0");
$stmt->execute([$car['seller_id'], $car_id]);
$seller_stats = $stmt->fetch();
?>

<div class="container">
    <div class="car-details">
        <div class="car-gallery">
            <?php if (!empty($car['images'])): ?>
                <div class="main-image">
                    <img src="<?php echo $car['images'][0]['image_path']; ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                </div>
                
                <div class="thumbnail-container">
                    <?php foreach ($car['images'] as $image): ?>
                        <div class="thumbnail">
                            <img src="<?php echo $image['image_path']; ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="main-image">
                    <img src="images/default-car.jpg" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                </div>
            <?php endif; ?>
        </div>
        
        <div class="car-info-section">
            <h1><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h1>
            
            <div class="car-meta-large">
                <span><i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['km_driven']); ?> km</span>
                <span><i class="fas fa-calendar-alt"></i> <?php echo $car['year']; ?></span>
                <span><i class="fas fa-gas-pump"></i> <?php echo $car['fuel_type']; ?></span>
                <span><i class="fas fa-cog"></i> <?php echo $car['transmission']; ?></span>
                <span><i class="fas fa-user"></i> <?php echo $car['ownership']; ?> Owner</span>
            </div>
            
            <div class="car-price-large">₹<?php echo number_format($car['price'], 0); ?></div>
            
            <?php if (isLoggedIn() && isBuyer()): ?>
                <div style="margin-bottom: 20px;">
                    <a href="#" class="btn btn-outline favorite-btn <?php echo $is_favorite ? 'active' : ''; ?>" data-car-id="<?php echo $car_id; ?>">
                        <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart"></i> 
                        <?php echo $is_favorite ? 'Remove from Favorites' : 'Add to Favorites'; ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="car-overview">
                <h2>Car Overview</h2>
                <div class="overview-grid">
                    <div class="overview-item">
                        <span>Make & Model</span>
                        <span><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></span>
                    </div>
                    <div class="overview-item">
                        <span>Registration Year</span>
                        <span><?php echo $car['year']; ?></span>
                    </div>
                    <div class="overview-item">
                        <span>Fuel Type</span>
                        <span><?php echo $car['fuel_type']; ?></span>
                    </div>
                    <div class="overview-item">
                        <span>Kilometers Driven</span>
                        <span><?php echo number_format($car['km_driven']); ?> km</span>
                    </div>
                    <div class="overview-item">
                        <span>Ownership</span>
                        <span><?php echo $car['ownership']; ?> Owner</span>
                    </div>
                    <div class="overview-item">
                        <span>Transmission</span>
                        <span><?php echo $car['transmission']; ?></span>
                    </div>
                    <div class="overview-item">
                        <span>Insurance</span>
                        <span><?php echo $car['insurance'] ?: 'N/A'; ?></span>
                    </div>
                    <div class="overview-item">
                        <span>Seats</span>
                        <span><?php echo $car['seats'] ?: 'N/A'; ?></span>
                    </div>
                    <div class="overview-item">
                        <span>Registration Number</span>
                        <span><?php echo htmlspecialchars($car['registration']); ?></span>
                    </div>
                    <div class="overview-item">
                        <span>Location</span>
                        <span><?php echo htmlspecialchars($car['location']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($car['description']): ?>
                <div class="car-overview">
                    <h2>Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="seller-card">
                <div class="seller-header">
                    <div class="seller-avatar">
                        <img src="<?php echo $car['profile_pic'] ?: 'images/default-profile.jpg'; ?>" alt="<?php echo htmlspecialchars($car['full_name']); ?>">
                    </div>
                    <div class="seller-info">
                        <h3><?php echo htmlspecialchars($car['full_name']); ?></h3>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($car['seller_location']); ?></p>
                    </div>
                </div>
                
                <div class="seller-stats">
                    <div class="seller-stat">
                        <h4>Listed Cars</h4>
                        <p><?php echo $seller_stats['total_cars']; ?></p>
                    </div>
                </div>
                
                <div class="seller-actions">
                    <a href="tel:<?php echo htmlspecialchars($car['phone']); ?>" class="btn btn-primary"><i class="fas fa-phone"></i> Call Seller</a>
                    <button class="btn btn-outline" data-modal-target="#sellerModal"><i class="fas fa-info-circle"></i> View Seller Details</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seller Modal -->
<div class="modal" id="sellerModal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h3 class="modal-title">Seller Information</h3>
        
        <div style="display: flex; margin-bottom: 20px;">
            <div style="margin-right: 20px;">
                <img src="<?php echo $car['profile_pic'] ?: 'images/default-profile.jpg'; ?>" alt="<?php echo htmlspecialchars($car['full_name']); ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
            </div>
            <div>
                <h4><?php echo htmlspecialchars($car['full_name']); ?></h4>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($car['seller_location']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($car['phone']); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($car['email']); ?></p>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <h4>Other Cars Listed by This Seller</h4>
            <?php if ($seller_stats['total_cars'] > 0): ?>
                <p>This seller has <?php echo $seller_stats['total_cars']; ?> other cars listed for sale.</p>
                <a href="search.php?seller=<?php echo $car['seller_id']; ?>" class="btn btn-outline">View All Cars</a>
            <?php else: ?>
                <p>This seller has no other cars listed at this time.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
