<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

$user_id = $_SESSION['user_id'] ?? null;

$stm = $_db->prepare('
    SELECT *
    FROM `order`
    WHERE user_id = ?
    ORDER BY order_id DESC
');
$stm->execute([$user->user_id]);
$arr = $stm->fetchAll();

?>

<link rel="stylesheet" href="/css/history.css">

<header>
    <nav>
        <div class="menu">
            <div class="menu_row">
                <span class="back_btn" name="back" value="return"><a href="./shopnow.php?user_id=<?= $_GET['user_id'] ?>"><img src="/icon/back.jpg" alt="backbtn"></a></span>
                <span><h1>Crispy Kaw²</h1></span><h1>|</h1><span><h1>My Shopping History</h1></span>
            </div>
        </div>
    </nav>
</header>

<body><br><hr><br><h1 class="product_history">Order History</h1><br><hr><br>
    <div class="order_history">
        
        <table>
            <tr class="title">
                <th>Order ID</th>
                <th>Order Quantity</th>
                <th>Total (RM)</th>
                <th>Datetime</th>
                <th>Details</th>
            </tr>

        <?php foreach ($arr as $o): ?>
        <tr>
            <td><?= $o->order_id ?></td>            
            <td><?= $o->tquantity ?></td>
            <td><?= $o->tprice ?></td>
            <td><?= $o->order_date ?></td>
            <td colspan="2">
                <button data-post="/user_details.php">
                <a href="../purchase/historydetails.php?user_id=<?= $user_id ?>&order_id=<?= $o->order_id ?>" style="text-decoration: none; color: black;">DETAILS</a></button>
            </td>
        </tr>
        <?php endforeach ?>
    </table><br><hr><br>
</body>

