<?php
$page_title = "Sign Up";
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'db_connect.php';
    
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $error = "Email already exists. Please use a different email.";
    } else {
        if ($role == 'buyer') {
            $username = $_POST['username'];
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password, 'buyer']);
            
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = 'buyer';
            $_SESSION['username'] = $username;
            
            header("Location: buyer_dashboard.php");
            exit();
        } elseif ($role == 'seller') {
            $full_name = $_POST['full_name'];
            $phone = $_POST['phone'];
            $location = $_POST['location'];
            
            // Handle profile picture upload
            $profile_pic = null;
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/profile_pics/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
                    $profile_pic = $destination;
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role, full_name, phone, profile_pic, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $password, 'seller', $full_name, $phone, $profile_pic, $location]);
            
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = 'seller';
            $_SESSION['full_name'] = $full_name;
            
            header("Location: verify_seller.php");
            exit();
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2>Create Your Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="signup.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="role">I want to:</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="buyer">Buy Cars</option>
                    <option value="seller">Sell Cars</option>
                </select>
            </div>
            
            <div id="buyer-fields" style="display: none;">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control">
                </div>
            </div>
            
            <div id="seller-fields" style="display: none;">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Profile Picture</label>
                    <div class="file-upload">
                        <i class="fas fa-user-circle"></i>
                        <p>Click to upload profile picture</p>
                        <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                        <div class="file-name" id="profile-pic-name">No file chosen</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
            </div>
            
            <div class="text-center">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    document.getElementById('buyer-fields').style.display = role === 'buyer' ? 'block' : 'none';
    document.getElementById('seller-fields').style.display = role === 'seller' ? 'block' : 'none';
});

document.getElementById('profile_pic').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
    document.getElementById('profile-pic-name').textContent = fileName;
});
</script>

<?php require 'footer.php'; ?>
