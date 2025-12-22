<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$user_id = 1;

$page = req('page', 1);

$product_search = req('product_search');
if ($product_search) {
    $stm = $_db->prepare('SELECT * FROM product p 
        JOIN category c ON p.category_id = c.category_id 
        WHERE product_name LIKE ? 
        ORDER BY product_id ');
    $stm->execute(["%$product_search%"]);
    $products = $stm->fetchAll();
} else {
    require_once '../lib/SimplePager.php';
    $ms = new SimplePager('SELECT * FROM product p JOIN category c ON c.category_id = p.category_id
                        ORDER BY product_id', [], 15, $page);
    $products = $ms->result;
}

?>

<link rel="stylesheet" href="../css/admin-products-modern.css">

<!-- Hero Section -->
<section class="products-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="navigation-bar">
                <div class="back-navigation">
                    <a href="admin.php" class="back-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                        </svg>
                        <span>Back to Dashboard</span>
                    </a>
                </div>
                
                <div class="add-product-action">
                    <a href="./add.php" class="add-product-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                        </svg>
                        Add Product
                    </a>
                </div>
            </div>
            
            <h1 class="hero-title">
                <span class="brand-highlight">Product Management</span>
            </h1>
            <p class="hero-subtitle">Manage your menu items and inventory</p>
            
            <!-- Search Section -->
            <div class="search-section">
                <form method="get" class="search-form">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <?= html_search('product_search', $product_search, 'placeholder="Search products by name..."') ?>
                        <button type="submit" class="search-btn">
                            <span>Search</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="section-container">
        <div class="products-header">
            <h2 class="section-title">Product List</h2>
            <div class="products-count"><?= count($products) ?> products found</div>
        </div>
        
        <?php if (!$product_search && $page): ?>
            <div class="pagination-top">
                <?= $ms->html($user_id) ?>
            </div>
        <?php endif ?>
        
        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach ($products as $p): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="/image/<?= $p->image ?>" alt="<?= $p->product_name ?>" class="product-image">
                            <div class="product-overlay">
                                <div class="overlay-actions">
                                    <a href="../admin/product_details.php?cat=<?= $p->category_name ?>&p_id=<?= $p->product_id ?>" class="overlay-btn edit-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/>
                                        </svg>
                                    </a>
                                    <form method="post" action="/admin/delete.php?cat=<?= $p->category_name ?>&p_id=<?= $p->product_id ?>" onsubmit="return confirm('Are you sure you want to delete this product?');" class="delete-form">
                                        <?= html_hidden('product_id', $p->product_id) ?>
                                        <button type="submit" class="overlay-btn delete-btn">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-header">
                                <h3 class="product-name"><?= $p->product_name ?></h3>
                                <div class="product-id">ID: <?= $p->product_id ?></div>
                            </div>
                            
                            <div class="product-details">
                                <div class="detail-item">
                                    <span class="detail-label">Price:</span>
                                    <span class="detail-value price">RM <?= sprintf('%.2f', $p->price) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Category:</span>
                                    <span class="detail-value category"><?= $p->category_name ?></span>
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                <a href="../admin/product_details.php?cat=<?= $p->category_name ?>&p_id=<?= $p->product_id ?>" class="action-btn edit-btn-main">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form method="post" action="/admin/delete.php?cat=<?= $p->category_name ?>&p_id=<?= $p->product_id ?>" onsubmit="return confirm('Are you sure you want to delete this product?');" class="delete-form-main">
                                    <?= html_hidden('product_id', $p->product_id) ?>
                                    <button type="submit" class="action-btn delete-btn-main">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <div class="no-products">
                <div class="no-products-icon">📦</div>
                <h3>No Products Found</h3>
                <p>No products match your search criteria. Try adjusting your search terms or add new products.</p>
                <?php if (!empty($product_search)): ?>
                    <a href="/admin/product.php" class="reset-btn">
                        View All Products
                    </a>
                <?php endif; ?>
                <a href="./add.php" class="add-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                    </svg>
                    Add New Product
                </a>
            </div>
        <?php endif; ?>
        
        <?php if (!$product_search && $page && count($products) > 0): ?>
            <div class="pagination-bottom">
                <?= $ms->html($user_id) ?>
            </div>
        <?php endif ?>
    </div>
</section>