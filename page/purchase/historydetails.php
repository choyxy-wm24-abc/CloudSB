<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

$user_id = $_SESSION['user_id'] ?? null;

$order_id = $_GET['order_id'];

$stm = $_db->prepare('
    SELECT *
    FROM `order` AS o
    JOIN orderlist AS ol ON o.order_id = ol.order_id
    JOIN product AS p ON ol.product_id = p.product_id
    WHERE o.user_id = ? && o.order_id = ?
    ORDER BY o.order_id DESC
');
$stm->execute([$user->user_id, $order_id]);
$arr = $stm->fetchAll();

?>

<link rel="stylesheet" href="/css/history.css">

<header>
    <nav>
        <div class="menu">
            <div class="menu_row">
                <span class="back_btn" name="back" value="return"><a href="./orderhistory.php?user_id=<?= $_GET['user_id'] ?>&cat=All%20Products"><img src="/icon/back.jpg" alt="backbtn"></a></span>
                <span><h1>CTRL + EAT</h1></span><h1>|</h1><span><h1>My Shopping History</h1></span>
            </div>
        </div>
    </nav>
</header>

<body><br><hr><br><h1 class="product_history">Orderlist History</h1><br><hr><br>
    <div class="order_history">
        
        <table>
            <tr class="title">
                <th>Image</th>
                <th>Product</th>
                <th>Order ID</th>
                <th>Quantity</th>
                <th>Total (RM)</th>
                <th>Datetime</th>
            </tr>

        <?php foreach ($arr as $o): ?>
        <tr>
            <td><img src="../../image/<?= $o->image ?>" alt="product_image" style="width: 150px; height: 150px; border: 5px solid black"></td>
            <td><?= $o->product_name ?></td>
            <td><?= $o->order_id ?></td>
            <td><?= $o->quantity ?></td>
            <td><?= $o->subtotal ?></td>
            <td><?= $o->order_date ?></td>
        </tr>
        <?php endforeach ?>
    </table><br><hr><br>
</body>

