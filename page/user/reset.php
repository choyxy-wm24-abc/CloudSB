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
        $m->addEmbeddedImage("../../photos/$u->photo", 'photo');
        $m->isHTML(true);
        $m->Subject = "Reset password";
        $m->Body = "
            <img src='cid:photo'
                 style='width: 200px; height: 200px;
                        border: 1px solid #333'>
            <p>Dear $u->username,<p>
            <h1 style='color: red'>Request for Reset Password</h1>
            <p>
                Your password can be reset through the url below.
            </p>
            <p>
                <a href='$url'>$url</a>
            </p>
            <p>
                Please click on the url to reset your password asap.
            </p>
            <h2 style='font-style: italic; color: blue'>If you DID NOT request for password reset, 
            please IGNORE this message, your password will REMAIN UNCHANGE.</h2>
            <p>From, CTRL + EAT Admin</p>
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