<?php
$page_title = "Seller Verification";
require 'header.php';
redirectIfNotSeller();

// Check if already verified
$stmt = $pdo->prepare("SELECT status FROM seller_verifications WHERE seller_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$verification = $stmt->fetch();

if ($verification && $verification['status'] == 'approved') {
    header("Location: seller_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Aadhar card upload
    if (isset($_FILES['aadhar_card']) && $_FILES['aadhar_card']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/verifications/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['aadhar_card']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['aadhar_card']['tmp_name'], $destination)) {
            // Insert verification request
            $stmt = $pdo->prepare("INSERT INTO seller_verifications (seller_id, aadhar_card) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $destination]);
            
            $_SESSION['success'] = "Verification submitted successfully! Our team will review your documents shortly.";
            header("Location: seller_dashboard.php");
            exit();
        } else {
            $error = "Failed to upload document. Please try again.";
        }
    } else {
        $error = "Please upload a valid Aadhar card document.";
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2>Seller Verification</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($verification && $verification['status'] == 'pending'): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Your verification request is pending. Our team will review your documents shortly.
            </div>
        <?php elseif ($verification && $verification['status'] == 'rejected'): ?>
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> Your previous verification request was rejected. Please submit new documents.
            </div>
        <?php endif; ?>
        
        <div class="verification-info">
            <p>To sell cars on CarBazaar, we need to verify your identity. Please upload a clear photo or scan of your Aadhar card.</p>
            <p><strong>Note:</strong> Your documents will be kept secure and only used for verification purposes.</p>
        </div>
        
        <form action="verify_seller.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Aadhar Card Document</label>
                <div class="file-upload">
                    <i class="fas fa-id-card"></i>
                    <p>Click to upload your Aadhar card (Front side)</p>
                    <input type="file" name="aadhar_card" id="aadhar_card" accept="image/*,.pdf" required>
                    <div class="file-name" id="aadhar-card-name">No file chosen</div>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Submit for Verification</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('aadhar_card').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
    document.getElementById('aadhar-card-name').textContent = fileName;
});
</script>

<?php require 'footer.php'; ?>
