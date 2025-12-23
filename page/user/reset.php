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
        $m->Subject = "🔐 Reset Your Password - CTRL + EAT";
        $m->Body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reset Your Password</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    line-height: 1.6;
                    color: #1a1a1a;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    margin: 0;
                    padding: 40px 20px;
                }
                
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 24px;
                    overflow: hidden;
                    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
                }
                
                .email-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 50px 40px;
                    text-align: center;
                    position: relative;
                    overflow: hidden;
                }
                
                .email-header::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"25\" cy=\"25\" r=\"1\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"75\" cy=\"75\" r=\"1\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"50\" cy=\"10\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"10\" cy=\"60\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"90\" cy=\"40\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');
                    pointer-events: none;
                }
                
                .brand-logo {
                    position: relative;
                    z-index: 2;
                    margin-bottom: 30px;
                }
                
                .brand-name {
                    color: white;
                    font-size: 32px;
                    font-weight: 700;
                    letter-spacing: -0.5px;
                    margin-bottom: 8px;
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                }
                
                .brand-tagline {
                    color: rgba(255, 255, 255, 0.9);
                    font-size: 16px;
                    font-weight: 400;
                }
                
                .security-icon {
                    position: relative;
                    z-index: 2;
                    width: 80px;
                    height: 80px;
                    background: rgba(255, 255, 255, 0.15);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 30px;
                    backdrop-filter: blur(10px);
                    border: 2px solid rgba(255, 255, 255, 0.2);
                }
                
                .security-icon svg {
                    width: 40px;
                    height: 40px;
                    fill: white;
                    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
                }
                
                .email-body {
                    padding: 50px 40px;
                }
                
                .greeting {
                    font-size: 18px;
                    color: #4a5568;
                    margin-bottom: 30px;
                    font-weight: 500;
                }
                
                .main-title {
                    font-size: 28px;
                    font-weight: 700;
                    color: #1a202c;
                    text-align: center;
                    margin-bottom: 20px;
                    letter-spacing: -0.5px;
                }
                
                .subtitle {
                    font-size: 16px;
                    color: #718096;
                    text-align: center;
                    margin-bottom: 40px;
                    line-height: 1.6;
                }
                
                .cta-section {
                    text-align: center;
                    margin: 40px 0;
                }
                
                .reset-button {
                    display: inline-block;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    text-decoration: none;
                    padding: 18px 40px;
                    border-radius: 50px;
                    font-weight: 600;
                    font-size: 16px;
                    letter-spacing: 0.5px;
                    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }
                
                .reset-button::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                    transition: left 0.5s;
                }
                
                .reset-button:hover::before {
                    left: 100%;
                }
                
                .security-notice {
                    background: linear-gradient(135deg, #fef5e7 0%, #fdf2e9 100%);
                    border: 1px solid #f6ad55;
                    border-radius: 16px;
                    padding: 25px;
                    margin: 40px 0;
                    position: relative;
                }
                
                .security-notice::before {
                    content: '⚠️';
                    position: absolute;
                    top: -10px;
                    left: 25px;
                    background: #fef5e7;
                    padding: 0 10px;
                    font-size: 20px;
                }
                
                .notice-title {
                    color: #c05621;
                    font-weight: 600;
                    font-size: 16px;
                    margin-bottom: 10px;
                }
                
                .notice-text {
                    color: #9c4221;
                    font-size: 14px;
                    line-height: 1.5;
                    margin: 0;
                }
                
                .info-grid {
                    display: table;
                    width: 100%;
                    margin: 30px 0;
                }
                
                .info-item {
                    display: table-row;
                    margin-bottom: 15px;
                }
                
                .info-icon {
                    display: table-cell;
                    width: 40px;
                    vertical-align: top;
                    padding-right: 15px;
                    padding-top: 2px;
                }
                
                .info-icon svg {
                    width: 20px;
                    height: 20px;
                    fill: #667eea;
                }
                
                .info-text {
                    display: table-cell;
                    color: #4a5568;
                    font-size: 14px;
                    line-height: 1.5;
                    vertical-align: top;
                }
                
                .email-footer {
                    background: #f7fafc;
                    padding: 40px;
                    text-align: center;
                    border-top: 1px solid #e2e8f0;
                }
                
                .footer-text {
                    color: #718096;
                    font-size: 14px;
                    margin-bottom: 15px;
                }
                
                .footer-brand {
                    color: #4a5568;
                    font-weight: 600;
                    font-size: 16px;
                    margin-bottom: 5px;
                }
                
                .footer-note {
                    color: #a0aec0;
                    font-size: 12px;
                    font-style: italic;
                }
                
                .divider {
                    height: 1px;
                    background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
                    margin: 30px 0;
                }
                
                @media only screen and (max-width: 600px) {
                    body {
                        padding: 20px 10px;
                    }
                    
                    .email-body {
                        padding: 30px 25px;
                    }
                    
                    .email-header {
                        padding: 40px 25px;
                    }
                    
                    .email-footer {
                        padding: 30px 25px;
                    }
                    
                    .main-title {
                        font-size: 24px;
                    }
                    
                    .reset-button {
                        padding: 16px 30px;
                        font-size: 15px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <!-- Header -->
                <div class='email-header'>
                    <div class='security-icon'>
                        <svg viewBox='0 0 24 24' fill='currentColor'>
                            <path d='M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z'/>
                        </svg>
                    </div>
                    <div class='brand-logo'>
                        <div class='brand-name'>CTRL + EAT</div>
                        <div class='brand-tagline'>Delicious Food Delivery</div>
                    </div>
                </div>
                
                <!-- Body -->
                <div class='email-body'>
                    <div class='greeting'>Hello $u->username,</div>
                    
                    <h1 class='main-title'>🔐 Password Reset Request</h1>
                    <p class='subtitle'>
                        We received a request to reset your password. Click the button below to create a new secure password for your account.
                    </p>
                    
                    <div class='cta-section'>
                        <a href='$url' class='reset-button'>
                            🔑 Reset My Password
                        </a>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <div class='info-grid'>
                        <div class='info-item'>
                            <div class='info-icon'>
                                <svg viewBox='0 0 24 24' fill='currentColor'>
                                    <path d='M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17A1.5,1.5 0 0,1 10.5,15.5A1.5,1.5 0 0,1 12,14A1.5,1.5 0 0,1 13.5,15.5A1.5,1.5 0 0,1 12,17M12,10.5C10.07,10.5 8.5,8.93 8.5,7C8.5,5.07 10.07,3.5 12,3.5C13.93,3.5 15.5,5.07 15.5,7C15.5,8.93 13.93,10.5 12,10.5Z'/>
                                </svg>
                            </div>
                            <div class='info-text'>
                                <strong>Quick Action Required:</strong> This reset link will expire in 5 minutes for your security.
                            </div>
                        </div>
                        <div class='info-item'>
                            <div class='info-icon'>
                                <svg viewBox='0 0 24 24' fill='currentColor'>
                                    <path d='M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z'/>
                                </svg>
                            </div>
                            <div class='info-text'>
                                <strong>Secure Process:</strong> Your account remains protected throughout this process.
                            </div>
                        </div>
                        <div class='info-item'>
                            <div class='info-icon'>
                                <svg viewBox='0 0 24 24' fill='currentColor'>
                                    <path d='M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z'/>
                                </svg>
                            </div>
                            <div class='info-text'>
                                <strong>One-Time Use:</strong> This link can only be used once and expires automatically.
                            </div>
                        </div>
                    </div>
                    
                    <div class='security-notice'>
                        <div class='notice-title'>🛡️ Important Security Notice</div>
                        <p class='notice-text'>
                            If you <strong>did not request</strong> this password reset, please ignore this email. 
                            Your password will remain unchanged and your account stays secure. 
                            Consider enabling two-factor authentication for enhanced security.
                        </p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class='email-footer'>
                    <div class='footer-brand'>CTRL + EAT Team</div>
                    <div class='footer-text'>
                        Bringing delicious food to your doorstep with care and security.
                    </div>
                    <div class='footer-note'>
                        This is an automated security email. Please do not reply to this message.
                    </div>
                </div>
            </div>
        </body>
        </html>
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