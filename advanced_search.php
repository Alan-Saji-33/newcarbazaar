<?php
$page_title = "Advanced Search";
require 'header.php';

// Get search filters from GET parameters
$make = $_GET['make'] ?? '';
$model = $_GET['model'] ?? '';
$min_year = $_GET['min_year'] ?? '';
$max_year = $_GET['max_year'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$fuel_type = $_GET['fuel_type'] ?? '';
$transmission = $_GET['transmission'] ?? '';
$location = $_GET['location'] ?? '';

// Build SQL query based on filters
$sql = "SELECT c.*, ci.image_path 
       FROM cars c 
       LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
       WHERE c.is_sold = 0";
$params = [];

if (!empty($make)) {
    $sql .= " AND c.make LIKE ?";
    $params[] = "%$make%";
}

if (!empty($model)) {
    $sql .= " AND c.model LIKE ?";
    $params[] = "%$model%";
}

if (!empty($min_year)) {
    $sql .= " AND c.year >= ?";
    $params[] = $min_year;
}

if (!empty($max_year)) {
    $sql .= " AND c.year <= ?";
    $params[] = $max_year;
}

if (!empty($min_price)) {
    $sql .= " AND c.price >= ?";
    $params[] = $min_price;
}

if (!empty($max_price)) {
    $sql .= " AND c.price <= ?";
    $params[] = $max_price;
}

if (!empty($fuel_type)) {
    $sql .= " AND c.fuel_type = ?";
    $params[] = $fuel_type;
}

if (!empty($transmission)) {
    $sql .= " AND c.transmission = ?";
    $params[] = $transmission;
}

if (!empty($location)) {
    $sql .= " AND c.location LIKE ?";
    $params[] = "%$location%";
}

$sql .= " ORDER BY c.created_at DESC";

// Execute query if there are any filters
$results = [];
if (!empty($params)) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
    <div class="search-header">
        <h2>Advanced Car Search</h2>
    </div>
    
    <div class="advanced-search-form">
        <form action="advanced_search.php" method="get">
            <div class="form-row">
                <div class="form-group">
                    <label for="make">Make</label>
                    <input type="text" name="make" id="make" class="form-control" value="<?php echo htmlspecialchars($make); ?>">
                </div>
                
                <div class="form-group">
                    <label for="model">Model</label>
                    <input type="text" name="model" id="model" class="form-control" value="<?php echo htmlspecialchars($model); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="min_year">Min Year</label>
                    <select name="min_year" id="min_year" class="form-control">
                        <option value="">Any</option>
                        <?php for ($y = date('Y'); $y >= 1990; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $min_year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="max_year">Max Year</label>
                    <select name="max_year" id="max_year" class="form-control">
                        <option value="">Any</option>
                        <?php for ($y = date('Y'); $y >= 1990; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $max_year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="min_price">Min Price (₹)</label>
                    <input type="number" name="min_price" id="min_price" class="form-control" value="<?php echo htmlspecialchars($min_price); ?>" min="0">
                </div>
                
                <div class="form-group">
                    <label for="max_price">Max Price (₹)</label>
                    <input type="number" name="max_price" id="max_price" class="form-control" value="<?php echo htmlspecialchars($max_price); ?>" min="0">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fuel_type">Fuel Type</label>
                    <select name="fuel_type" id="fuel_type" class="form-control">
                        <option value="">Any</option>
                        <option value="Petrol" <?php echo $fuel_type == 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                        <option value="Diesel" <?php echo $fuel_type == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                        <option value="CNG" <?php echo $fuel_type == 'CNG' ? 'selected' : ''; ?>>CNG</option>
                        <option value="Electric" <?php echo $fuel_type == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                        <option value="Hybrid" <?php echo $fuel_type == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="transmission">Transmission</label>
                    <select name="transmission" id="transmission" class="form-control">
                        <option value="">Any</option>
                        <option value="Manual" <?php echo $transmission == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                        <option value="Automatic" <?php echo $transmission == 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="<?php echo htmlspecialchars($location); ?>">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="advanced_search.php" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>
    
    <?php if (!empty($params)): ?>
        <div class="search-results">
            <h3>Search Results (<?php echo count($results); ?> cars found)</h3>
            
            <?php if (empty($results)): ?>
                <div class="alert alert-info">
                    No cars found matching your search criteria. Try adjusting your filters.
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
    <?php endif; ?>
</div>

<style>
.advanced-search-form {
    background-color: var(--card-bg);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.form-row .form-group {
    flex: 1;
}

.search-results {
    margin-top: 30px;
}
</style>

<?php require 'footer.php'; ?>
