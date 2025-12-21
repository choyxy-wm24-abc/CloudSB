<?php

require_once '../connect.php';
require_once '../_base.php';

auth('Admin');

?>

<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/allbackgroundimg.css">

<div class="background">
    <img src="/icon/background1.jpg" alt="Our background image">
</div>

<div id="info"><?= temp('info') ?></div>

<div class="top_bar">
    <div class="row_bar">
        <div class="teddy">
            <a href="../admin/admin.php"><img src="/icon/teddy-bear.png" alt="Admin dashboard"></a>
        </div>
        <h2>Admin Dashboard</h2>
    </div>
</div>

