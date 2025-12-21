<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM user u WHERE user_id = ?');
    $stm->execute([$user_id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/admin/member.php');
    }

    extract((array)$u);
    $_SESSION['photo'] = $u->photo;
}

if (is_post()) {
    $status = req('status');

    $stm = $_db->prepare('
        UPDATE user
        SET status = ?
        WHERE user_id = ?
    ');
    $stm->execute([$status, $user_id]);

    temp('info', 'User Status updated');
    redirect();
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

    .layout {
        margin-left: 120px;
    }

    .layout {
        display: grid;
        grid: auto / auto auto;
        gap: 5px;
        place-content: start;
        font-size: large;
        margin-top: 10px;
    }

    .layout > label:not(:has(*)) {
        place-self: stretch;
        background: #ccc;
        font-weight: bold;
        padding: 10px;
    }

    form {
        display: grid;
        grid: auto / auto auto auto auto;
        gap: 15px;
        place-content: start;
        font-size: large;
        margin-top: 10px;
        color: white;
    }

    form > label:not(:has(*)) {
        place-self: stretch;
        background: #ccc;
        font-weight: bold;
        padding: 10px;
    }
</style>

<br>
<button onclick="window.location.href='../admin/member.php'" style='margin-left: 3%; font-size: 30px'>Back</button>
<br><br>
<h1 class="title">USER DETAILS</h1>
<br>
<main>
    <div class="layout">
        <label for="photo">Photo:</label>
        <label class="upload" tabindex="0">
            <?= html_file_disabled('photo', 'image/*', 'hidden') ?>     
            <img id="preview" src="../photos/<?= $photo ?>" style="margin-left:0px;">               
        </label>
        <label for="username">Username:</label>
        <?= html_text_disabled('username') ?>
        
        <label for="age">Age:</label>
        <?= html_text_disabled('age') ?>
        
        <label for="email">Email:</label>
        <?= html_text_disabled('email') ?>
        <form method="post">
            <label for="status">Status:</label>
            <?= html_radios('status', $_status) ?>
            <button type="submit">Update</button>
        </form>
        <br><br><br>
    </div>
    <div class="order">
        <h1 class="title">Order History</h1>
        <br>
        <table>
            <tr class="head">
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Total Quantity</th>
                <th>Total Amount (RM)</th>
                <th>Details</th>
            </tr>
        <?php 
            $stm = $_db->prepare('SELECT * FROM `order` WHERE user_id = ?');
            $stm->execute([$user_id]);
            $orders = $stm->fetchAll();
        ?>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= $o->order_id ?></td>
                    <td><?= $o->order_date ?></td>
                    <td><?= $o->tquantity ?></td>
                    <td><?= $o->tprice ?></td>
                    <td colspan="2">
                        <button data-post="/user_details.php">
                        <a href="../admin/user_orderdetails.php?user_id=<?= $user_id ?>&order_id=<?= $o->order_id ?>" style="text-decoration: none; color: black;">DETAILS</a></button>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
        <br>
    </div>
</main>