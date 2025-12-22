<?php
        error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

    require_once 'connect.php';
    require_once '_head.php';
    require_once '_base.php';

    $user_id = $_SESSION['user_id'] ?? null;

    $stm = $_db->prepare('
        SELECT p.*, SUM(ol.quantity) AS total_quantity
        FROM orderlist ol
        JOIN product p ON p.product_id = ol.product_id
        WHERE status = 1
        GROUP BY p.product_id
        ORDER BY total_quantity DESC
        LIMIT 3
    ');
    $stm->execute();
    $top_products = $stm->fetchAll();
?>

<link rel="stylesheet" href="../css/menu.css">
<link rel="stylesheet" href="../css/allbackgroundimg.css">
<link rel="stylesheet" href="../css/shopnow.css">


<style>
    table {
        width: 33.33%;
        border-collapse: seperate;
        border: 0px solid white;        
    }

    th {
        width: 33.3%;
        text-align: center;
        padding: 40px; 
        border-radius: 15px; /*rounded table*/
    }  

    header h1.best_sales {
        padding: 12px;
        margin-left:21%;
        color: #b2a5ff;
        font-size: 40px;
    }
</style>

<section class="top_seller">
    <h2>🔥Top Seller🔥</h2>
</section>

<div class="background">
    <img src="/icon/background1.jpg" alt="Our background image">
</div>

<div class="product-container">
    <?php foreach ($top_products as $t): ?>
        <div class="product-card">
            <?php if ($user): ?>
                <a href="./page/purchase/product.php?user_id=<?= $user->user_id ?>&p_id=<?= $t->product_id ?>" class="product-cell">
            <?php else: ?>
                <a href="page/user/login.php" class="product-cell">
            <?php endif ?>
                <img src="./image/<?= $t->image ?>" alt="<?= $t->product_name ?>">
                <div class="product-info">
                    <h2><?= $t->product_name ?></h2>
                    <p><?= $t->flavour ?></p>
                    <h3>RM <?= $t->price ?></h3>
                </div>
            </a>
        </div>
    <?php endforeach; ?>  
</div><br>


<?php 
    include '_foot.php';
?>


