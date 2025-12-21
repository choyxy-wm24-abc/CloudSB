<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$user = 1;

$page = req('page', 1);

$order = $_db->query('SELECT * FROM `order` o JOIN user u ON u.user_id = o.user_id
                    ORDER BY o.order_id DESC')->fetchAll();

if ($page) {
    // Use pagination for full product list
    require_once '../lib/SimplePager.php';
    $o = new SimplePager('SELECT * FROM `order` o JOIN user u ON u.user_id = o.user_id
                        ORDER BY o.order_id DESC', [], 15, $page);
    $order = $o->result;
}

?>

<link rel="stylesheet" href="../css/admin.css">

<style>
    .popup {
        width: 150px;
        height: 150px;
    }

    p.total {
        font-size: large;
        font-weight: bold;
        margin-left: 78%;
        color: white;
    }

    .title{
        text-align: center;
        padding: 10px;
        color: rgb(255, 255, 255);
        font-weight: bolder;
        background: rgb(178, 71, 255)
    }
</style>

<br><nav class="search" style="margin-left:-10px;">   
    <button onclick="window.location.href='admin.php'" style='margin-left: 3%; font-size: 30px'>Back</button>
</nav><br>
<h1 class="title">Order Summary</h1>
<br>
<?php if ($page): ?>
    <?= $o->html($user) ?>
<?php endif; ?>
<br>
<table class="ad_table">
    <tr>
        <th>Order ID</th>
        <th>Order Date</th>
        <th>Total Quantity</th>
        <th>Total Price</th>
        <th>UserName</th>
        <th>Order Details</th>
    </tr>
        <?php foreach ($order as $o): ?>
        <tr>
            <td><?= $o->order_id ?></td>
            <td><?= $o->order_date ?></td>
            <td><?= $o->tquantity ?></td>
            <td><?= $o->tprice ?></td>
            <td><?= $o->username ?></td>
            <td>
                <button data-post="/user_details.php">
                <a href="../admin/user_orderdetails.php?user_id=<?= $o->user_id ?>&order_id=<?= $o->order_id ?>" style="text-decoration: none; color: black;">DETAILS</a></button>
            </td>
        </tr>
        <?php endforeach ?>
</table>
<br>
<p class="total"><?= count($order)?> Order(s)</p>
<br>