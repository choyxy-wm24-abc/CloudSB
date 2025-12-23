<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

if (!isset($_SESSION['address_name'])) {
    header("Location: ./addressbook.php?user_id=$user->user_id");     
} else {
    $address = $_SESSION['address_name'];
}


$payment = '';
$account_number = '';
            

if (is_post()) {
    $payment = req('payment');
    $account_number = req('account_number');

    if ($payment == '') {
        $_err['payment'] = "Please select a payment method.";
    }

    if ($account_number === '') {
        $_err['account_number'] = "Please enter your account number.";
    } else if (!is_numeric($account_number)) {
        $_err['account_number'] = "Account number should only contain numbers.";
    } else if (strlen($account_number) < 10 || strlen($account_number) > 12) {
        $_err['account_number'] = "Account number should be 10 to 12 digits.";
    }

    // Check total cart amount
    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?'); 
    $cart = get_cart();
    $total = 0;

    foreach ($cart as $product_id => $unit) {
        $stm->execute([$product_id]);
        $product = $stm->fetch();
        $total += $product->price * $unit; 
    }

    // Final submission
    if (!$_err) {

        // Database Select Info
        $stm = $_db->prepare('
            SELECT * FROM user 
            WHERE email = ?
        ');
        $stm->execute([$user->email]);
        $u = $stm->fetch();

        // Send Report to notify user
        $m = new stdClass(); 
        $m = get_mail();   
        $m->addAddress($u->email, $u->username);
        
        // Fix PHPMailer error: Check if photo file exists before adding
        $photoPath = "../../photos/$u->photo";
        $photoExists = !empty($u->photo) && file_exists($photoPath);
        
        if ($photoExists) {
            $m->addEmbeddedImage($photoPath, 'photo');
            $photoTag = "<img src='cid:photo' style='width: 200px; height: 200px; border: 1px solid #333; border-radius: 10px; margin-bottom: 20px;'>";
        } else {
            // Use a default avatar or no image if photo doesn't exist
            $photoTag = "<div style='width: 200px; height: 200px; background: linear-gradient(135deg, #e74c3c, #f39c12); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold; margin-bottom: 20px;'>" . strtoupper(substr($u->username, 0, 1)) . "</div>";
        }
        
        $m->isHTML(true);
        $m->Subject = "Your payment has been confirmed";
        $m->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    $photoTag
                    <h2 style='color: #2c3e50; margin-bottom: 10px;'>Dear $u->username,</h2>
                    <h1 style='color: #e74c3c; margin-bottom: 20px;'>Your Order has been Confirmed!</h1>
                    <div style='background: linear-gradient(135deg, #e74c3c, #f39c12); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;'>
                        <h3 style='margin: 0;'>✅ Payment Successfully Processed</h3>
                    </div>
                </div>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;'>
                    <p style='font-size: 18px; color: #2c3e50; margin-bottom: 15px;'>
                        Thank you for shopping with us! Your order will be delivered to you within 
                        <span style='color: #27ae60; font-size: 22px; font-weight: bold;'>3-5 working days</span>.
                    </p>
                    <p style='color: #7f8c8d; margin: 0;'>You will receive a tracking number once your order is shipped.</p>
                </div>
                <div style='text-align: center; padding: 20px; background: linear-gradient(135deg, #e74c3c, #f39c12); color: white; border-radius: 10px;'>
                    <p style='font-size: 20px; font-weight: bold; margin: 0;'>From, CTRL + EAT Team</p>
                </div>
            </div>
        ";
        $m->send();

        ?>  <!-- This form is post to the checkedout.php after success validation -->
        <form id="redirectForm" action="./checkedout.php?user_id=<?= $user->user_id ?>" method="post">
            <input type="hidden" name="payment" value="<?= htmlspecialchars($payment) ?>">
            <input type="hidden" name="account_number" value="<?= htmlspecialchars($account_number) ?>">
            <input type="hidden" name="amount" value="<?= htmlspecialchars($total) ?>">
            <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
        </form>
        <script>
            document.getElementById('redirectForm').submit();  
        </script>

        <?php
        exit();
    }

}
?>

<link rel="stylesheet" href="/css/checkout-modern.css">

<!-- Hide old styles -->
<style>
header nav .menu { display: none !important; }
body .checkout { display: none !important; }
</style>

<!-- Modern Checkout Page -->
<div class="modern-checkout-page">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="./addressbook.php?user_id=<?= $_GET['user_id'] ?>" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Address
                </a>
            </div>
            <div class="hero-text">
                <div class="checkout-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        <polyline points="9,11 12,14 22,4"/>
                    </svg>
                </div>
                <h1>Checkout</h1>
                <p>Review your order and complete your purchase</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Shipping Address Section -->
            <div class="section-card">
                <div class="section-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Shipping Address
                    </h2>
                </div>
                <div class="address-display">
                    <div class="address-info">
                        <div class="address-text"><?= htmlspecialchars($address) ?></div>
                    </div>
                    <button type="button" class="change-address-btn" 
                            onclick="window.location.href='addressbook.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Change
                    </button>
                </div>
            </div>

            <!-- Order Details Section -->
            <div class="section-card">
                <div class="section-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        </svg>
                        Order Summary
                    </h2>
                </div>
                
                <div class="order-items">
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
                    <div class="order-item">
                        <div class="item-image">
                            <img src="/image/<?= $product->image ?>" alt="<?= htmlspecialchars($product->product_name) ?>">
                        </div>
                        <div class="item-details">
                            <h3><?= htmlspecialchars($product->product_name) ?></h3>
                            <div class="item-price">RM <?= number_format($product->price, 2) ?> × <?= $unit ?></div>
                        </div>
                        <div class="item-total">
                            <span class="subtotal">RM <?= number_format($subtotal, 2) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Total Items:</span>
                        <span><?= $count ?></span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total Amount:</span>
                        <span>RM <?= number_format($total, 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="section-card">
                <div class="section-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        Payment Information
                    </h2>
                </div>

                <form method="post" class="payment-form" action="?user_id=<?= $user->user_id ?>">
                    <div class="form-group">
                        <label for="payment">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            Payment Method
                        </label>
                        <?= html_select('payment', $payment_method, $payment, 'class="modern-select"') ?>
                        <?= err('payment') ?>
                    </div>

                    <div class="form-group">
                        <label for="account_number">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="2" y1="3" x2="22" y2="3"/>
                                <line x1="2" y1="9" x2="22" y2="9"/>
                                <line x1="2" y1="15" x2="22" y2="15"/>
                                <line x1="2" y1="21" x2="22" y2="21"/>
                            </svg>
                            Account Number
                        </label>
                        <?= html_text('account_number', $account_number, 'class="modern-input" placeholder="Enter your account number"') ?>
                        <?= err('account_number') ?>
                        <div class="input-help">Enter 10-12 digit account number</div>
                    </div>

                    <div class="payment-actions">
                        <button type="submit" class="confirm-btn" data-confirm>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <polyline points="9,12 11,14 15,10"/>
                            </svg>
                            Confirm Payment
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <div class="notice-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <div class="notice-content">
                    <h3>Secure Payment</h3>
                    <p>Your payment information is encrypted and secure. We never store your account details.</p>
                </div>
            </div>
        </div>
    </div>
</div>
