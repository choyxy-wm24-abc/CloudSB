<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$product_id = isset($_GET['p_id']) ? $_GET['p_id'] : null; // get from url
// $product_id = 1
if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
    $stm->execute([$product_id]); // 1
    $p = $stm->fetch();

    if (!$p) {
        redirect('/');
    }

    extract((array)$p);
    $_SESSION['product_name'] = $p->product_name;
    $_SESSION['image'] = $p->image;
}

if (is_post()) { // form sumbitted check
    $product_name   = req('product_name');
    $flavour = req('flavour');
    $details = req('details');
    $price = req('price'); 
    $image = $_SESSION['image'];
    $f = get_file('image');
    $category_id = req('category_id');
    $status = req('status');

    // Product Name validation
    if ($product_name == ''){
        $_err['product_name'] = 'Product Name cannot be empty';
    }
    else if (strlen($product_name) > 50) {
        $_err['product_name'] = 'Product Name too long';
    } 
    else {
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM product
            WHERE product_name = ? AND product_id!= ?
        '); 
        $stm->execute([$product_name, $product_id]);

        if ($stm->fetchColumn() > 0) {
            $_err['product_name'] = 'Product Name Duplicated';
        }
    }

    // Flavour validation
    if ($flavour == ''){
        $_err['flavour'] = 'Flavour cannot be empty';
    } 
    else if (strlen($flavour) > 30) {
        $_err['flavour'] = 'Flavour too long';
    } 
    

    // Details validation
    if ($details == ''){
        $_err['details'] = 'Details cannot be empty';
    } 
    else if (strlen($details) > 500) {
        $_err['details'] = 'Details too long';
    } 
    else {
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM product
            WHERE details = ? AND product_id!= ?
        ');
        $stm->execute([$details, $product_id]);

        if ($stm->fetchColumn() > 0) {
            $_err['details'] = 'Details too long should be not more than 500 words';
        }
    }

    // Price validation
    if ($price == ''){
        $_err['price'] = 'Price cannot be empty';
    } 
    else if(!is_numeric($price)){
        $_err['price'] = 'Price must be a number';
    }
    else if ($price <= 0) {
        $_err['price'] = 'Price cannot be 0 or less than';
    }
    
    // Validate: photo (file)
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['image'] = 'Must be image';
        }
        else if ($f->size > 3 * 1024 * 1024) {
            $_err['image'] = 'Maximum 3MB';
        }

    }

    // Category ID
    if ($category_id == ''){
        $_err['category_id'] = 'Category ID cannot be empty';
    }
    else if (!is_exists($category_id, 'category', 'category_id')) {
        $_err['category_id'] = 'Category ID does not exist';
    }

    // Status 
    if ($status == '') {
        $_err['status'] = 'Required';
    }

    if (!$_err) {

        // (1) Delete and save photo --> optional
        if ($f) {
                unlink("../Ass/image/$image");
        
            $image = save_photo($f, '../Ass/image');
            
        }
        
        // (2) Update user (email, name, photo)
        // TODO
        $stm = $_db->prepare('
            UPDATE product
            SET product_name = ?, flavour = ?, details = ?, price = ?, image = ?, category_id = ?, status = ?
            WHERE product_id = ?
        ');
        $stm->execute([$product_name, $flavour, $details, $price, $image, $category_id, $status, $product_id]);

        temp('info', 'Record updated');
        redirect('/admin/product.php');
    }

}

?>

<link rel="stylesheet" href="../css/admin-product-details-modern.css">

<!-- Hide old styles -->
<style>
.admin-product-form-fullscreen { display: none !important; }
</style>

<!-- Modern Admin Product Details Page -->
<div class="modern-admin-product-details">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="product.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Products
                </a>
            </div>
            <div class="hero-text">
                <div class="product-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <h1>Edit Product Details</h1>
                <p>Update product information and settings</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <form method="post" class="product-form" enctype="multipart/form-data">
                <!-- Basic Information Card -->
                <div class="form-card">
                    <div class="card-header">
                        <h2>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Basic Information
                        </h2>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product_name">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M7 7h.01"/>
                                    <path d="M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 2 12V7a5 5 0 0 1 5-5z"/>
                                </svg>
                                Product Name
                            </label>
                            <?= html_text('product_name', 'class="modern-input" placeholder="Enter product name"') ?>
                            <?= err('product_name') ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="flavour">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Flavour
                            </label>
                            <?= html_text('flavour', 'class="modern-input" placeholder="e.g., Chocolate, Vanilla"') ?>
                            <?= err('flavour') ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                </svg>
                                Category ID
                            </label>
                            <?= html_text('category_id', 'class="modern-input" type="number" min="1" placeholder="Enter category ID"') ?>
                            <?= err('category_id') ?>
                        </div>
                    </div>
                </div>

                <!-- Product Description Card -->
                <div class="form-card">
                    <div class="card-header">
                        <h2>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10,9 9,9 8,9"/>
                            </svg>
                            Product Description
                        </h2>
                    </div>
                    <div class="form-group-full">
                        <label for="details">Description</label>
                        <?= html_textarea('details', 'class="modern-textarea" rows="6" placeholder="Enter detailed product description..."') ?>
                        <?= err('details') ?>
                        <div class="character-count">Max 500 characters</div>
                    </div>
                </div>

                <!-- Pricing & Status Card -->
                <div class="form-card">
                    <div class="card-header">
                        <h2>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                            Pricing & Status
                        </h2>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="price">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                Price (RM)
                            </label>
                            <div class="price-input-wrapper">
                                <span class="currency-symbol">RM</span>
                                <?= html_text('price', 'class="modern-input price-input" type="number" step="0.01" min="0" placeholder="0.00"') ?>
                            </div>
                            <?= err('price') ?>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                                Product Status
                            </label>
                            <div class="status-options">
                                <?= html_radios('status', $_status, '', 'class="status-radio"') ?>
                            </div>
                            <?= err('status') ?>
                        </div>
                    </div>
                </div>

                <!-- Product Image Card -->
                <div class="form-card">
                    <div class="card-header">
                        <h2>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21,15 16,10 5,21"/>
                            </svg>
                            Product Image
                        </h2>
                    </div>
                    <div class="image-upload-section">
                        <div class="image-preview-container">
                            <label class="image-upload-label" for="image">
                                <?= html_file('image', 'image/*', 'class="image-input" id="image"') ?>
                                <div class="image-preview">
                                    <img id="preview" src="/image/<?= htmlspecialchars($image) ?>" alt="Product Image">
                                    <div class="upload-overlay">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                            <polyline points="21,15 16,10 5,21"/>
                                        </svg>
                                        <p>Click to upload new image</p>
                                        <small>Max size: 3MB | Formats: JPG, PNG, GIF</small>
                                    </div>
                                </div>
                            </label>
                            <?= err('image') ?>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        Update Product
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                        Reset Form
                    </button>
                    <a href="product.php" class="btn btn-outline">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 19-7-7 7-7"/>
                            <path d="m19 12H5"/>
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('preview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>