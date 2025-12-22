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

if (is_post()) {
    $password = req('password');
    $confirm  = req('confirm');

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'Required';
    }
    else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    // Validate: confirm
    if ($confirm == '') {
        $_err['confirm'] = 'Required';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    }
    else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    // DB operation
    if (!$_err) {
        // Update password
        $stm = $_db->prepare('UPDATE user SET password = SHA1(?) WHERE user_id = (SELECT user_id FROM token WHERE id = ?)');
        $stm->execute([$password, $id]);

        // Delete token
        $stm = $_db->prepare('DELETE FROM token WHERE id = ?');
        $stm->execute([$id]);


        temp('info', 'Record updated');
        redirect('./login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
?>

<link rel="stylesheet" href="/css/token-modern.css">

<!-- Hide old styles -->
<style>
header h1 { display: none !important; }
body form { display: none !important; }
header nav .menu { display: none !important; }
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
                    <div class="form-group">
                        <label for="password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <circle cx="12" cy="16" r="1"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            New Password
                        </label>
                        <div class="input-wrapper">
                            <?= html_password('password', 'maxlength="100" placeholder="Enter your new password"') ?>
                            <div class="password-toggle" onclick="togglePassword('password')">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </div>
                        </div>
                        <?= err('password') ?>
                        <div class="password-strength" id="password-strength"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                <polyline points="9,12 11,14 15,10"/>
                            </svg>
                            Confirm Password
                        </label>
                        <div class="input-wrapper">
                            <?= html_password('confirm', 'maxlength="100" placeholder="Confirm your new password"') ?>
                            <div class="password-toggle" onclick="togglePassword('confirm')">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </div>
                        </div>
                        <?= err('confirm') ?>
                        <div class="password-match" id="password-match"></div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
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
                        <span>Use at least 8 characters</span>
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
                        <span>Avoid common words or patterns</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

// Password strength checker
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm');
    const strengthDiv = document.getElementById('password-strength');
    const matchDiv = document.getElementById('password-match');

    if (passwordField) {
        passwordField.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            strengthDiv.innerHTML = `<div class="strength-bar strength-${strength.level}"><div class="strength-fill"></div></div><span class="strength-text">${strength.text}</span>`;
        });
    }

    if (confirmField) {
        confirmField.addEventListener('input', function() {
            const password = passwordField.value;
            const confirm = this.value;
            
            if (confirm.length > 0) {
                if (password === confirm) {
                    matchDiv.innerHTML = '<span class="match-success">✓ Passwords match</span>';
                } else {
                    matchDiv.innerHTML = '<span class="match-error">✗ Passwords do not match</span>';
                }
            } else {
                matchDiv.innerHTML = '';
            }
        });
    }
});

function checkPasswordStrength(password) {
    let score = 0;
    let feedback = [];

    if (password.length >= 8) score++;
    else feedback.push('at least 8 characters');

    if (/[a-z]/.test(password)) score++;
    else feedback.push('lowercase letters');

    if (/[A-Z]/.test(password)) score++;
    else feedback.push('uppercase letters');

    if (/[0-9]/.test(password)) score++;
    else feedback.push('numbers');

    if (/[^A-Za-z0-9]/.test(password)) score++;
    else feedback.push('special characters');

    const levels = ['weak', 'fair', 'good', 'strong', 'excellent'];
    const texts = ['Weak', 'Fair', 'Good', 'Strong', 'Excellent'];

    return {
        level: levels[score] || 'weak',
        text: texts[score] || 'Weak',
        feedback: feedback
    };
}
</script>


