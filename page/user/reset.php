<?php
require_once '../../connect.php';
require_once '../../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_exists($email, 'user', 'email')) {
        $_err['email'] = 'Not exists';
    }

    // Send reset token (if valid)
    if (!$_err) {
        // TODO: (1) Select user
        $stm = $_db->prepare('SELECT * FROM user WHERE email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();

        // TODO: (2) Generate token id
        $id = sha1(uniqid() . rand());

        // Delete old token
        $stm = $_db->prepare('DELETE FROM token WHERE user_id = ?');
        $stm->execute([$u->user_id]);

        // Insert new token
        $stm = $_db->prepare('INSERT INTO token (id, expire, user_id) VALUES (?, ADDTIME(NOW(), "00:05"), ?)');
        $stm->execute([$id, $u->user_id]);


        // TODO: (4) Generate token url
        $url = base("page/user/token.php?id=$id");

        // TODO: (5) Send email
        $m = new stdClass();
        $m = get_mail();
        $m->addAddress($u->email, $u->username);
        
        // Check if photo exists before embedding
        $photoPath = "../../photos/$u->photo";
        $photoExists = file_exists($photoPath) && !empty($u->photo);
        
        if ($photoExists) {
            $m->addEmbeddedImage($photoPath, 'photo');
            $photoHtml = "<img src='cid:photo' style='width: 200px; height: 200px; border: 1px solid #333; border-radius: 10px; margin-bottom: 20px;'>";
        } else {
            // Use a default placeholder or no image
            $photoHtml = "<div style='width: 200px; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; margin-bottom: 20px;'>👤</div>";
        }
        
        $m->isHTML(true);
        $m->Subject = "Reset password";
        $m->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    $photoHtml
                </div>
                <p style='font-size: 16px; color: #333;'>Dear $u->username,</p>
                <h1 style='color: #667eea; text-align: center; margin: 30px 0;'>Request for Reset Password</h1>
                <p style='font-size: 16px; color: #555; line-height: 1.6;'>
                    Your password can be reset through the link below.
                </p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$url' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>Reset Password</a>
                </div>
                <p style='font-size: 16px; color: #555; line-height: 1.6;'>
                    Please click on the button above to reset your password as soon as possible.
                </p>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0; border-left: 4px solid #ffc107;'>
                    <h3 style='color: #856404; margin-top: 0;'>Important Security Notice</h3>
                    <p style='color: #856404; margin-bottom: 0;'>If you DID NOT request for password reset, please IGNORE this message. Your password will REMAIN UNCHANGED.</p>
                </div>
                <p style='font-size: 14px; color: #888; text-align: center; margin-top: 40px;'>
                    From, CTRL + EAT Admin<br>
                    <small>This link will expire in 5 minutes for security purposes.</small>
                </p>
            </div>
        ";
        $m->send();

        temp('info', 'Email sent');
        redirect("/index.php?user_id={$u->user_id}");
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
?>

<link rel="stylesheet" href="/css/reset-modern.css">

<!-- Hero Section -->
<section class="reset-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="back-navigation">
                <button onclick="window.history.back()" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    <span>Back</span>
                </button>
            </div>
            <h1 class="hero-title">
                <span class="brand-highlight">Password Reset</span>
            </h1>
            <p class="hero-subtitle">Reset your account password securely</p>
        </div>
    </div>
</section>

<!-- Reset Section -->
<section class="reset-section">
    <div class="section-container">
        <div class="reset-content">
            <div class="reset-card">
                <div class="card-header">
                    <div class="reset-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C15.4,11.5 16,12.4 16,13V16C16,16.6 15.6,17 15,17H9C8.4,17 8,16.6 8,16V13C8,12.4 8.4,11.5 9,11.5V10C9,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.2,9.2 10.2,10V11.5H13.8V10C13.8,9.2 12.8,8.2 12,8.2Z"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Reset Your Password</h2>
                    <p class="section-description">
                        Enter your email address and we'll send you a secure link to reset your password.
                    </p>
                </div>
                
                <form method="post" class="reset-form">
                    <div class="form-group">
                        <label for="email" class="field-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,5.11 21.1,4 20,4Z"/>
                            </svg>
                            Email Address
                        </label>
                        <div class="input-wrapper">
                            <?= html_text('email', 'maxlength="100" placeholder="Enter your email address"') ?>
                            <?= err('email') ?>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,5.11 21.1,4 20,4Z"/>
                            </svg>
                            Send Reset Link
                        </button>
                    </div>
                </form>
                
                <div class="reset-info">
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                            </svg>
                        </div>
                        <p>The reset link will expire in 5 minutes for security purposes.</p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                            </svg>
                        </div>
                        <p>If you didn't request this reset, you can safely ignore this message.</p>
                    </div>
                </div>
                
                <div class="additional-actions">
                    <p class="help-text">Remember your password?</p>
                    <a href="login.php" class="login-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
                        </svg>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>