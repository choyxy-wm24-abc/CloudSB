<?php
require_once '../../connect.php';
require_once '../../_base.php';

if (is_post()) {
    $username   = req('username');
    $password   = req('password');

    if ($username == '') { 
        $_err['username'] = 'Please enter your username';
    }

    if ($password == '') {
        $_err['password'] = 'Please enter your password';
    }
        $stmt = $_db->prepare("SELECT * FROM user WHERE username = ? AND password = SHA1(?)");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();

    if ($user) {
        if ($user->status == 0) {
            $_err['inactive'] = 'Your account has been temporarily closed.';
        } else {
            if ($user->role == "Admin") {
                temp('info', 'Welcome, Admin!');
                header("Location: ../../admin/admin.php");
                exit;
            } else {
                login($user);
                temp('info', "Welcome, $user->username");
                header("Location: ../../index.php?user_id=$user->user_id");
                exit;
            }
        }
    } else {
        $_err['invalid'] = 'Invalid username or password';
    }
}

$_title = 'User | Login';
?>

<link rel="stylesheet" href="/css/login-modern.css">

<!-- Hero Section -->
<section class="login-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="back-navigation">
                <button onclick="window.location.href='../../index.php'" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    <span>Back to Home</span>
                </button>
            </div>
            <h1 class="hero-title">
                <span class="brand-highlight">Welcome Back</span>
            </h1>
            <p class="hero-subtitle">Sign in to your account to continue</p>
        </div>
    </div>
</section>

<!-- Login Section -->
<section class="login-section">
    <div class="section-container">
        <div class="login-content">
            <div class="login-card">
                <div class="card-header">
                    <div class="login-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Sign In</h2>
                    <p class="section-description">
                        Enter your credentials to access your account
                    </p>
                </div>
                
                <form method="post" class="login-form">
                    <div class="form-group">
                        <label for="username" class="field-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                            </svg>
                            Username
                        </label>
                        <div class="input-wrapper">
                            <?= html_text('username', 'placeholder="Enter your username"') ?>
                            <?= err('username') ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="field-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                            </svg>
                            Password
                        </label>
                        <div class="input-wrapper">
                            <?= html_password('password', 'placeholder="Enter your password"') ?>
                            <?= err('password') ?>
                            <?= err('invalid') ?>
                            <?= err('inactive') ?>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
                            </svg>
                            Sign In
                        </button>
                    </div>
                </form>
                
                <div class="additional-actions">
                    <div class="forgot-password">
                        <a href="reset.php" class="forgot-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C15.4,11.5 16,12.4 16,13V16C16,16.6 15.6,17 15,17H9C8.4,17 8,16.6 8,16V13C8,12.4 8.4,11.5 9,11.5V10C9,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.2,9.2 10.2,10V11.5H13.8V10C13.8,9.2 12.8,8.2 12,8.2Z"/>
                            </svg>
                            Forgot your password?
                        </a>
                    </div>
                    
                    <div class="signup-prompt">
                        <p class="help-text">Don't have an account?</p>
                        <a href="signup.php" class="signup-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/>
                            </svg>
                            Create Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

