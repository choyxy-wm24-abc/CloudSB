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

    //password
    if ($password == ''){
        $_err['password'] = 'Please enter your password';
    } 

    //password confirmation
    if ($confirm_password == ''){
        $_err['confirm_password'] = 'Please confirm your password';
    }
    else if ($password !== $confirm_password) {
        $_err['password_notmatched'] = 'Password Not Matched';
    }
    else if (strlen($confirm_password) < 8 || strlen($confirm_password) > 100) {
        $_err['confirm_password'] = 'Between 8-100 characters';
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

<div class="background">
    <img src="/icon/background1.jpg" alt="Our background image">
</div>

<div class="signup">
    <table>
        <th>
            <h1>Sign Up</h1>
            <h1>-------------------------------------------------</h1>
            <h2>Fill in the informations below for create an account</h2>
            <form method="post" class="form" enctype="multipart/form-data">
                <label for="username">Username&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_text('username') ?>
                <?= err('username') ?>
                <br><br>

                <label>Gender&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                    <div class="gender">
                        <?= html_radios('gender', $_genders) ?>
                    </div>
                <?= err('gender') ?>
                <br><br>

                <label for="age">Age&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_text('age') ?>
                <?= err('age') ?>
                <br><br>

                <label for="email">Email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_text('email') ?>
                <?= err('email') ?>
                <br><br>

                <label for="photo">Photo:</label>
                <label class="upload" tabindex="0">
                <?= html_file('photo', 'image/*', 'hidden') ?> 
                <?= err('photo') ?>                   
                <img id="preview" src="../../photos/photo.jpg" alt="Preview">
                </label>
                <br>

                <label for="password">Password&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
                <?= html_password('password') ?>
                <?= err('password') ?>
                <br><br>

                <label for="confirm_password">Confirm Password :</label>
                <?= html_password('confirm_password') ?>
                <?= err('confirm_password') ?>
                <?= err('password_notmatched') ?>
                <br><br>

                <button>Sign Up</button>
                <button type="button" onclick="window.location.href='../../index.php'">Back to Home</button>
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
</script>


