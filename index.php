<?php 
$page_title = "Home";
require 'header.php'; 
?>

<section class="hero">
    <div class="container">
        <h1>Find Your Perfect Used Car</h1>
        <p>Browse thousands of quality used cars from trusted sellers across the country</p>
        <form action="search.php" method="get" class="search-bar">
            <input type="text" name="query" placeholder="Search for make, model or keyword...">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
    </div>
</section>

<section class="recent-cars">
    <div class="container">
        <div class="section-title">
            <h2>Recently Listed Cars</h2>
            <p>Check out the latest additions to our inventory</p>
        </div>
        
        <div class="cars-grid">
            <?php 
            $recent_cars = getRecentCars();
            if (empty($recent_cars)): ?>
                <p>No cars listed yet. Check back later!</p>
            <?php else: 
                foreach ($recent_cars as $car): ?>
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
                <?php endforeach; 
            endif; ?>
        </div>
        
        <div class="text-center">
            <a href="search.php" class="btn btn-primary">View All Cars</a>
        </div>
    </div>
</section>

<?php require 'footer.php'; ?>
