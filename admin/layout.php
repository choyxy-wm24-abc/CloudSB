<?php

require_once '../connect.php';
require_once '../_base.php';

auth('Admin');

?>

<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/allbackgroundimg.css">

<div id="info"><?= temp('info') ?></div>

<header class="admin-header">
    <div class="admin-nav">
        <div class="admin-logo">
            <a href="../admin/admin.php" class="admin-logo-link">
                <img src="/icon/teddy-bear.png" alt="Admin Dashboard" class="admin-logo-img">
                <span class="admin-brand">Admin Dashboard</span>
            </a>
        </div>
        
        <nav class="admin-menu">
            <ul class="admin-nav-list">
                <li><a href="../admin/admin.php" class="admin-nav-link">Dashboard</a></li>
                <li><a href="../admin/product.php" class="admin-nav-link">Products</a></li>
                <li><a href="../admin/member.php" class="admin-nav-link">Members</a></li>
                <li><a href="../admin/order.php" class="admin-nav-link">Orders</a></li>
                <li><a href="../admin/setting.php" class="admin-nav-link">Settings</a></li>
            </ul>
        </nav>
        
        <div class="admin-actions">
            <a href="../index.php" class="admin-nav-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                </svg>
                <span>Back to Site</span>
            </a>
        </div>
    </div>
</header>

