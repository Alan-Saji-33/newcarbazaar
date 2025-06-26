<?php
$page_title = "Search Cars";
require 'header.php';

$query = $_GET['query'] ?? '';
$results = [];

if (!empty($query)) {
    $search_term = "%$query%";
    $stmt = $pdo->prepare("SELECT c.*, ci.image_path 
                          FROM cars c 
                          LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
                          WHERE (c.make LIKE ? OR c.model LIKE ? OR c.description LIKE ?) 
                          AND c.is_sold = 0
                          ORDER BY c.created_at DESC");
    $stmt->execute([$search_term, $search_term, $search_term]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
    <div class="search-header">
        <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
        <a href="advanced_search.php" class="btn btn-outline">Advanced Search</a>
    </div>
    
    <?php if (empty($query)): ?>
        <div class="alert alert-info">
            Please enter a search term to find cars.
        </div>
    <?php elseif (empty($results)): ?>
        <div class="alert alert-info">
            No cars found matching your search. Try different keywords or <a href="advanced_search.php">use advanced search</a>.
        </div>
    <?php else: ?>
        <div class="cars-grid">
            <?php foreach ($results as $car): ?>
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

<?php require 'footer.php'; ?>
