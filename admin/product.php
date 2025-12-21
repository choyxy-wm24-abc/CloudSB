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

<link rel="stylesheet" href="../css/admin.css">

<style>
    .popup {
        width: 150px;
        height: 150px;
    }

    .add button{
        font-size: large;
        padding: 15px;
        text-align: center;
        margin-left: 300%;
        width: 150px;
    }

    .title{
        text-align: center;
        padding: 10px;
        color: rgb(255, 255, 255);
        font-weight: bolder;
        background: rgb(178, 71, 255)
    }

    td button {
        text-decoration: none;
    }

    p.total {
        font-size: large;
        font-weight: bold;
        margin-left: 78%;
        color: white;
    }
</style>
<br>
<nav class="search" style="margin-left:-10px;">   
    <button  onclick="window.location.href='admin.php'" style='margin-left: 3%; font-size: 30px'>Back</button>
    <form method="get" style="margin-top: 8px;">
        <input type="hidden" >
        <label for="search" style="color: white;">Search Product By Product Name :</label>
        <?= html_search('product_search', $product_search) ?>
        <button type="submit">Search</button>
    </form>
    <div class="action">
        <div class="add">
            <button data-post="./product.php" onclick="window.location.href='./add.php'">ADD Product</button>
        </div>
    </div>
</nav>
<br>
<h1 class="title">Product List</h1>
<br>
<?php if (!$product_search && $page): ?>
    <?= $ms->html($user) ?>
<?php endif ?>
<br>
<table class="ad_table">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Product Category</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= $p-> product_id ?></td>
            <td><?= $p-> product_name ?></td>
            <td><?= $p-> price ?></td>
            <td><?= $p-> category_name ?></td>
            <td><button data-post="/product_details.php">
                <a href="../admin/product_details.php?cat=<?= $p->category_name ?>&p_id=<?= $p->product_id ?>" style="text-decoration: none; color: black;">EDIT</a></button></td>
            <td>
                <form method="post" action="/admin/delete.php?cat<?= $p->category_name ?>&p_id=<?= $p->product_id ?>" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    <?= html_hidden('product_id', $p->product_id) ?>
                    <button data-post="?image" data-confirm="Are you sure you want to delete this product?">DELETE</button>
                </form>
            </td>
            <td><img src='/image/<?=$p->image?>'  alt="Product Image" class="popup" ></td>

        </tr>
        <?php endforeach ?>
    <?php else: ?>
        <p style="color: white; font-size: xx-large; text-align: center; color: red;">No Product(s) Found.</p><br>
    <?php endif; ?>
</table>
<br>
<p class="total"><?= count($products)?> product(s)</p>
<br>

