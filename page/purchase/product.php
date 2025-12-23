<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php"); // Redirect to login page if not logged in
    exit();
}

// Get session user ID
$user_id = $_GET['user_id'] ?? $_SESSION['user']->user_id ?? null; // get user id from URL or session
$product_id = isset($_GET['p_id']) ? $_GET['p_id'] : null; // Get product_id from URL
$page = $_SESSION['page'] = $_SESSION['page'] ?? 1; // to get back the same page at shopnow.php

$stm = $_db->prepare('SELECT * FROM product p
JOIN category c ON c.category_id = p.category_id WHERE product_id = :product_id');

$stm->bindParam(':product_id', $product_id); // Bind product_id to the query
$stm->execute();
$product = $stm->fetch(); 

// Check if product exists
if (!$product) {
    temp('info', 'Product not found');
    header("Location: ./shopnow.php?user_id=$user_id");
    exit();
}

if (is_post()) {
    $product_id = req('product_id');
    $unit = req('unit');
    
    // Validate unit
    if ($unit <= 0) {
        $_err['unit'] = 'Please select a valid quantity';
    }
    
    if (!$_err) {
        update_cart($product_id, $unit);
        temp('info', 'Product added to cart successfully');
        // Redirect to cart page after adding to cart
        header("Location: ./cart.php?user_id=$user_id");
        exit();
    }
}

?>

<link rel="stylesheet" href="/css/product-modern.css">

<!-- Hide old styles -->
<style>
.singleproduct { display: none !important; }
</style>

<!-- Modern Product Page -->
<div class="modern-product-page">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="./shopnow.php?user_id=<?= $user_id ?>" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Shop
                </a>
            </div>
            <div class="hero-text">
                <div class="product-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="9" r="7"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
                <h1>Product Details</h1>
                <p>Everything you need to know about this delicious item</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="product-details-card">
                <div class="product-layout">
                    <!-- Product Image -->
                    <div class="product-image-section">
                        <div class="image-container">
                            <img src="/image/<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->product_name) ?>" class="product-image">
                            <div class="image-overlay">
                                <div class="category-badge">
                                    <?= htmlspecialchars($product->category_name) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="product-info-section">
                        <div class="product-header">
                            <h1 class="product-title"><?= htmlspecialchars($product->product_name) ?></h1>
                            <?php if (!empty($product->flavour)): ?>
                            <p class="product-flavor">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <?= htmlspecialchars($product->flavour) ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <div class="price-section">
                            <div class="current-price">RM <?= number_format($product->price, 2) ?></div>
                        </div>

                        <!-- Add to Cart Form -->
                        <div class="cart-section">
                            <form method="post" class="add-to-cart-form">
                                <?= html_hidden('product_id', $product->product_id) ?>
                                
                                <div class="quantity-selector">
                                    <label for="unit" class="quantity-label">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3"/>
                                            <circle cx="9" cy="20" r="1"/>
                                            <circle cx="20" cy="20" r="1"/>
                                        </svg>
                                        Quantity
                                    </label>
                                    <?php
                                        $cart = get_cart();
                                        $current_unit = $cart[$product->product_id] ?? 1;
                                    ?>
                                    <div class="quantity-input-wrapper">
                                        <?= html_select('unit', $_units, $current_unit, 'class="quantity-select"') ?>
                                    </div>
                                    <?= err('unit') ?>
                                </div>

                                <button type="submit" class="add-to-cart-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="9" cy="21" r="1"/>
                                        <circle cx="20" cy="21" r="1"/>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                        <path d="M12 8v8"/>
                                        <path d="M8 12h8"/>
                                    </svg>
                                    Add to Cart
                                </button>
                            </form>
                        </div>

                        <!-- Product Description -->
                        <?php if (!empty($product->details)): ?>
                        <div class="description-section">
                            <h3 class="description-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                                Product Description
                            </h3>
                            <p class="description-text"><?= nl2br(htmlspecialchars($product->details)) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="additional-info-card">
                <h3 class="info-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    Additional Information
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M7 7h.01"/>
                                <path d="M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 2 12V7a5 5 0 0 1 5-5z"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Category</h4>
                            <p><?= htmlspecialchars($product->category_name) ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Quality Guarantee</h4>
                            <p>Fresh ingredients and quality assured</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="3" width="15" height="13"/>
                                <polygon points="16,8 20,8 23,11 23,16 16,16 16,8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Fast Delivery</h4>
                            <p>Quick and reliable delivery service</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Customer Support</h4>
                            <p>24/7 customer service available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


