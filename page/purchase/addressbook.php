<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

if (is_post()) {
    $address_name = req('address');

    if ($address_name == '') {
        $_err['address'] = 'Address Required';
    }

    if (!$_err) {
        $_SESSION['address_name'] = $address_name;
        header("Location: ./checkout.php?user_id=$user->user_id");
    }
       
}
?>

<link rel="stylesheet" href="/css/addressbook-modern.css">

<!-- Hide old styles -->
<style>
header nav .menu { display: none !important; }
body .checkout { display: none !important; }
</style>

<!-- Modern Addressbook Page -->
<div class="modern-addressbook-page">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="./cart.php?user_id=<?= $_GET['user_id'] ?>" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Cart
                </a>
            </div>
            <div class="hero-text">
                <div class="address-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <h1>Address Book</h1>
                <p>Select your shipping address to continue</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <?php
                $stm = $_db->prepare('SELECT address_id, address_name FROM address WHERE user_id = ?');
                $stm->execute([$user->user_id]);
                $add = $stm->fetchAll(PDO::FETCH_ASSOC);

                $addressbook = [];
                foreach ($add as $a) {
                    $addressbook[$a['address_name']] = $a['address_name']; 
                }
            ?>

            <?php if (empty($addressbook)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                        <circle cx="12" cy="10" r="3"/>
                        <path d="M12 2v8"/>
                        <path d="M8 6l8 8"/>
                        <path d="M16 6l-8 8"/>
                    </svg>
                </div>
                <h2>No Addresses Found</h2>
                <p>You haven't added any shipping addresses yet. Add your first address to continue with checkout.</p>
                <a href="add_address.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>" class="add-first-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v8"/>
                        <path d="M8 12h8"/>
                    </svg>
                    Add Your First Address
                </a>
            </div>
            <?php else: ?>
            <!-- Address Selection -->
            <div class="address-selection-card">
                <div class="card-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Select Shipping Address
                    </h2>
                    <p>Choose from your saved addresses</p>
                </div>

                <form method="post" class="address-form">
                    <div class="address-options">
                        <?php foreach ($addressbook as $address_name => $address_value): ?>
                        <div class="address-option">
                            <input type="radio" id="addr_<?= md5($address_name) ?>" name="address" value="<?= htmlspecialchars($address_value) ?>" required>
                            <label for="addr_<?= md5($address_name) ?>" class="address-card">
                                <div class="address-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </div>
                                <div class="address-details">
                                    <div class="address-text"><?= htmlspecialchars($address_name) ?></div>
                                </div>
                                <div class="radio-indicator">
                                    <div class="radio-dot"></div>
                                </div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?= err('address') ?>

                    <div class="form-actions">
                        <button type="submit" class="continue-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>
                            Continue to Checkout
                        </button>
                    </div>
                </form>
            </div>

            <!-- Add New Address -->
            <div class="add-address-card">
                <div class="add-content">
                    <div class="add-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v8"/>
                            <path d="M8 12h8"/>
                        </svg>
                    </div>
                    <div class="add-text">
                        <h3>Need a New Address?</h3>
                        <p>Add a new shipping address to your address book</p>
                    </div>
                    <a href="add_address.php?user_id=<?= urlencode($_GET['user_id'] ?? $_SESSION['user']->user_id ) ?>" class="add-address-btn">
                        Add New Address
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Address Management Tips -->
            <div class="tips-card">
                <h3>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    Address Tips
                </h3>
                <div class="tips-grid">
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Include complete address with postal code</span>
                    </div>
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Add landmarks for easier delivery</span>
                    </div>
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Save multiple addresses for convenience</span>
                    </div>
                    <div class="tip-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <span>Double-check spelling and details</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
