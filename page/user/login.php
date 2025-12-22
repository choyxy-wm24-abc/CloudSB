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

<title>Login</title>
<link rel="stylesheet" href="../../css/menu.css">
<link rel="stylesheet" href="../../css/allbackgroundimg.css">
<link rel="stylesheet" href="../../css/login_signup.css">
<script src="javascript/app.js"></script>

<div class="login">
    <table>
        <th>
            <h1>Log In</h1>
            <h2>Please login to your account to proceed</h2>
            <form method="post" class="form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <?= html_text('username') ?>
                    <?= err('username') ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <?= html_password('password') ?>
                    <?= err('password')?>
                    <?= err('invalid')?>
                    <?= err('inactive')?>
                </div>
                
                <div class="form-group">
                    <p><a href="reset.php" style="color: #0077be; text-decoration: none;">Forgot password?</a></p>
                    <p>Don't have an account? <a href="signup.php" style="color: #0077be; text-decoration: none;">Sign Up</a> now.</p>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Log In</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='../../index.php'">Back to Home</button>
                </div>
            </form> 
        </th>
    </table>
</div>

