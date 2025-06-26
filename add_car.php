<?php
$page_title = "Add New Car";
require 'header.php';
redirectIfNotSeller();

// Check if seller is verified
$stmt = $pdo->prepare("SELECT status FROM seller_verifications WHERE seller_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$verification = $stmt->fetch();

if (!$verification || $verification['status'] != 'approved') {
    echo '<script>
        alert("You need to verify your account to sell cars. You will be redirected to the verification page.");
        window.location.href = "verify_seller.php";
    </script>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $km_driven = $_POST['km_driven'];
    $fuel_type = $_POST['fuel_type'];
    $transmission = $_POST['transmission'];
    $ownership = $_POST['ownership'];
    $insurance = $_POST['insurance'];
    $seats = $_POST['seats'];
    $registration = $_POST['registration'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    
    try {
        $pdo->beginTransaction();
        
        // Insert car details
        $stmt = $pdo->prepare("INSERT INTO cars (seller_id, make, model, year, price, km_driven, fuel_type, transmission, ownership, insurance, seats, registration, location, description) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $make, $model, $year, $price, $km_driven, $fuel_type, $transmission, $ownership, $insurance, $seats, $registration, $location, $description]);
        
        $car_id = $pdo->lastInsertId();
        
        // Handle image uploads
        $upload_dir = 'uploads/car_images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $primary_set = false;
        foreach ($_FILES['car_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['car_images']['error'][$key] == UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['car_images']['name'][$key], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($tmp_name, $destination)) {
                    $is_primary = (!$primary_set) ? 1 : 0;
                    $primary_set = true;
                    
                    $stmt = $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?, ?, ?)");
                    $stmt->execute([$car_id, $destination, $is_primary]);
                }
            }
        }
        
        $pdo->commit();
        
        $_SESSION['success'] = "Car listed successfully!";
        header("Location: seller_dashboard.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error listing car: " . $e->getMessage();
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2>List Your Car for Sale</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="add_car.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="make">Make</label>
                <input type="text" name="make" id="make" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="year">Year</label>
                <select name="year" id="year" class="form-control" required>
                    <option value="">Select Year</option>
                    <?php for ($y = date('Y'); $y >= 1990; $y--): ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">Price (₹)</label>
                <input type="number" name="price" id="price
