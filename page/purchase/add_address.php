<?php
require_once '../../connect.php';
require_once '../../_base.php';
auth('Member');

if (is_post()) {
    $newaddress = req('newaddress');

    if ($newaddress == '') {
        $_err['newaddress'] = 'Required';
    } else {
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM address
            WHERE address_name = ? AND user_id= ?
        ');
        $stm->execute([$newaddress, $user->user_id]);

        if ($stm->fetchColumn() > 0) {
            $_err['newaddress'] = 'Address already Exist in your account';
        }
    }

    if (!$_err) {
        $stm = $_db->prepare('INSERT INTO address (address_name, user_id) VALUES (?, ?)');
        $stm->execute([$newaddress, $user->user_id]);
        header("Location: ./addressbook.php?user_id=$user->user_id");
        exit;
    }

}

var_dump($user->user_id);
?>

<link rel="stylesheet" href="/css/checkout.css">

<style>
    .address {
        font-size: 30px;
    }

    input {
        font-size: 30px;
        width: 800px;
    }

    .err {
        color: red;
    }
</style>

<header>
    <nav>
        <div class="menu">
            <div class="menu_row">
                <span class="back_btn" onclick="window.location.href='./addressbook.php?user_id=<?= $_GET['user_id'] ?>'">
                    <img src="/icon/back.jpg" alt="backbtn">
                </span>
                <span><h1>Crispy Kaw²</h1></span><h1>|</h1><span><h1>Address Book</h1></span>
            </div>
        </div>
    </nav>
</header>

<body>
    <div class="checkout"><br>
        <h1 style="text-decoration: underline;">PLEASE ENTER A NEW ADDRESS</h1><br>
        <div class="address">
            <form action="" method="post">
                <label for="newaddress">New Address:</label>
                <?= html_text('newaddress') ?>
                <?= err('newaddress') ?>
            <section>
                <button>Submit</button>
            </section>
</form>
        </div>
        
    </div>
</body>