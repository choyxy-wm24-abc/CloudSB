<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$user_id = req('user_id');

$user = $_db->query('SELECT * FROM user WHERE user_id > 1')->fetchAll();

$user_search = req('user_search');
if ($user_search) {
    $stm = $_db->prepare('SELECT * FROM user  
        WHERE username LIKE ?');
    $stm->execute(["%$user_search%"]);
    $user = $stm->fetchAll();
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
    <button onclick="window.location.href='../admin/admin.php'" style='margin-left: 3%; font-size: 30px'>Back</button>
    <form method="get" style="margin-top: 8px;">
        <input type="hidden" >
        <label for="search" style="color: white;">Search User By Username :</label>
        <?= html_search('user_search', $user_search) ?>
        <button type="submit">Search</button>
    </form>
</nav><br>
<h1 class="title">Member List</h1>
<br>
<table class="ad_table">
    <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Age</th>
        <th>About</th>
    </tr>
    <?php if (!empty($user)): ?>
        <?php foreach ($user as $u): ?>
        <tr>
            <td><?= $u->user_id ?></td>
            <td><?= $u->username ?></td>
            <td><?= $u->email ?></td>
            <td><?= $u->gender ?></td>
            <td><?= $u->age ?></td>
            <td><button data-post="/user_details.php">
                <a href="../admin/user_details.php?user_id=<?= $u->user_id ?>&username=<?= $u->username ?>" style="text-decoration: none; color: black;">DETAILS</a></button></td>
            <td>
            <td><img src='/photos/<?=$u->photo?>'  alt="Product Image" class="popup" ></td>
        </tr>
        <?php endforeach ?>
    <?php else: ?>
        <p style="color: white; font-size: xx-large; text-align: center; color: red;">No User(s) Found.</p><br>
    <?php endif; ?>
</table>
<br>
<p class="total"><?= count($user)?> user(s)</p>
<br>


