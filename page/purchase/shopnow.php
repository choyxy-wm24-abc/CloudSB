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


<link rel="stylesheet" href="../../css/shopnow.css">
<link rel="stylesheet" href="../../css/menu.css">
<link rel="stylesheet" href="../../css/allbackgroundimg.css">

<div class="background">
    <img src="/icon/background1.jpg" alt="Our background image">
</div>

<br><br>
<div class="category-container">
    <?php foreach ($cat as $c): ?>

        <?php if (isset($_SESSION['user']) && isset($user)): ?>
            <a href="/page/purchase/shopnow.php?user_id=<?= $user->user_id ?>&cat=<?= urlencode($c->category_name) ?>"
               class="category-card">
                <span><?= $c->category_name ?></span>
            </a>
        <?php else: ?>
            <a href="/page/purchase/shopnow.php?cat=<?= urlencode($c->category_name) ?>"
               class="category-card">
                <span><?= $c->category_name ?></span>
            </a>
        <?php endif; ?>

    <?php endforeach; ?>
</div>

<br><br>
<nav class="search">                       
    <form style="font-size: larger;" method="get" action="">
        <input type="hidden" name="user_id" value="<?= $user->user_id ?? null ?>">
        <label for="search" style="color: white;">Search By Product Name :</label>
        <?= html_search('product_search', $product_search) ?>
        <button type="submit">Search</button>
    </form>
</nav>
    <br>
    <?php if (!$product_search): ?>
        <?= $p->html($user->user_id ?? null) ?>
    <?php endif; ?>
<body>
    <div class="product-container">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if ($user): ?>
                        <a href="./product.php?user_id=<?= $user->user_id ?>&p_id=<?= $product->product_id ?>" class="product-cell">
                    <?php else: ?>
                        <a href="../user/login.php" style="text-decoration: none;">
                    <?php endif ?>
                        <img src="/image/<?= $product->image ?>" alt="product_img">
                        <h2><?= $product->product_name ?></h2><br>
                        <p><?= $product->flavour ?></p><br>
                        <h3>RM <?= $product->price ?></h3>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: white; font-size: xx-large; color: red;">No Products Found.</p>
        <?php endif; ?>
    </div>
</body>

<?php if (!$product_search): ?>
    <?= $p->html($user->user_id ?? null) ?>
<?php endif; ?>

<br><br><br>

