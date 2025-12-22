<?php
require_once '../../connect.php';
require_once '../../_base.php';
include '../../_head.php';

auth('Member');

$user_id = $_SESSION['user_id'] ?? null;

$product_search = req('product_search');
$category = req('cat', 'All Products');
$page = req('page', 1);
$_SESSION['page'] = $page;

// Prepare WHERE clause
$where = 'WHERE p.status = 1';
$params = [];

// Searching
if (!empty($product_search)) {
    $where .= ' AND p.product_name LIKE ?';
    $params[] = "%$product_search%";
}

// Filtering by category
if (!empty($category) && $category !== 'All Products') {
    $where .= ' AND c.category_name = ?';
    $params[] = $category;
}

// Always use paging
require_once '../../lib/SimplePager.php';
$query = "SELECT * FROM product p JOIN category c ON p.category_id = c.category_id $where ORDER BY c.category_name ASC";

$p = new SimplePager($query, $params, 15, $page);
$products = $p->result;

?>

<link rel="stylesheet" href="../../css/shopnow-modern.css">

<!-- Hero Section -->
<section class="shop-hero">
    <div class="hero-content">
        <h1 class="hero-title">Discover Our <span class="highlight">Delicious Menu</span></h1>
        <p class="hero-subtitle">Fresh ingredients, amazing flavors, and unforgettable experiences</p>
        
        <!-- Search Bar -->
        <div class="search-section">
            <form method="get" action="" class="modern-search-form">
                <input type="hidden" name="user_id" value="<?= $user->user_id ?? null ?>">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <?= html_search('product_search', $product_search, 'placeholder="Search for delicious food..."') ?>
                    <button type="submit" class="search-btn">
                        <span>Search</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="section-container">
        <h2 class="section-title">Browse by Category</h2>
        <div class="categories-grid">
            <?php foreach ($cat as $c): ?>
                <div class="category-item <?= ($category === $c->category_name) ? 'active' : '' ?>">
                    <?php if (isset($_SESSION['user']) && isset($user)): ?>
                        <a href="/page/purchase/shopnow.php?user_id=<?= $user->user_id ?>&cat=<?= urlencode($c->category_name) ?>" class="category-link">
                    <?php else: ?>
                        <a href="/page/purchase/shopnow.php?cat=<?= urlencode($c->category_name) ?>" class="category-link">
                    <?php endif; ?>
                        <div class="category-icon">
                            <?php
                            // Add icons based on category
                            $icons = [
                                'Fast Food' => '🍔',
                                'Asian Cuisine' => '🍜',
                                'Healthy Food' => '🥗',
                                'Desserts' => '🍰',
                                'Beverages' => '🥤',
                                'All Products' => '🍽️'
                            ];
                            echo $icons[$c->category_name] ?? '🍽️';
                            ?>
                        </div>
                        <span class="category-name"><?= $c->category_name ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="section-container">
        <div class="products-header">
            <h2 class="section-title">
                <?= !empty($category) && $category !== 'All Products' ? $category : 'All Products' ?>
                <?= !empty($product_search) ? " - Search: \"$product_search\"" : '' ?>
            </h2>
            <div class="products-count">
                <?= count($products) ?> items found
            </div>
        </div>

        <?php if (!$product_search): ?>
            <div class="pagination-top">
                <?= $p->html($user->user_id ?? null) ?>
            </div>
        <?php endif; ?>

        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="modern-product-card">
                        <?php if ($user): ?>
                            <a href="./product.php?user_id=<?= $user->user_id ?>&p_id=<?= $product->product_id ?>" class="product-link">
                        <?php else: ?>
                            <a href="../user/login.php" class="product-link">
                        <?php endif ?>
                            <div class="product-image-container">
                                <img src="/image/<?= $product->image ?>" alt="<?= $product->product_name ?>" class="product-image">
                                <div class="product-overlay">
                                    <div class="overlay-content">
                                        <span class="view-product">View Product</span>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="product-details">
                                <h3 class="product-name"><?= $product->product_name ?></h3>
                                <p class="product-flavor"><?= $product->flavour ?></p>
                                <div class="product-price">RM <?= $product->price ?></div>
                                <div class="product-category"><?= $product->category_name ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <div class="no-products-icon">🔍</div>
                    <h3>No Products Found</h3>
                    <p>Try adjusting your search or browse different categories</p>
                    <?php if (!empty($product_search) || $category !== 'All Products'): ?>
                        <a href="/page/purchase/shopnow.php<?= $user ? "?user_id={$user->user_id}" : '' ?>" class="reset-btn">
                            View All Products
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$product_search && count($products) > 0): ?>
            <div class="pagination-bottom">
                <?= $p->html($user->user_id ?? null) ?>
            </div>
        <?php endif; ?>
    </div>
</section>

