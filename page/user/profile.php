<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM user WHERE user_id = ?');
    $stm->execute([$user->user_id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }

    extract((array)$u);
    $_SESSION['photo'] = $u->photo;
}

if (is_post()) { 
        $username   = req('username');
        $gender = req('gender');
        $age = req('age');
        $email = req('email'); 
        $photo = $_SESSION['photo'];
        $f = get_file('photo');
        $password   = req('password');
        
        //username validation
        if ($username == ''){
            $_err['username'] = 'Please enter your username';
        } else {
            $stm = $_db->prepare('
                SELECT COUNT(*) FROM user
                WHERE username = ? AND user_id!= ?
            ');
            $stm->execute([$username, $user->user_id]);
    
            if ($stm->fetchColumn() > 0) {
                $_err['username'] = 'Username Duplicated';
            }
        }
    
        //gender validation
        if ($gender == '') {
            $_err['gender'] = 'Required';
        }
    
        //age validation
        if($age == ''){
            $_err['age'] = 'Required';
        }
    
        //email validation
        if($email == ''){
            $_err['email'] = 'Please enter your email';
        }else if(!is_email($email)){
            $_err['email'] = 'Invalid email address. Please use a Gmail address.';
        }else {
            $stm = $_db->prepare('
                SELECT COUNT(*) FROM user
                WHERE email = ? AND user_id!= ?
            ');
            $stm->execute([$email, $user->user_id]);
    
            if ($stm->fetchColumn() > 0) {
                $_err['email'] = 'email Duplicated';
            }
        }
    
        // Validate: photo (file)
        
        if ($f) {
            if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($f->size > 3 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }

        }
    
    
        if (!$_err) {
    
            // (1) Delete and save photo --> optional
            if ($f) {
                    unlink("../../photos/$photo");
            
                $photo = save_photo($f, '../../photos');
                
            }
            
            // (2) Update user (email, name, photo)
            // TODO
            $stm = $_db->prepare('
                UPDATE user
                SET username = ?, gender = ?, age = ?, email = ?, photo = ?
                WHERE user_id = ?
            ');
            $stm->execute([$username, $gender, $age, $email, $photo, $user->user_id]);
    
            
            // (3) Update global user object
            $user->username = $username;
            $user->gender = $gender;
            $user->age = $age;
            $user->email = $email;
            $user->photo = $photo;

            temp('info', 'Record updated');
            header("Location: /index.php?user_id=$user->user_id");
        }
    
}

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../javascript/app.js"></script>

<head>
    <title>My Account</title>
    <link rel="shortcut icon" href="/image/teddy-bear.png">
    <link rel="stylesheet" href="/css/profile-modern.css">
</head>

<!-- Hero Section -->
<section class="profile-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="navigation-bar">
                <div class="back-navigation">
                    <button onclick="window.history.back()" class="back-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                        </svg>
                        <span>Back</span>
                    </button>
                </div>
                
                <h1 class="hero-title">
                    <span class="brand-highlight">My Account</span>
                </h1>
                
                <div class="account-actions">
                    <a href="./reset.php" class="reset-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,4A4,4 0 0,0 8,8A4,4 0 0,0 12,12A4,4 0 0,0 16,8A4,4 0 0,0 12,4M12,14C8.69,14 2,15.34 2,18.5V20A1,1 0 0,0 3,21H21A1,1 0 0,0 22,20V18.5C22,15.34 15.31,14 12,14Z"/>
                        </svg>
                        Reset Password
                    </a>
                    <a href="./logout.php" class="logout-btn" data-confirm="Are you sure you want to log out?">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
            <p class="hero-subtitle">Manage your account information and preferences</p>
        </div>
    </div>
</section>

<!-- Profile Section -->
<section class="profile-section">
    <div class="section-container">
        <div class="profile-content">
            <div class="profile-card">
                <div class="card-header">
                    <h2 class="section-title">Profile Information</h2>
                    <div class="title-decoration"></div>
                </div>
                
                <form method="post" class="profile-form" enctype="multipart/form-data">
                    <!-- Profile Photo Section -->
                    <div class="photo-section">
                        <div class="photo-container">
                            <label class="photo-upload" tabindex="0">
                                <?= html_file('photo', 'image/*', 'hidden') ?>
                                <div class="photo-wrapper">
                                    <img id="preview" src="../../photos/<?= $photo ?>" alt="Profile Photo">
                                    <div class="photo-overlay">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"/>
                                        </svg>
                                        <span>Change Photo</span>
                                    </div>
                                </div>
                            </label>
                            <?= err('photo') ?>
                        </div>
                    </div>
                    
                    <!-- Form Fields -->
                    <div class="form-fields">
                        <div class="field-group">
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
                        
                        <div class="field-group">
                            <label class="field-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z"/>
                                </svg>
                                Gender
                            </label>
                            <div class="radio-group">
                                <?= html_radios('gender', $_genders) ?>
                                <?= err('gender') ?>
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <label for="age" class="field-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z"/>
                                </svg>
                                Age
                            </label>
                            <div class="input-wrapper">
                                <?= html_text('age', 'placeholder="Enter your age"') ?>
                                <?= err('age') ?>
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <label for="email" class="field-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,5.11 21.1,4 20,4Z"/>
                                </svg>
                                Email
                            </label>
                            <div class="input-wrapper">
                                <?= html_text('email', 'placeholder="Enter your email address"') ?>
                                <?= err('email') ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="submit-btn" data-confirm="Are you sure you want to update this profile?">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                            </svg>
                            Update Profile
                        </button>
                        <button type="reset" class="reset-form-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,6V9L16,5L12,1V4A8,8 0 0,0 4,12C4,13.57 4.46,15.03 5.24,16.26L6.7,14.8C6.25,13.97 6,13 6,12A6,6 0 0,1 12,6M18.76,7.74L17.3,9.2C17.74,10.04 18,11 18,12A6,6 0 0,1 12,18V15L8,19L12,23V20A8,8 0 0,0 20,12C20,10.43 19.54,8.97 18.76,7.74Z"/>
                            </svg>
                            Reset Form
                        </button>
                    </div>
                </form>
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
</script>