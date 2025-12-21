<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

if (!isset($_SESSION['address_name'])) {
    header("Location: ./addressbook.php?user_id=$user->user_id");     
} else {
    $address = $_SESSION['address_name'];
}


$payment = '';
$account_number = '';
            

if (is_post()) {
    $payment = req('payment');
    $account_number = req('account_number');

    if ($payment == '') {
        $_err['payment'] = "Please select a payment method.";
    }

    if ($account_number === '') {
        $_err['account_number'] = "Please enter your account number.";
    } else if (!is_numeric($account_number)) {
        $_err['account_number'] = "Account number should only contain numbers.";
    } else if (strlen($account_number) < 10 || strlen($account_number) > 12) {
        $_err['account_number'] = "Account number should be 10 to 12 digits.";
    }

    // Check total cart amount
    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?'); 
    $cart = get_cart();
    $total = 0;

    foreach ($cart as $product_id => $unit) {
        $stm->execute([$product_id]);
        $product = $stm->fetch();
        $total += $product->price * $unit; 
    }

    // Final submission
    if (!$_err) {

        // Database Select Info
        $stm = $_db->prepare('
            SELECT * FROM user 
            WHERE email = ?
        ');
        $stm->execute([$user->email]);
        $u = $stm->fetch();

        // Send Report to notify user
        $m = new stdClass(); 
        $m = get_mail();   
        $m->addAddress($u->email, $u->username);
        $m->addEmbeddedImage("../../photos/$u->photo", 'photo');
        $m->isHTML(true);
        $m->Subject = "Your payment has been confirmed";
        $m->Body = "
            <img src='cid:photo'
                style='width: 200px; height: 200px; border: 1px solid #333'>
            <h2>Dear $u->username,</h2>
            <h1>Your Order had been confirmed</h1>
            <h3 style='color: blue;'>---- Payment Successfully ----</h3>
            <p style='font-size: large;'>
                Thank you for shopping with us!! Your order will be delivered to you within 
                <span style='color: green; font-size: x-large;'>3-5 working days</span>.
            </p>
            <p style='font-size: x-large; color: blue; font-weight: bold;'>From, Crispy Kaw Kaw Admin</p>
        ";
        $m->send();

        ?>  <!-- This form is post to the checkedout.php after success validation -->
        <form id="redirectForm" action="./checkedout.php?user_id=<?= $user->user_id ?>" method="post">
            <input type="hidden" name="payment" value="<?= htmlspecialchars($payment) ?>">
            <input type="hidden" name="account_number" value="<?= htmlspecialchars($account_number) ?>">
            <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
            <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
        </form>
        <script>
            document.getElementById('redirectForm').submit();  
        </script>

        <?php
        exit();
    }

}
?>

<link rel="stylesheet" href="/css/checkout.css">  
<script src="javascript/app.js"></script>

<header>
    <nav>
        <div class="menu">
            <div class="menu_row">
                <span class="back_btn" onclick="window.location.href='./addressbook.php?user_id=<?= $_GET['user_id'] ?>'">
                    <img src="/icon/back.jpg" alt="backbtn">
                </span>
                <span><h1>Crispy Kaw²</h1></span><h1>|</h1><span><h1>Check Out</h1></span>
            </div>
        </div>
    </nav>
</header>

<body>
    <div class="checkout"><br>
        <h1 style="text-decoration: underline;">Shipping Address</h1><br>
        <div class="address">
            <label for="address">Shipping Address:</label>
            <?= html_text_readonly('address', $address) ?>
            <button type="submit" action="checkout.php" method="post" style="font-size: large; padding: 5px; margin-top: -5px;"
            onclick="window.location.href='addressbook.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>'">Change</button>
        </div><br>

        <div class="order_details">
            <hr><br><h1 class="product_cart" style="text-align: center;">Checkout Details</h1><br><hr><br>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price Per Unit (RM)</th>
                    <th>Quantity</th>
                    <th>Subtotal (RM)</th>
                </tr>
            <?php
                $count = 0;
                $total = 0;
                $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
                $cart = get_cart();

                foreach ($cart as $product_id => $unit):
                    $stm->execute([$product_id]);
                    $product = $stm->fetch();
                    $subtotal = $product->price * $unit;
                    $count += $unit;
                    $total += $subtotal;
            ?>
                <tr>
                    <td><img src="/image/<?= $product->image ?>" style="width: 120px; height: 120px;"></td>
                    <td><?= $product->product_name ?></td>
                    <td><?= $product->price ?></td>
                    <td><?= $unit ?></td>
                    <td class="right"><?= sprintf('%.2f', $subtotal) ?></td>
                </tr>
            <?php endforeach; ?>
                <tr>
                    <th colspan="3"></th>
                    <th class="right"><?= $count ?></th>
                    <th class="right"><?= sprintf('%.2f', $total) ?></th>
                </tr>
            </table><br><hr><br><br>
        </div>
        <div class="payment_info">
            <form method="post" enctype="multipart/form-data" action="?user_id=<?= $user->user_id ?>"> <!-- This form is post for current file -->
                <label for="payment">Payment Method :</label>
                <?= html_select('payment', $payment_method, $payment) ?>
                <?= err('payment') ?>
                <br>
                <label for="account_number">Account Number :</label>
                <?= html_text('account_number', $account_number) ?>
                <?= err('account_number') ?>
                <br><br>
                <button type="submit" style="font-size: x-large;" data-confirm >Confirm</button>
            </form>
        </div>
    </div>
</body>
