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
<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/login_signup.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="javascript/app.js"></script>

<div class="admin-product-form-fullscreen">
    <div class="form-container-xl">
        <div class="form-header">
            <h1>Edit Product Details</h1>
            <p>Update product information below</p>
        </div>
        
        <form method="post" class="form-xl" enctype="multipart/form-data">
            <!-- Product Basic Info Section -->
            <div class="form-section">
                <h3 class="section-title">Basic Information</h3>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <?= html_text('product_name', 'placeholder="Enter product name"') ?>
                        <?= err('product_name') ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="flavour">Flavour</label>
                        <?= html_text('flavour', 'placeholder="e.g., Chocolate, Vanilla, Strawberry"') ?>
                        <?= err('flavour') ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category ID</label>
                        <?= html_text('category_id', 'type="number" min="1" placeholder="Enter category ID"') ?>
                        <?= err('category_id') ?>
                    </div>
                </div>
            </div>

            <!-- Product Details Section -->
            <div class="form-section">
                <h3 class="section-title">Product Details</h3>
                <div class="form-group-full">
                    <label for="details">Product Description</label>
                    <?= html_textarea('details', 'rows="8" placeholder="Enter detailed product description..."') ?>
                    <?= err('details') ?>
                </div>
            </div>

            <!-- Pricing & Status Section -->
            <div class="form-section">
                <h3 class="section-title">Pricing & Status</h3>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="price">Price (RM)</label>
                        <div class="input-with-icon">
                            <span class="input-icon">RM</span>
                            <?= html_text('price', 'type="number" step="0.01" min="0" placeholder="0.00"') ?>
                        </div>
                        <?= err('price') ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Product Status</label>
                        <div class="radio-group-modern">
                            <?= html_radios('status', $_status) ?>
                        </div>
                        <?= err('status') ?>
                    </div>
                </div>
            </div>

            <!-- Product Image Section -->
            <div class="form-section">
                <h3 class="section-title">Product Image</h3>
                <div class="image-upload-section">
                    <div class="image-preview-container">
                        <label class="upload-xl" tabindex="0">
                            <?= html_file('image', 'image/*', 'hidden') ?>     
                            <div class="upload-content">
                                <img id="preview" src="/image/<?= $image ?>" alt="Product Image">
                                <div class="upload-overlay">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
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
            <div class="form-actions-xl">
                <button type="submit" class="btn btn-primary-xl">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Update Product
                </button>
                <button type="reset" class="btn btn-secondary-xl">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2C17.5,2 22,6.5 22,12C22,17.5 17.5,22 12,22C6.5,22 2,17.5 2,12C2,6.5 6.5,2 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M16.59,8.59L10,15.17L7.41,12.59L6,14L10,18L18,10L16.59,8.59Z"/>
                    </svg>
                    Reset Form
                </button>
                <button type="button" class="btn btn-outline-xl" onclick="window.location.href='product.php'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    Back to Products
                </button>
            </div>
        </form>
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