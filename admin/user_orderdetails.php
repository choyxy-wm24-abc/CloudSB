<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM orderlist ol JOIN product p ON ol.product_id = p.product_id WHERE order_id = ?');
    $stm->execute([$order_id]);
    $or = $stm->fetch();

    extract((array)$or);
    $_SESSION['image'] = $or->image;
}

?>

<style>
    .title{
        text-align: center;
        padding: 10px;
        color: rgb(255, 255, 255);
        font-weight: bolder;
        background: rgb(178, 71, 255);
    }
</style>

<br>
<button onclick="window.history.back()" style='margin-left: 3%; font-size: 30px'>Back</button>
<main><br>
    <div class="order">
        <h1 class="title">Order History</h1>
        <br>
        <table>
            <tr class="head">
                <th>Order ID</th>
                <th>Product Image</th>
                <th>Product ID</th>
                <th>Price Per Unit (RM)</th>
                <th>Quantity</th>
                <th>Subtotal (RM)</th>
            </tr>
        <?php 
            $stm = $_db->prepare('SELECT * FROM orderlist ol JOIN product p ON ol.product_id = p.product_id WHERE order_id = ?');
            $stm->execute([$order_id]);
            $orders = $stm->fetchAll();
        ?>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= $o->order_id ?></td>
                    <td><img src="../image/<?= $o->image ?>" style='width: 150px; height: 150px;'></td>
                    <td><?= $o->product_id ?></td>
                    <td><?= $o->price_per_unit ?></td>
                    <td><?= $o->quantity ?></td>
                    <td><?= $o->subtotal ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <br>
        <br>
    </div>
</main>