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

<link rel="stylesheet" href="/css/signup-modern.css">

<!-- Hero Section -->
<section class="signup-hero">
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
                <span class="brand-highlight">Join Us Today</span>
            </h1>
            <p class="hero-subtitle">Create your account to get started</p>
        </div>
    </div>
</section>

<!-- Signup Section -->
<section class="signup-section">
    <div class="section-container">
        <div class="signup-content">
            <div class="signup-card">
                <div class="card-header">
                    <div class="signup-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Create Account</h2>
                    <p class="section-description">
                        Fill in your information to create your account
                    </p>
                </div>
                
                <form method="post" class="signup-form" enctype="multipart/form-data">
                    <div class="form-row">
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
                            <label for="age" class="field-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9,12A3,3 0 0,0 12,9A3,3 0 0,0 15,12A3,3 0 0,0 12,15A3,3 0 0,0 9,12M12,20L16.95,15.05C17.92,14.08 18.5,12.77 18.5,11.36C18.5,9.15 16.85,7.5 14.64,7.5A4.14,4.14 0 0,0 12,8.5A4.14,4.14 0 0,0 9.36,7.5C7.15,7.5 5.5,9.15 5.5,11.36C5.5,12.77 6.08,14.08 7.05,15.05L12,20Z"/>
                                </svg>
                                Age
                            </label>
                            <div class="input-wrapper">
                                <?= html_text('age', 'type="number" min="13" max="100" placeholder="Enter your age"') ?>
                                <?= err('age') ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="field-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                            </svg>
                            Gender
                        </label>
                        <div class="radio-wrapper">
                            <?= html_radios('gender', $_genders) ?>
                            <?= err('gender') ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="field-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,5.11 21.1,4 20,4Z"/>
                            </svg>
                            Email Address
                        </label>
                        <div class="input-wrapper">
                            <?= html_text('email', 'type="email" placeholder="Enter your email address"') ?>
                            <?= err('email') ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="photo" class="field-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9,2V7.38L10.5,8.88L12,7.38V2H9M15,7.38V2H12V7.38L13.5,8.88L15,7.38M9,10.62L7.5,9.12L2,14.62V17.62H7.5L9,16.12V10.62M15,10.62V16.12L16.5,17.62H22V14.62L16.5,9.12L15,10.62Z"/>
                            </svg>
                            Profile Photo
                        </label>
                        <div class="photo-upload-wrapper">
                            <label class="photo-upload" tabindex="0">
                                <?= html_file('photo', 'image/*', 'hidden') ?>
                                <img id="preview" src="../../photos/photo.jpg" alt="Preview">
                                <div class="upload-overlay">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    </svg>
                                    <span>Click to upload</span>
                                </div>
                            </label>
                            <?= err('photo') ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
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
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="field-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                                </svg>
                                Confirm Password
                            </label>
                            <div class="input-wrapper">
                                <?= html_password('confirm_password', 'placeholder="Confirm your password"') ?>
                                <?= err('confirm_password') ?>
                                <?= err('password_notmatched') ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="password-requirements">
                        <h4>Password Requirements:</h4>
                        <ul>
                            <li>8-12 characters long</li>
                            <li>At least one letter</li>
                            <li>At least one number</li>
                            <li>At least one special character (@$!%*?&)</li>
                        </ul>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"/>
                            </svg>
                            Create Account
                        </button>
                    </div>
                </form>
                
                <div class="additional-actions">
                    <p class="help-text">Already have an account?</p>
                    <a href="login.php" class="login-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
                        </svg>
                        Sign In Instead
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
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

// Real-time password validation
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const feedback = document.createElement('div');
    feedback.className = 'password-feedback';
    
    // Remove existing feedback
    const existing = e.target.parentNode.querySelector('.password-feedback');
    if (existing) existing.remove();
    
    if (password.length > 0) {
        let messages = [];
        
        if (password.length < 8 || password.length > 12) {
            messages.push('8-12 characters required');
        }
        if (!/[a-zA-Z]/.test(password)) {
            messages.push('At least one letter required');
        }
        if (!/\d/.test(password)) {
            messages.push('At least one number required');
        }
        if (!/[@$!%*?&]/.test(password)) {
            messages.push('At least one special character (@$!%*?&) required');
        }
        
        if (messages.length > 0) {
            feedback.innerHTML = '<small style="color: #e74c3c;">• ' + messages.join('<br>• ') + '</small>';
            feedback.style.marginTop = '5px';
            e.target.parentNode.appendChild(feedback);
        } else {
            feedback.innerHTML = '<small style="color: #27ae60;">✓ Password meets all requirements</small>';
            feedback.style.marginTop = '5px';
            e.target.parentNode.appendChild(feedback);
        }
    }
});
</script>


