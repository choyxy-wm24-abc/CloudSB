<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

if (is_post()) {
    $address_name = req('address');

    if ($address_name == '') {
        $_err['address'] = 'Address Required';
    }

    if (!$_err) {
        $_SESSION['address_name'] = $address_name;
        header("Location: ./checkout.php?user_id=$user->user_id");
    }
       
}
?>

<link rel="stylesheet" href="/css/checkout.css">

<header>
    <nav>
        <div class="menu">
            <div class="menu_row">
                <span class="back_btn" onclick="window.location.href='./cart.php?user_id=<?= $_GET['user_id'] ?>'">
                    <img src="/icon/back.jpg" alt="backbtn">
                </span>
                <span><h1>CTRL + EAT</h1></span><h1>|</h1><span><h1>User Address Book</h1></span>
            </div>
        </div>
    </nav>
</header>

<body>
    <div class="checkout"><br>
        <h1 style="text-decoration: underline;">SELECT AN ADDRESS FROM ADDRESSBOOK</h1><br>
        <div class="address">
            <?php
                $stm = $_db->prepare('SELECT address_id, address_name FROM address WHERE user_id = ?');
                $stm->execute([$user->user_id]);
                $add = $stm->fetchAll(PDO::FETCH_ASSOC);

                $addressbook = [];
                foreach ($add as $a) {
                    $addressbook[$a['address_name']] = $a['address_name']; 
                }
            ?>
            <div class="addressbook">
                <form method="post">
                    <label for="address">Address Shipping:</label>
                    <?= html_select('address', $addressbook) ?>
                    <button type="submit" action="checkout.php" method="post" onclick="window.location.href='checkout.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>'">DONE</button>
                    <?= err('address') ?>
                </form>                    
            </div>
            <br><button action="add_address.php" method="post" onclick="window.location.href='add_address.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>'">Add new shipping address</button>
        </div>       
    </div>
</body>
