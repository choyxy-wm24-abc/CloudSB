<?php
require_once '../../connect.php';
require_once '../../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_exists($email, 'user', 'email')) {
        $_err['email'] = 'Not exists';
    }

    // Send reset token (if valid)
    if (!$_err) {
        // TODO: (1) Select user
        $stm = $_db->prepare('SELECT * FROM user WHERE email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();

        // TODO: (2) Generate token id
        $id = sha1(uniqid() . rand());

        // Delete old token
        $stm = $_db->prepare('DELETE FROM token WHERE user_id = ?');
        $stm->execute([$u->user_id]);

        // Insert new token
        $stm = $_db->prepare('INSERT INTO token (id, expire, user_id) VALUES (?, ADDTIME(NOW(), "00:05"), ?)');
        $stm->execute([$id, $u->user_id]);


        // TODO: (4) Generate token url
        $url = base("page/user/token.php?id=$id");

        // TODO: (5) Send email
        $m = new stdClass();
        $m = get_mail();
        $m->addAddress($u->email, $u->username);
        $m->addEmbeddedImage("../../photos/$u->photo", 'photo');
        $m->isHTML(true);
        $m->Subject = "Reset password";
        $m->Body = "
            <img src='cid:photo'
                 style='width: 200px; height: 200px;
                        border: 1px solid #333'>
            <p>Dear $u->username,<p>
            <h1 style='color: red'>Request for Reset Password</h1>
            <p>
                Your password can be reset through the url below.
            </p>
            <p>
                <a href='$url'>$url</a>
            </p>
            <p>
                Please click on the url to reset your password asap.
            </p>
            <h2 style='font-style: italic; color: blue'>If you DID NOT request for password reset, 
            please IGNORE this message, your password will REMAIN UNCHANGE.</h2>
            <p>From, Crispy Kaw Kaw Admin</p>
        ";
        $m->send();

        temp('info', 'Email sent');
        redirect("/index.php?user_id={$u->user_id}");
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
?>

<link rel="stylesheet" href="/css/cart.css">

<style>

body form, body h2{
    margin-left: 120px;
    font-size:x-large;
} 

span {
    color: red;
}

</style>


<header>
    <nav>
        <div class="menu">
            <div class="menu_row">
                <span class="back_btn" onclick="window.history.back()"><img src="/icon/back.jpg" alt="backbtn"></span>
                <span><h1>Crispy Kaw²</h1></span><h1>|</h1><span><h1>Password Reset</h1></span>
            </div>
        </div>
    </nav>
</header>

<body>
    <br><h2>Please Enter your email to reset your password</h2><br>
    <form method="post" class="form">
        <label for="email">Email</label>
        <?= html_text('email', 'maxlength="100"') ?>
        <?= err('email') ?>
        
        <section>
            <br><button>Submit</button>
        </section>
    </form>
</body>
    
