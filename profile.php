<?php
$page_title = "My Profile";
require 'header.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $location = $_POST['location'] ?? null;
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    
    // Handle profile picture upload
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profile_pics/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Delete old profile pic if exists
        if (!empty($_SESSION['profile_pic']) && file_exists($_SESSION['profile_pic'])) {
            unlink($_SESSION['profile_pic']);
        }
        
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
            $profile_pic = $destination;
        }
    }
    
    // Update user in database
    $update_fields = [];
    $params = [];
    
    if ($full_name !== null) {
        $update_fields[] = 'full_name = ?';
        $params[] = $full_name;
        $_SESSION['full_name'] = $full_name;
    }
    
    if ($phone !== null) {
        $update_fields[] = 'phone = ?';
        $params[] = $phone;
    }
    
    if ($location !== null) {
        $update_fields[] = 'location = ?';
        $params[] = $location;
    }
    
    if ($username !== null) {
        $update_fields[] = 'username = ?';
        $params[] = $username;
        $_SESSION['username'] = $username;
    }
    
    if ($email !== null) {
        $update_fields[] = 'email = ?';
        $params[] = $email;
        $_SESSION['email'] = $email;
    }
    
    if ($profile_pic !== null) {
        $update_fields[] = 'profile_pic = ?';
        $params[] = $profile_pic;
        $_SESSION['profile_pic'] = $profile_pic;
    }
    
    if (!empty($update_fields)) {
        $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
        $params[] = $_SESSION['user_id'];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $_SESSION['success'] = "Profile updated successfully!";
    }
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: logout.php");
    exit();
}
?>

<div class="dashboard">
    <?php if (isSeller()): ?>
        <div class="sidebar">
            <div class="sidebar-title">Seller Menu</div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="seller_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="add_car.php"><i class="fas fa-plus-circle"></i> Add New Car</a></li>
                    <li><a href="my_cars.php"><i class="fas fa-car"></i> My Cars</a></li>
                    <li><a href="profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
                </ul>
            </div>
        </div>
    <?php elseif (isBuyer()): ?>
        <div class="sidebar">
            <div class="sidebar-title">Buyer Menu</div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="buyer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="search.php"><i class="fas fa-search"></i> Browse Cars</a></li>
                    <li><a href="favorites.php"><i class="fas fa-heart"></i> Favorites</a></li>
                    <li><a href="profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
                </ul>
            </div>
        </div>
    <?php elseif (isAdmin()): ?>
        <div class="sidebar">
            <div class="sidebar-title">Admin Menu</div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="verify_sellers.php"><i class="fas fa-user-check"></i> Seller Verifications</a></li>
                    <li><a href="manage_cars.php"><i class="fas fa-car"></i> Manage Cars</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a href="profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h2>My Profile</h2>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Profile Picture</label>
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <img src="<?php echo $user['profile_pic'] ?: 'images/default-profile.jpg'; ?>" alt="Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                    <div class="file-upload">
                        <p>Change profile picture</p>
                        <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                        <div class="file-name" id="profile-pic-name">No file chosen</div>
                    </div>
                </div>
            </div>
            
            <?php if (isBuyer()): ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <?php if (!isBuyer()): ?>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location" class="form-control" value="<?php echo htmlspecialchars($user['location']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="change_password.php" class="btn btn-outline">Change Password</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('profile_pic').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
    document.getElementById('profile-pic-name').textContent = fileName;
});
</script>

<?php require 'footer.php'; ?>
