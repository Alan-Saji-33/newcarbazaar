<?php
$page_title = "Login";
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'db_connect.php';
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Special admin login
    if ($email === 'admin' && $password === 'admin123') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin'");
        $stmt->execute();
        $admin = $stmt->fetch();
        
        if (!$admin) {
            // Create admin user if not exists
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);
            $admin_id = $pdo->lastInsertId();
        } else {
            $admin_id = $admin['id'];
        }
        
        $_SESSION['user_id'] = $admin_id;
        $_SESSION['role'] = 'admin';
        $_SESSION['email'] = 'admin';
        
        header("Location: admin_dashboard.php");
        exit();
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        
        if ($user['role'] === 'buyer') {
            $_SESSION['username'] = $user['username'];
            header("Location: buyer_dashboard.php");
        } elseif ($user['role'] === 'seller') {
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: seller_dashboard.php");
        } elseif ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2>Login to Your Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
            
            <div class="text-center">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                <p><a href="forgot_password.php">Forgot your password?</a></p>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
