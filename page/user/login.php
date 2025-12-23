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

<link rel="stylesheet" href="../../css/login-modern.css">

<!-- Hide old styles -->
<style>
header nav .menu { display: none !important; }
body form { display: none !important; }
body h1 { display: none !important; }
</style>

<!-- Modern Login Page -->
<div class="modern-login-page">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="../../index.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Home
                </a>
            </div>
            <div class="hero-text">
                <div class="login-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10,17 15,12 10,7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>
                <h1>Welcome Back</h1>
                <p>Sign in to your account to continue your food journey</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Login Form Card -->
            <div class="login-form-card">
                <div class="form-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>

                <form method="post" class="modern-form">
                    <div class="form-group">
                        <label for="username">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Username
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="username" name="username" class="modern-input" placeholder="Enter your username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                        </div>
                        <?php if (isset($_err['username'])): ?>
                            <div class="error-message"><?= $_err['username'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <circle cx="12" cy="16" r="1"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            Password
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="modern-input" placeholder="Enter your password" required>
                            <div class="password-toggle" onclick="togglePassword('password')">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </div>
                        </div>
                        <?php if (isset($_err['password'])): ?>
                            <div class="error-message"><?= $_err['password'] ?></div>
                        <?php endif; ?>
                        <?php if (isset($_err['invalid'])): ?>
                            <div class="error-message"><?= $_err['invalid'] ?></div>
                        <?php endif; ?>
                        <?php if (isset($_err['inactive'])): ?>
                            <div class="error-message"><?= $_err['inactive'] ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="submit-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10,17 15,12 10,7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Sign In
                    </button>
                </form>

                <div class="form-footer">
                    <div class="forgot-password">
                        <a href="reset.php" class="forgot-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                            Forgot your password?
                        </a>
                    </div>
                    
                    <div class="signup-prompt">
                        <p>Don't have an account?</p>
                        <a href="signup.php" class="signup-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Create Account
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="features-section">
                <h3>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Why Choose CTRL + EAT?
                </h3>
                <div class="features-grid">
                    <div class="feature-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        <div class="feature-content">
                            <h4>Fast Delivery</h4>
                            <p>Quick and reliable food delivery to your doorstep</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <div class="feature-content">
                            <h4>Quality Food</h4>
                            <p>Fresh ingredients and delicious meals every time</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"/>
                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                        </svg>
                        <div class="feature-content">
                            <h4>Easy Ordering</h4>
                            <p>Simple and intuitive ordering process</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                <path d="M1 1l22 22"/>
            </svg>
        `;
    } else {
        field.type = 'password';
        toggle.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        `;
    }
}
</script>

