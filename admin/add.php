<?php
require_once '../connect.php';
require_once '../_base.php';

require_once '../admin/layout.php';

auth('Admin');

if (is_post()){
    $product_name = req('product_name');
    $flavour = req('flavour');
    $details = req('details');
    $price = req('price'); 
    $f = get_file('image');
    $category_id = req('category_id');
    $product_id = null; // Initialize user ID

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
            WHERE product_name = ?
        ');
        $stm->execute([$product_name]);

        if ($stm->fetchColumn() > 0) {
            $_err['product_name'] = 'Product Name Existed';
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
        $_err['details'] = 'Details too long should be not more than 500 words';
    } 
    else {
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM product
            WHERE details = ?
        ');
        $stm->execute([$details]);

        if ($stm->fetchColumn() > 0) {
            $_err['details'] = 'Details Duplicated';
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
    
    // Validate: image (file)
    if (!$f) {
        $_err['image'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['image'] = 'Must be image';
    }
    else if ($f->size > 3 * 1024 * 1024) {
        $_err['image'] = 'Maximum 3MB';
    }

    // Category ID
    if ($category_id == ''){
        $_err['category_id'] = 'Category ID cannot be empty';
    }
    else if (!is_exists($category_id, 'category', 'category_id')) {
        $_err['category_id'] = 'Category ID does not exist';
    }

    // Generate product ID
    $stm = $_db->query("SELECT MAX(product_id) FROM product");
    $max_id = $stm->fetchColumn();
    if ($max_id > 0){
            $product_id = $max_id + 1;
    }

    if (!$_err) {

        $image = save_photo($f, '../image');
        
        // INSERT INTO product
        $stm = $_db->prepare('
            INSERT INTO product (product_id, product_name, flavour, details, price, image, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stm->execute([$product_id, $product_name, $flavour, $details, $price, $image, $category_id]);

        temp('info', 'Product Added Successful');
        redirect('/admin/product.php');
    }
}

?>

<link rel="stylesheet" href="../css/admin.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="javascript/app.js"></script>


<style>
    h1 {
        color: white;
        padding: 15px;
        margin-left: 8%;
    }
</style>

<br>
<button onclick="window.history.back()" style='margin-left: 3%; font-size: 30px'>Back</button>
<br><br>

<header>
    <h1>ADD PRODUCT</h1>
</header>

<main>
    <div class="profile">
        <form method="post" class="form" enctype="multipart/form-data">
            <label for="product_name">Product Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('product_name') ?>
            <?= err('product_name') ?><br>
            
            <label for="flavour">Flavour&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('flavour') ?>
            <?= err('flavour') ?><br>
            
            <label for="details">Details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_textarea('details') ?>
            <?= err('details') ?><br>
            
            <label for="price">Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('price') ?>
            <?= err('price') ?><br>
            
            <label for="image">Product Image&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <label class="upload" tabindex="0">
            <input type="file" name="image" id="image" accept="image/*">                
            <img id="preview" src="../photos/photo.jpg" alt="Preview">
            </label>
            <?= err('image') ?><br>

            <label for="category_id">Category ID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label>
            <?= html_text('category_id') ?>
            <?= err('category_id') ?><br>

            <section style="font-size: 30px;">
                <button data-confirm="Are you sure you want to submit?">Submit</button>
                <button type="reset">Reset</button>
            </section>
        </form>
    </div>
    <br>
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