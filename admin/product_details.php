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
        $_err['price'] = 'Price cannot be 0 or less than';
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="javascript/app.js"></script>

<style>
    div.status {
        color: white;
    }
</style>

<main>
    <div class="profile">
        <form method="post" class="form" enctype="multipart/form-data">
            <label for="product_name">Product Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('product_name') ?>
            <?= err('product_name') ?><br>
            
            <label for="flavour">Flavour&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('flavour') ?>
            <?= err('flavour') ?><br>
            
            <label for="details">Details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_textarea('details') ?>
            <?= err('details') ?><br>
            
            <label for="price">Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('price') ?>
            <?= err('price') ?><br>
            
            <label for="image">Product Image&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <label class="upload" tabindex="0">
                <?= html_file('image', 'image/*', 'hidden') ?>     
                <img id="preview" src="/image/<?= $image ?>">               
            </label>
            <?= err('image') ?><br>

            <label for="category_id">Category ID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('category_id') ?>
            <?= err('category_id') ?><br>

            <label>Status&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <div class="status">
                <?= html_radios('status', $_status) ?>
            </div>
            <?= err('status') ?><br>

            <section>
                <button>Submit</button>
                <button type="reset">Reset</button>
            </section>
        </form>
    </div>
</main>

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