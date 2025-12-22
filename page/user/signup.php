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

?>

<link rel="stylesheet" href="../../css/menu.css">
<link rel="stylesheet" href="../../css/allbackgroundimg.css">
<link rel="stylesheet" href="../../css/login_signup.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="javascript/app.js"></script>

<div class="signup">
    <table>
        <th>
            <h1>Sign Up</h1>
            <h2>Fill in the information below to create an account</h2>
            <form method="post" class="form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <?= html_text('username') ?>
                    <?= err('username') ?>
                </div>

                <div class="form-group">
                    <label>Gender:</label>
                    <div class="radio-group">
                        <?= html_radios('gender', $_genders) ?>
                    </div>
                    <?= err('gender') ?>
                </div>

                <div class="form-group">
                    <label for="age">Age:</label>
                    <?= html_text('age', 'type="number" min="13" max="100"') ?>
                    <?= err('age') ?>
                    <small>You must be at least 13 years old to register</small>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <?= html_text('email', 'type="email"') ?>
                    <?= err('email') ?>
                </div>

                <div class="form-group">
                    <label for="photo">Photo:</label>
                    <label class="upload" tabindex="0">
                        <?= html_file('photo', 'image/*', 'hidden') ?>                    
                        <img id="preview" src="../../photos/photo.jpg" alt="Preview">
                    </label>
                    <?= err('photo') ?>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <?= html_password('password') ?>
                    <?= err('password') ?>
                    <small>Password must be 8-12 characters with letters, numbers, and special characters (@$!%*?&)</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <?= html_password('confirm_password') ?>
                    <?= err('confirm_password') ?>
                    <?= err('password_notmatched') ?>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Sign Up</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='../../index.php'">Back to Home</button>
                </div>
            </form>
        </th>
    </table>
</div>

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


