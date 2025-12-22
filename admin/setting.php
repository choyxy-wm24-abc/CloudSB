<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM user WHERE user_id = 1');
    $stm->execute();
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }

    extract((array)$u);
    $_SESSION['photo'] = $u->photo;
}

if (is_post()) { 
    $username   = 'admin';
    $gender = req('gender');
    $age = req('age');
    $email = req('email'); 
    $photo = $_SESSION['photo'];
    $f = get_file('photo');
    $password   = req('password');

    //username validation
    if ($username !== 'admin'){
        $_err['username'] = 'Please enter your username';
    }

    //gender validation
    if ($gender == '') {
        $_err['gender'] = 'Required';
    }

    //age validation
    if($age == ''){
        $_err['age'] = 'Required';
    }

    // email query
    $stm = $_db->prepare('
            SELECT COUNT(*) FROM user
            WHERE email = ? AND user_id = 1
        ');
        $stm->execute([$email]);

    //email validation
    if ($stm->fetchColumn() == 0) {
        if($email == ''){
            $_err['email'] = 'Please enter your email';
        }else if(!is_email($email)){
            $_err['email'] = 'Invalid email address. Please use a Gmail address.';
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
                unlink("../admin/photo/$photo");
        
            $photo = save_photo($f, '../admin/photo');
            
        }
        
        // (2) Update user (email, name, photo)
        // TODO
        $stm = $_db->prepare('
            UPDATE user
            SET gender = ?, age = ?, email = ?, photo = ?
            WHERE user_id = 1
        ');

        
        $stm->execute([$gender, $age, $email, $photo]);

        temp('info', 'Record updated');
        redirect('/admin/admin.php');
    }

}

?>

<link rel="stylesheet" href="../css/admin-settings-modern.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../javascript/app.js"></script>

<!-- Hero Section -->
<section class="admin-settings-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="back-navigation">
                <a href="../admin/admin.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>
            <h1 class="hero-title">
                <span class="brand-highlight">Admin Settings</span>
            </h1>
            <p class="hero-subtitle">Manage your administrator account preferences</p>
        </div>
    </div>
</section>

<!-- Settings Section -->
<section class="settings-section">
    <div class="section-container">
        <div class="settings-content">
            <div class="settings-card">
                <div class="card-header">
                    <h2 class="section-title">Account Information</h2>
                    <div class="title-decoration"></div>
                </div>
                
                <form method="post" class="settings-form" enctype="multipart/form-data">
                    <!-- Profile Photo Section -->
                    <div class="photo-section">
                        <div class="photo-container">
                            <label class="photo-upload" tabindex="0">
                                <?= html_file('photo', 'image/*', 'hidden') ?>
                                <div class="photo-wrapper">
                                    <img id="preview" src="/admin/photo/<?= $photo ?>" alt="Admin Photo">
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
                                <?= html_text_disabled('username') ?>
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
                        <button type="submit" class="submit-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                            </svg>
                            Update Settings
                        </button>
                        <button type="reset" class="reset-form-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,6V9L16,5L12,1V4A8,8 0 0,0 4,12C4,13.57 4.46,15.03 5.24,16.26L6.7,14.8C6.25,13.97 6,13 6,12A6,6 0 0,1 12,6M18.76,7.74L17.3,9.2C17.74,10.04 18,11 18,12A6,6 0 0,1 12,18V15L8,19L12,23V20A8,8 0 0,0 20,12C20,10.43 19.54,8.97 18.76,7.74Z"/>
                            </svg>
                            Reset Form
                        </button>
                    </div>
                    
                    <!-- Additional Actions -->
                    <div class="additional-actions">
                        <a href="/reset.php" class="action-btn reset-password-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C15.4,11.5 16,12.4 16,13V16C16,16.6 15.6,17 15,17H9C8.4,17 8,16.6 8,16V13C8,12.4 8.4,11.5 9,11.5V10C9,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.2,9.2 10.2,10V11.5H13.8V10C13.8,9.2 12.8,8.2 12,8.2Z"/>
                            </svg>
                            Reset Password
                        </a>
                        <a href="../../page/user/logout.php" class="action-btn logout-btn" data-confirm="Are you sure you want to log out?">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                            </svg>
                            Logout
                        </a>
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