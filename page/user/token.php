<?php
require_once '../../connect.php';
require_once '../../_base.php';

// ----------------------------------------------------------------------------

// TODO: (1) Delete expired tokens
$_db->query('DELETE FROM token WHERE expire < NOW()'); // throw all the expire token

$id = req('id');

// TODO: (2) Is token id valid?
if (!is_exists($id, 'token', 'id')) {
    temp('info', 'Invalid token. Try again');
    redirect('/');
}

// Get user's current password for validation
$stm = $_db->prepare('SELECT password FROM user WHERE user_id = (SELECT user_id FROM token WHERE id = ?)');
$stm->execute([$id]);
$current_password_hash = $stm->fetchColumn();

if (is_post()) {
    $password = req('password');
    $confirm  = req('confirm');

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'New password is required';
    }
    else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Password must be between 5-100 characters';
    }
    else if (SHA1($password) === $current_password_hash) {
        $_err['password'] = 'New password must be different from your current password';
    }

    // Validate: confirm
    if ($confirm == '') {
        $_err['confirm'] = 'Please confirm your password';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Confirmation must be between 5-100 characters';
    }
    else if ($confirm != $password) {
        $_err['confirm'] = 'Passwords do not match';
    }

    // DB operation
    if (!$_err) {
        // Update password
        $stm = $_db->prepare('UPDATE user SET password = SHA1(?) WHERE user_id = (SELECT user_id FROM token WHERE id = ?)');
        $stm->execute([$password, $id]);

        // Delete token
        $stm = $_db->prepare('DELETE FROM token WHERE id = ?');
        $stm->execute([$id]);

        temp('info', 'Password updated successfully');
        redirect('./login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
?>

<link rel="stylesheet" href="../../css/token-modern.css">

<!-- Hide old styles -->
<style>
header h1 { display: none !important; }
body form { display: none !important; }
header nav .menu { display: none !important; }

/* Force form elements to be visible */
.modern-form { display: block !important; visibility: visible !important; opacity: 1 !important; }
.form-group { display: block !important; visibility: visible !important; opacity: 1 !important; margin-bottom: 20px !important; }
.form-group label { display: block !important; visibility: visible !important; opacity: 1 !important; margin-bottom: 8px !important; font-weight: bold !important; color: #333 !important; }
.modern-input { display: block !important; visibility: visible !important; opacity: 1 !important; }
.submit-btn { display: block !important; visibility: visible !important; opacity: 1 !important; }
.error-message { display: block !important; visibility: visible !important; opacity: 1 !important; }

/* Override any hiding animations */
* { animation: none !important; }
</style>

<!-- Modern Token Reset Page -->
<div class="modern-token-page">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="./login.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Login
                </a>
            </div>
            <div class="hero-text">
                <div class="security-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <h1>Reset Your Password</h1>
                <p>Create a new secure password for your account</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Password Reset Form -->
            <div class="reset-form-card">
                <div class="form-header">
                    <h2>Set New Password</h2>
                    <p>Please enter your new password below</p>
                </div>

                <form method="post" class="modern-form">
                    <!-- New Password Field -->
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <input type="password" id="password" name="password" class="modern-input" placeholder="Enter your new password" required style="display: block !important; visibility: visible !important; opacity: 1 !important; width: 100% !important; padding: 15px !important; border: 2px solid #ddd !important; border-radius: 8px !important; font-size: 16px !important; background: white !important;">
                        <?php if (isset($_err['password'])): ?>
                            <div class="error-message" style="color: red; margin-top: 5px;"><?= $_err['password'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label for="confirm">Confirm Password:</label>
                        <input type="password" id="confirm" name="confirm" class="modern-input" placeholder="Confirm your new password" required style="display: block !important; visibility: visible !important; opacity: 1 !important; width: 100% !important; padding: 15px !important; border: 2px solid #ddd !important; border-radius: 8px !important; font-size: 16px !important; background: white !important;">
                        <?php if (isset($_err['confirm'])): ?>
                            <div class="error-message" style="color: red; margin-top: 5px;"><?= $_err['confirm'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" style="display: block !important; visibility: visible !important; opacity: 1 !important; width: 100% !important; padding: 15px !important; background: linear-gradient(135deg, #e74c3c 0%, #f39c12 100%) !important; color: white !important; border: none !important; border-radius: 8px !important; font-size: 16px !important; font-weight: bold !important; cursor: pointer !important; margin-top: 20px !important;">
                        Update Password
                    </button>
                </form>
            </div>

            <!-- Security Tips -->
            <div class="security-tips">
                <h3>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    Password Security Tips
                </h3>
                <div class="tips-grid">
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Use at least 5 characters (8+ recommended)</span>
                    </div>
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Include uppercase and lowercase letters</span>
                    </div>
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Add numbers and special characters</span>
                    </div>
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Must be different from your current password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple password validation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Token page loaded');
    
    // Make sure form elements are visible
    const form = document.querySelector('.modern-form');
    const inputs = document.querySelectorAll('.modern-input');
    const button = document.querySelector('.submit-btn');
    
    if (form) {
        form.style.display = 'block';
        form.style.visibility = 'visible';
        form.style.opacity = '1';
        console.log('Form found and made visible');
    }
    
    inputs.forEach(function(input) {
        input.style.display = 'block';
        input.style.visibility = 'visible';
        input.style.opacity = '1';
        console.log('Input made visible:', input.name);
    });
    
    if (button) {
        button.style.display = 'block';
        button.style.visibility = 'visible';
        button.style.opacity = '1';
        console.log('Button made visible');
    }
});
</script>


