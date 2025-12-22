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
    <link rel="shortcut icon" href="/image/teddy-bear.png">
    <link rel="stylesheet" href="/css/head.css">
    <link rel="stylesheet" href="/css/menu.css">
    <link rel="stylesheet" href="/css/modern-enhancements.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="javascript/app.js"></script>
</head>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
    <div class="menu_bar">
        <?php if (isset($_SESSION['user'])): ?>
            <div class="menu_icon"><a href="../../index.php?user_id=<?= $user->user_id?>"><img src="/icon/teddy-bear.png" alt="This is our store icon"></a></div>
        <?php else: ?>
            <div class="menu_icon"><a href="../../index.php"><img src="/icon/teddy-bear.png" alt="This is our store icon"></a></div>
        <?php endif ?>
        <div class="menu_store_name"><h1>CTRL + EAT</h1></div>

        <div class="menu_nav">
            <nav>
                <ul>
                    <li class="shopnow"> 
                        <?php if (isset($_SESSION['user'])): ?>
                            <h1><a href="/page/purchase/shopnow.php?&user_id=<?= $user->user_id ?>">Shop Now</a></h1>
                        <?php else: ?>
                            <h1><a href="/page/purchase/shopnow.php?">Shop Now</a></h1>
                        <?php endif ?>
                    </li>
                    
                    <li class="about">
                        <h1><a href="/page/user/about.php">About</a></h1>
                    </li>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <li>
                            <div class="cart" style="padding:6px; font-weight: bold;">Cart                               
                                <?php
                                    $cart = get_cart();
                                    $count = count($cart);
                                    if ($count) echo"($count)";
                                ?>
                            </div>
                            <div class="cart">
                                <a href="/page/purchase/cart.php?user_id=<?= $user->user_id ?>"
                                style="text-decoration: none; font-size: x-large; color: white;">
                                <img src="/icon/cart.jpg" alt="cart"></a>
                            </div>
                        </li>
                        <li><div class="order_history" style="font-weight: bold; margin-top: -8px;">
                                Order<br>History
                            </div>
                            <div class="history"><a href="/page/purchase/orderhistory.php?user_id=<?= $user->user_id ?>"><img src="/icon/order-history-icon.png" alt="history"></a></div>    
                        </li>
                        <div class="username" style="font-weight: bold;">
                            <?= $user->username ?>
                        </div>
                        <div class="photo">
                            <a href="../../page/user/profile.php?user_id=<?= $user->user_id ?>"><img src='../../photos/<?= $user->photo ?>'></a>
                        </div>
                        
                    <?php endif ?>
                    <?php if (!isset($_SESSION['user'])): ?>
                    <li class="dropdown"> 
                        <h1>Account</h1>
                        <ul class="dropdown_menu">
                            <li><a href="/page/user/signup.php">Sign Up</a></li>
                            <li><a href="/page/user/login.php">Login</a></li>
                        </ul>
                    </li>
                    <?php endif ?>
                </ul>
            </nav>
        </div>
    </div>

    </header>

    

</body>
    
<main>