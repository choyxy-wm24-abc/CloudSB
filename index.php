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

<link rel="stylesheet" href="css/landing.css">

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    Welcome to<br>
                    The world of<br>
                    <span class="highlight">Tasty & Fresh Food.</span>
                </h1>
                <p class="hero-description">
                    Discover delicious meals crafted with love and the finest ingredients.<br>
                    From mouth-watering burgers and refreshing drinks to<br>
                    fresh, flavorful sides.
                </p>
                
                <!-- Search Bar -->
                <div class="search-container">
                    <form action="page/purchase/shopnow.php" method="GET" class="search-form">
                        <?php if ($user): ?>
                            <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                        <?php endif ?>
                        <div class="search-input-group">
                            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                            <input type="text" name="product_search" placeholder="Search food..." class="search-input">
                            <button type="submit" class="search-btn">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="hero-image">
                <div class="food-image-container">
                    <img src="icon/best-food-background.jpg" alt="Delicious Food" class="hero-food-img">
                    <div class="floating-elements">
                        <div class="float-element float-1"></div>
                        <div class="float-element float-2"></div>
                        <div class="float-element float-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Sellers Section -->
<section class="top-sellers-section">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">🔥 Top Sellers 🔥</h2>
            <p class="section-subtitle">Most loved dishes by our customers</p>
        </div>
        
        <div class="product-grid">
            <?php foreach ($top_products as $t): ?>
                <div class="product-card">
                    <?php if ($user): ?>
                        <a href="./page/purchase/product.php?user_id=<?= $user->user_id ?>&p_id=<?= $t->product_id ?>" class="product-link">
                    <?php else: ?>
                        <a href="page/user/login.php" class="product-link">
                    <?php endif ?>
                        <div class="product-image-wrapper">
                            <img src="./image/<?= $t->image ?>" alt="<?= $t->product_name ?>" class="product-image">
                            <div class="product-overlay">
                                <span class="view-details">View Details</span>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= $t->product_name ?></h3>
                            <p class="product-flavor"><?= $t->flavour ?></p>
                            <div class="product-price">RM <?= $t->price ?></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>  
        </div>
        
        <div class="cta-section">
            <?php if ($user): ?>
                <a href="page/purchase/shopnow.php?user_id=<?= $user->user_id ?>" class="cta-button">
            <?php else: ?>
                <a href="page/purchase/shopnow.php" class="cta-button">
            <?php endif ?>
                Explore All Menu
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                </svg>
            </a>
        </div>
    </div>
</section>


<?php 
    include '_foot.php';
?>


