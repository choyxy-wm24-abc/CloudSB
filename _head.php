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
    <link rel="stylesheet" href="/css/modern-header-footer.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="javascript/app.js"></script>
</head>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <!-- Modern Header -->
    <header class="modern-main-header">
        <div class="header-container">
            <!-- Logo Section -->
            <div class="modern-logo-section">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="../../index.php?user_id=<?= $user->user_id?>" class="modern-logo-link">
                <?php else: ?>
                    <a href="../../index.php" class="modern-logo-link">
                <?php endif ?>
                    <div class="logo-icon">
                        <img src="/icon/teddy-bear.png" alt="CTRL + EAT Logo" class="modern-logo-img">
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">CTRL + EAT</span>
                        <span class="brand-tagline">Delicious Food Delivered</span>
                    </div>
                </a>
            </div>

            <!-- Main Navigation -->
            <nav class="modern-main-nav">
                <ul class="modern-nav-list">
                    <li class="modern-nav-item">
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="/page/purchase/shopnow.php?&user_id=<?= $user->user_id ?>" class="modern-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3"/>
                                    <circle cx="9" cy="20" r="1"/>
                                    <circle cx="20" cy="20" r="1"/>
                                </svg>
                                <span>Shop Now</span>
                            </a>
                        <?php else: ?>
                            <a href="/page/purchase/shopnow.php?" class="modern-nav-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3"/>
                                    <circle cx="9" cy="20" r="1"/>
                                    <circle cx="20" cy="20" r="1"/>
                                </svg>
                                <span>Shop Now</span>
                            </a>
                        <?php endif ?>
                    </li>
                    
                    <li class="modern-nav-item">
                        <a href="/page/user/about.php" class="modern-nav-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            <span>About</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Section -->
            <div class="modern-user-section">
                <?php if (isset($_SESSION['user'])): ?>
                    <!-- Cart -->
                    <div class="modern-nav-item cart-item">
                        <a href="/page/purchase/cart.php?user_id=<?= $user->user_id ?>" class="modern-nav-link cart-link">
                            <div class="nav-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3"/>
                                    <circle cx="9" cy="20" r="1"/>
                                    <circle cx="20" cy="20" r="1"/>
                                </svg>
                                <?php
                                    $cart = get_cart();
                                    $count = count($cart);
                                    if ($count): ?>
                                        <span class="cart-badge"><?= $count ?></span>
                                <?php endif ?>
                            </div>
                            <span>Cart</span>
                        </a>
                    </div>

                    <!-- Order History -->
                    <div class="modern-nav-item">
                        <a href="/page/purchase/orderhistory.php?user_id=<?= $user->user_id ?>" class="modern-nav-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                            </svg>
                            <span>Orders</span>
                        </a>
                    </div>

                    <!-- User Profile -->
                    <div class="modern-nav-item user-profile">
                        <a href="../../page/user/profile.php?user_id=<?= $user->user_id ?>" class="modern-profile-link">
                            <div class="profile-avatar">
                                <img src='../../photos/<?= $user->photo ?>' alt="Profile" class="profile-img">
                            </div>
                            <div class="profile-info">
                                <span class="username"><?= $user->username ?></span>
                                <span class="user-role">Member</span>
                            </div>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Account Dropdown for Non-logged Users -->
                    <div class="modern-nav-item account-dropdown">
                        <button class="modern-nav-link dropdown-toggle">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>Account</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="dropdown-arrow">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </button>
                        <ul class="modern-dropdown-menu">
                            <li>
                                <a href="/page/user/signup.php">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Sign Up
                                </a>
                            </li>
                            <li>
                                <a href="/page/user/login.php">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                        <polyline points="10,17 15,12 10,7"/>
                                        <line x1="15" y1="12" x2="3" y2="12"/>
                                    </svg>
                                    Login
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif ?>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Hide old header -->
    <style>
        .main-header { display: none !important; }
    </style>

</body>
    
<main>