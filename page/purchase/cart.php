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

<link rel="stylesheet" href="../../css/cart-modern.css">

<!-- Hero Section -->
<section class="cart-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="back-navigation">
                <a href="./shopnow.php?user_id=<?= $_GET['user_id'] ?>" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    <span>Continue Shopping</span>
                </a>
            </div>
            <h1 class="hero-title">
                <span class="brand-highlight">My Shopping Cart</span>
            </h1>
            <p class="hero-subtitle">Review your delicious selections</p>
        </div>
    </div>
</section>

<!-- Cart Content -->
<section class="cart-section">
    <div class="section-container">
        <?php
        $count = 0;
        $total = 0;
        $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?'); 
        $cart = get_cart();
        ?>
        
        <?php if (empty($cart)): ?>
            <!-- Empty Cart State -->
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any delicious items to your cart yet.</p>
                <a href="./shopnow.php?user_id=<?= $_GET['user_id'] ?>" class="shop-now-btn">
                    Start Shopping
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </a>
            </div>
        <?php else: ?>
            <!-- Cart Items -->
            <div class="cart-content">
                <div class="cart-items">
                    <h2 class="section-title">Cart Items</h2>
                    
                    <?php foreach ($cart as $product_id => $unit): ?>
                        <?php
                        $stm->execute([$product_id]);
                        $product = $stm->fetch();
                        $subtotal = $product->price * $unit;
                        $count += $unit;
                        $total += $subtotal;
                        ?>
                        
                        <div class="cart-item">
                            <div class="item-image">
                                <img src="/image/<?= $product->image ?>" alt="<?= $product->product_name ?>">
                            </div>
                            
                            <div class="item-details">
                                <h3 class="item-name"><?= $product->product_name ?></h3>
                                <p class="item-flavor"><?= $product->flavour ?></p>
                                <div class="item-price">RM <?= $product->price ?> per unit</div>
                            </div>
                            
                            <div class="item-quantity">
                                <label>Quantity</label>
                                <form method="post" class="quantity-form">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    <div class="quantity-selector">
                                        <select name="unit" class="quantity-select">
                                            <?php foreach ($_units as $u): ?>
                                                <option value="<?= $u ?>" <?= $u == $unit ? 'selected' : '' ?>><?= $u ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="item-subtotal">
                                <div class="subtotal-label">Subtotal</div>
                                <div class="subtotal-amount" id="subtotal-<?= $product_id ?>">
                                    RM <?= sprintf('%.2f', $subtotal) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                
                <!-- Cart Summary -->
                <div class="cart-summary">
                    <div class="summary-card">
                        <h3 class="summary-title">Order Summary</h3>
                        
                        <div class="summary-row">
                            <span>Total Items:</span>
                            <span id="total-qty"><?= $count ?></span>
                        </div>
                        
                        <div class="summary-row total-row">
                            <span>Total Amount:</span>
                            <span class="total-amount" id="total-price">RM <?= sprintf('%.2f', $total) ?></span>
                        </div>
                        
                        <div class="summary-actions">
                            <form method="post" class="clear-form">
                                <input type="hidden" name="btn" value="clear">
                                <button type="submit" class="clear-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                    </svg>
                                    Clear Cart
                                </button>
                            </form>
                            
                            <button type="button" class="checkout-btn" onclick="window.location.href='addressbook.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17,18C17.56,18 18,18.44 18,19C18,19.56 17.56,20 17,20C16.44,20 16,19.56 16,19C16,18.44 16.44,18 17,18M1,2V4H3L6.6,11.59L5.24,14.04C5.09,14.32 5,14.65 5,15C5,16.1 5.9,17 7,17H19V15H7.42C7.28,15 7.17,14.89 7.17,14.75L7.2,14.63L8.1,13H15.55C16.3,13 16.96,12.58 17.3,11.97L20.88,5H5.21L4.27,3H1M7,18C7.56,18 8,18.44 8,19C8,19.56 7.56,20 7,20C6.44,20 6,19.56 6,19C6,18.44 6.44,18 7,18Z"/>
                                </svg>
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>

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
            document.getElementById('subtotal-' + productId).textContent = 'RM ' + data.subtotal;
            document.getElementById('total-qty').textContent = data.count;
            document.getElementById('total-price').textContent = 'RM ' + data.total;
        });
    });
});
</script>