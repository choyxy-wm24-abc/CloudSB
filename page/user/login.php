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


?>

<div class="background">
    <img src="/icon/background1.jpg" alt="Our background image">
</div>

<title>Login</title>
<link rel="stylesheet" href="../../css/menu.css">
<link rel="stylesheet" href="../../css/allbackgroundimg.css">
<link rel="stylesheet" href="../../css/login_signup.css">
<script src="javascript/app.js"></script>

<div class="login">
    <table>
        <th>
            <h1>Log In</h1>
            <h1>-------------------------------------------</h1>
            <h2>Please Login to your account to proceed</h2><br>
            <form method="post" class="form">
                <label for="username">Username :</label>
                <?= html_text('username') ?>
                <?= err('username') ?>
                <br><br>
                <label for="password">Password  :</label>
                <?= html_password('password') ?>
                <?= err('password')?>
                <?= err('invalid')?>
                <?= err('inactive')?>
                <h5><a href="reset.php">Forgot password?</a></h5>
                <h5>Haven get an account? <a href="signup.php">Sign Up</a> now.</h5>
                <button>Enter</button>
                <button type="button" onclick="window.location.href='../../index.php'">Back to Home</button>
            </form> 
        </th>
    </table>
</div>

