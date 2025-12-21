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

if (is_post()) {
    $product_id = req('product_id');
    $unit = req('unit');
    update_cart($product_id, $unit);
    temp('info', 'Product added to cart successfully');
    $category = $_GET['cat'] ?? 'All Products'; // Default to 'All Products' if not set
    header("Location: ./shopnow.php?&user_id=$user_id");
    exit();
}

?>

<link rel="stylesheet" href="../../css/product.css">

<!-- Cointainer display -->
<div class="singleproduct" id="product">
    <div class="viewproduct">
        <!-- Close button -->
        <span class="close-btn" onclick="closeContainer()">&times;</span>

        <!-- Product Title -->
        <h1 class="product-title">Product Details</h1>
        <br>

        <div class="viewproduct-content">
            <!-- Image holder -->
            <div class="image-holder">
<img src='/image/<?= $product->image ?>' alt='<?= $product->image ?>'>

            </div>

            <!-- Product Information -->
            <div class="product-info">
                <h2><?= $product->product_name ?>&nbsp;(<?= $product->flavour ?>)</h2>
                <br>
                <h3 class="price">RM <?= $product->price ?></h3>
                <br>

                <!-- Quantity input -->
                <?php
                    $cart = get_cart();
                    $product_id = $product->product_id;
                    $unit = $cart[$product_id] ?? 0;
                ?>
                <form id="quantityForm" method="post">
                    <label for="quantity">Enter Quantity:</label>
                    <?= html_hidden('product_id', $product->product_id) ?>
                    <?= html_select('unit', $_units, $unit) ?>
                    <button class="btn cart-btn" onclick="closeContainer()">ADD TO CART</button>
                </form>
                
                <!-- Product description -->
                <br>
                <h3>Product Description</h3>
                <p><?= $product->details ?></p>
            </div>
        </div>
    </div>
</div>


<script>
    function openContainer() {
        document.getElementById('product').style.display = 'flex';
    }

    function closeContainer() {
        document.getElementById('product').style.display = 'none';
    }

    //Bring back to product page 
    function closeContainer() {
    window.history.back();
    }

    $('select').on('change', e => e.target.form.submit());

</script>


