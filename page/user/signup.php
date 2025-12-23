<?php

require_once '../../connect.php';
require_once '../../_base.php';

if (is_post())  { 
    $username   = req('username');
    $gender = req('gender');
    $age = req('age');
    $email = req('email'); 
    $f = get_file('photo');
    $user_id = null; // Initialize user ID
    $password   = req('password');
    $confirm_password = req('confirm_password', '');
    $role = "Member";
    $status = 1;

    //username validation
    if ($username == ''){
        $_err['username'] = 'Please enter your username';
    } else if (!is_unique($username, 'user', 'username')) {
        $_err['username'] = 'Username already exist';
    }

    //gender validation
    if ($gender == '') {
        $_err['gender'] = 'Required';
    }
    else if (!array_key_exists($gender, $_genders)) {
        $_err['name'] = 'Invalid value';
    }

    //age validation
    if($age == ''){
        $_err['age'] = 'Required';
    }else if (!is_numeric($age)) {
        $_err['age'] = 'Age must be a number';
    }else if ($age < 13) {
        $_err['age'] = 'You must be at least 13 years old to register';
    }else if ($age > 100) {
        $_err['age'] = 'Age invalid, please enter a valid age';
    }

    //email validation
    if($email == ''){
        $_err['email'] = 'Please enter your email';
    }else if(!is_email($email)){
        $_err['email'] = 'Invalid email address. Please use a Gmail address.';
    }else if(!is_unique($email, 'user', 'email')){
        $_err['email'] = 'Email already exist';
    }

    // Validate: photo (file)
    if (!$f) {
        $_err['photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    }
    else if ($f->size > 3 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }

    //password validation
    if ($password == ''){
        $_err['password'] = 'Please enter your password';
    } else if (strlen($password) < 8 || strlen($password) > 12) {
        $_err['password'] = 'Password must be between 8-12 characters';
    } else if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/', $password)) {
        $_err['password'] = 'Password must contain at least one letter, one number, and one special character (@$!%*?&)';
    }

    //password confirmation
    if ($confirm_password == ''){
        $_err['confirm_password'] = 'Please confirm your password';
    }
    else if ($password !== $confirm_password) {
        $_err['password_notmatched'] = 'Password Not Matched';
    }

    // Generate user ID
    $stm = $_db->query("SELECT MAX(user_id) FROM user");
    $max_id = $stm->fetchColumn();
    if ($max_id > 0){
            $user_id = $max_id + 1;
    }

    if (!$_err) {

        // (1) Save photo
        $photo = save_photo($f, '../../photos');

        $stm = $_db->prepare('INSERT INTO user (user_id, username, email, gender, age, photo, password, role, status) VALUES (?, ?, ?, ?, ?, ?, SHA1(?), ?, ?)');
        $stm->execute([$user_id, $username, $email, $gender, $age, $photo, $password, $role, $status]);
        redirect('login.php');
        temp('info', "Your account has been created successfully. Please log in.");
        
    } else 
        $_err['database'] = 'Error saving user data.';
    
}

$_title = 'User | Sign Up';
?>

<link rel="stylesheet" href="../../css/signup-modern.css">

<!-- Hide old styles -->
<style>
header nav .menu { display: none !important; }
body form { display: none !important; }
body h1 { display: none !important; }
</style>

<!-- Modern Signup Page -->
<div class="modern-signup-page">
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
                <div class="signup-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h1>Join Us Today</h1>
                <p>Create your account and start your delicious food journey</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Signup Form Card -->
            <div class="signup-form-card">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Fill in your information to get started</p>
                </div>

                <form method="post" class="modern-form" enctype="multipart/form-data">
                    <div class="form-row">
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
                            <label for="age">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                Age
                            </label>
                            <div class="input-wrapper">
                                <input type="number" id="age" name="age" class="modern-input" min="13" max="100" placeholder="Enter your age" value="<?= htmlspecialchars($age ?? '') ?>" required>
                            </div>
                            <?php if (isset($_err['age'])): ?>
                                <div class="error-message"><?= $_err['age'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Gender
                        </label>
                        <div class="radio-group">
                            <?php foreach ($_genders as $value => $text): ?>
                                <label class="radio-option">
                                    <input type="radio" name="gender" value="<?= $value ?>" <?= ($gender ?? '') == $value ? 'checked' : '' ?>>
                                    <span class="radio-custom"></span>
                                    <span class="radio-text"><?= $text ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($_err['gender'])): ?>
                            <div class="error-message"><?= $_err['gender'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            Email Address
                        </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" class="modern-input" placeholder="Enter your email address" value="<?= htmlspecialchars($email ?? '') ?>" required>
                        </div>
                        <?php if (isset($_err['email'])): ?>
                            <div class="error-message"><?= $_err['email'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="photo">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="9" cy="9" r="2"/>
                                <path d="M21 15l-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                            </svg>
                            Profile Photo
                        </label>
                        <div class="photo-upload-wrapper">
                            <label class="photo-upload" for="photo">
                                <input type="file" id="photo" name="photo" accept="image/*" hidden>
                                <img id="preview" src="../../photos/photo.jpg" alt="Preview">
                                <div class="upload-overlay">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17,8 12,3 7,8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <span>Click to upload photo</span>
                                    <small>JPG, PNG, GIF up to 3MB</small>
                                </div>
                            </label>
                        </div>
                        <?php if (isset($_err['photo'])): ?>
                            <div class="error-message"><?= $_err['photo'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-row">
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
                            <div class="password-strength" id="password-strength"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    <polyline points="9,12 11,14 15,10"/>
                                </svg>
                                Confirm Password
                            </label>
                            <div class="input-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" class="modern-input" placeholder="Confirm your password" required>
                                <div class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>
                            </div>
                            <?php if (isset($_err['confirm_password'])): ?>
                                <div class="error-message"><?= $_err['confirm_password'] ?></div>
                            <?php endif; ?>
                            <?php if (isset($_err['password_notmatched'])): ?>
                                <div class="error-message"><?= $_err['password_notmatched'] ?></div>
                            <?php endif; ?>
                            <div class="password-match" id="password-match"></div>
                        </div>
                    </div>
                    
                    <div class="password-requirements">
                        <h4>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Password Requirements
                        </h4>
                        <div class="requirements-grid">
                            <div class="requirement-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                                <span>8-12 characters long</span>
                            </div>
                            <div class="requirement-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                                <span>At least one letter</span>
                            </div>
                            <div class="requirement-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                                <span>At least one number</span>
                            </div>
                            <div class="requirement-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                                <span>Special character (@$!%*?&)</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Create Account
                    </button>
                </form>

                <div class="form-footer">
                    <div class="login-prompt">
                        <p>Already have an account?</p>
                        <a href="login.php" class="login-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                <polyline points="10,17 15,12 10,7"/>
                                <line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                            Sign In Instead
                        </a>
                    </div>
                </div>
            </div>

            <!-- Benefits Section -->
            <div class="benefits-section">
                <h3>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Join Thousands of Happy Customers
                </h3>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"/>
                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                        </svg>
                        <div class="benefit-content">
                            <h4>Easy Ordering</h4>
                            <p>Simple and intuitive ordering process</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        <div class="benefit-content">
                            <h4>Fast Delivery</h4>
                            <p>Quick delivery to your doorstep</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <div class="benefit-content">
                            <h4>Quality Food</h4>
                            <p>Fresh ingredients and delicious meals</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Photo upload preview
document.getElementById('photo').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('preview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Password toggle function
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

// Password strength checker
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm_password');
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
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[@$!%*?&]/.test(password)) score++;

    const levels = ['weak', 'weak', 'fair', 'good', 'strong', 'excellent'];
    const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Excellent'];

    return {
        level: levels[score] || 'weak',
        text: texts[score] || 'Very Weak'
    };
}
</script>


