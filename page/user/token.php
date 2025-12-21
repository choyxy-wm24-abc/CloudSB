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

<link rel="stylesheet" href="/css/cart.css">

<style>

header h1{
    margin-left: 120px;
}

body form{
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
                <h1>Crispy Kaw² &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; Password Reset</h1>
            </div>
        </div>
    </nav>
</header>

<body>
    <form method="post" class="form">
        <label for="password">New Password</label>
        <?= html_password('password', 'maxlength="100"') ?>
        <?= err('password') ?><br>

        <label for="confirm">Confirm Password</label>
        <?= html_password('confirm', 'maxlength="100"') ?>
        <?= err('confirm') ?>

        <section>
            <button>Submit</button>
        </section>
    </form>
</body>


