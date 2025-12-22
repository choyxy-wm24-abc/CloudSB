<?php
require_once 'connect.php';
require_once '_base.php';

$user = $_SESSION['user'] ?? null;

$cat = $_db->query('SELECT * FROM category')->fetchAll(); 

$product_search = req('product_search');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTRL + EAT</title>
    <link rel="shortcut icon" href="/icon/teddy-bear.png">
    <link rel="stylesheet" href="/css/head.css">
    <link rel="stylesheet" href="/css/menu.css">
    <link rel="stylesheet" href="/css/modern-enhancements.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="javascript/app.js"></script>
</head>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header class="main-header">
        <div class="menu_bar">
            <!-- Logo Section -->
            <div class="logo-section">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="../../index.php?user_id=<?= $user->user_id?>" class="logo-link">
                <?php else: ?>
                    <a href="../../index.php" class="logo-link">
                <?php endif ?>
                    <img src="/icon/teddy-bear.png" alt="CTRL + EAT Logo" class="logo-img">
                    <span class="brand-name">CTRL + EAT</span>
                </a>
            </div>

            <!-- Main Navigation -->
            <nav class="main-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="/page/purchase/shopnow.php?&user_id=<?= $user->user_id ?>" class="nav-link">
                                <span>Shop Now</span>
                            </a>
                        <?php else: ?>
                            <a href="/page/purchase/shopnow.php?" class="nav-link">
                                <span>Shop Now</span>
                            </a>
                        <?php endif ?>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/page/user/about.php" class="nav-link">
                            <span>About</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Section -->
            <div class="user-section">
                <?php if (isset($_SESSION['user'])): ?>
                    <!-- Cart -->
                    <div class="nav-item cart-item">
                        <a href="/page/purchase/cart.php?user_id=<?= $user->user_id ?>" class="nav-link cart-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                            <span>Cart</span>
                            <?php
                                $cart = get_cart();
                                $count = count($cart);
                                if ($count): ?>
                                    <span class="cart-count"><?= $count ?></span>
                            <?php endif ?>
                        </a>
                    </div>

                    <!-- Order History -->
                    <div class="nav-item">
                        <a href="/page/purchase/orderhistory.php?user_id=<?= $user->user_id ?>" class="nav-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z"/>
                            </svg>
                            <span>Orders</span>
                        </a>
                    </div>

                    <!-- User Profile -->
                    <div class="nav-item user-profile">
                        <a href="../../page/user/profile.php?user_id=<?= $user->user_id ?>" class="profile-link">
                            <img src='../../photos/<?= $user->photo ?>' alt="Profile" class="profile-img">
                            <span class="username"><?= $user->username ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Account Dropdown for Non-logged Users -->
                    <div class="nav-item dropdown">
                        <button class="nav-link dropdown-toggle">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                            </svg>
                            <span>Account</span>
                        </button>
                        <ul class="dropdown_menu">
                            <li><a href="/page/user/signup.php">Sign Up</a></li>
                            <li><a href="/page/user/login.php">Login</a></li>
                        </ul>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </header>

</body>
    
<main>