<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

if (is_post()) {
    $btn = req('btn');
    if ($btn == 'clear') {
        set_cart();
        redirect();
    }
}
?>

<link rel="stylesheet" href="../../css/cart.css">

<header>
    <nav>
        <div class="menu">
            <div class="menu_row">
                <span class="back_btn" name="back" value="return"><a href="./shopnow.php?user_id=<?= $_GET['user_id'] ?>"><img src="/icon/back.jpg" alt="backbtn"></a></span>
                <span><h1>CTRL + EAT</h1></span><h1>|</h1><span><h1>My Shopping Cart</h1></span>
            </div>
        </div>
    </nav>
</header>

<body>
    <div class="shopping_cart"><br><hr><br>
        <h1 class="product_cart">Cart Details</h1><br><hr><br>
        <table>
            <tr>
                <th>Product</th>
                <th>Name</th>
                <th>Price Per Unit (RM)</th>
                <th>Quantity</th>
                <th>Subtotal (RM)</th>
            </tr>
        <?php
        $count = 0;
        $total = 0;
        $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?'); 
        $cart = get_cart();
        foreach ($cart as $product_id => $unit):
             
            $stm->execute([$product_id]);
            $product = $stm->fetch();

            $subtotal = $product->price * $unit;
            $count += $unit;
            $total += $subtotal;
        ?>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<img src="/image/<?= $product->image ?>" id="product_image"></td>          
            <td><?= $product->product_name ?></td>
            <td><?= $product->price ?></td>
            <td class="quantity">
            <form method="post">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <select name="unit">
                    <?php foreach ($_units as $u): ?>
                        <option value="<?= $u ?>" <?= $u == $unit ? 'selected' : '' ?>><?= $u ?></option>
                    <?php endforeach ?>
                </select>
            </form>     
            </td>
            <td class="right subtotal" id="subtotal-<?= $product_id ?>">
                <?= sprintf('%.2f', $subtotal) ?>
            </td>
        </tr>
    <?php endforeach ?>
        <tr>
            <th colspan="3"></th>
            <th class="right" id="total-qty"><?= $count ?></th>
            <th class="right" id="total-price"><?= sprintf('%.2f', $total) ?></th>
        </tr> 
        </table><br><hr><br>
        
        <?php if ($cart): ?>
            
            <form method="post">
                <input type="hidden" name="btn" value="clear"> <!-- clear cart button -->
                <button type="submit">Clear</button>
                <button type="button" onclick="window.location.href='addressbook.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>'">Proceed to Select Address</button>
            </form>
        <?php endif ?>

    </div>
</body>

<script>
document.querySelectorAll('select').forEach(select => { 
    select.addEventListener('change', function () {         
        const form = this.closest('form');                  
        const formData = new FormData(form);
        fetch('update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const productId = formData.get('product_id');
            document.getElementById('subtotal-' + productId).textContent = data.subtotal;
            document.getElementById('total-qty').textContent = data.count;
            document.getElementById('total-price').textContent = data.total;
        });
    });
});
</script>

