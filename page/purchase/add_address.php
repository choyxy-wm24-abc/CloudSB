<?php
require_once '../../connect.php';
require_once '../../_base.php';
auth('Member');

if (is_post()) {
    $newaddress = req('newaddress');

    if ($newaddress == '') {
        $_err['newaddress'] = 'Required';
    } else {
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM address
            WHERE address_name = ? AND user_id= ?
        ');
        $stm->execute([$newaddress, $user->user_id]);

        if ($stm->fetchColumn() > 0) {
            $_err['newaddress'] = 'Address already Exist in your account';
        }
    }

    if (!$_err) {
        $stm = $_db->prepare('INSERT INTO address (address_name, user_id) VALUES (?, ?)');
        $stm->execute([$newaddress, $user->user_id]);
        header("Location: ./addressbook.php?user_id=$user->user_id");
        exit;
    }

}
?>

<link rel="stylesheet" href="/css/add-address-modern.css">

<!-- Hide old styles -->
<style>
header nav .menu { display: none !important; }
body .checkout { display: none !important; }
</style>

<!-- Modern Add Address Page -->
<div class="modern-add-address-page">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="./addressbook.php?user_id=<?= $_GET['user_id'] ?>" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Address Book
                </a>
            </div>
            <div class="hero-text">
                <div class="address-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                        <circle cx="12" cy="10" r="3"/>
                        <path d="M12 8v4"/>
                        <path d="M8 10h8"/>
                    </svg>
                </div>
                <h1>Add New Address</h1>
                <p>Add a new shipping address to your address book</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Add Address Form Card -->
            <div class="add-address-card">
                <div class="card-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        New Shipping Address
                    </h2>
                    <p>Enter your complete address details below</p>
                </div>

                <form method="post" class="address-form">
                    <div class="form-group">
                        <label for="newaddress">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            Complete Address
                        </label>
                        <?= html_textarea('newaddress', 'class="modern-textarea" rows="4" placeholder="Enter your complete address including street, city, state, and postal code..."') ?>
                        <?= err('newaddress') ?>
                        <div class="address-help">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            <span>Include landmarks and detailed directions for easier delivery</span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20,6 9,17 4,12"/>
                            </svg>
                            Add Address
                        </button>
                        <a href="./addressbook.php?user_id=<?= $user->user_id ?>" class="cancel-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Address Tips Card -->
            <div class="tips-card">
                <div class="tips-header">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        Address Guidelines
                    </h3>
                </div>
                <div class="tips-grid">
                    <div class="tip-item">
                        <div class="tip-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20,6 9,17 4,12"/>
                            </svg>
                        </div>
                        <div class="tip-content">
                            <h4>Complete Details</h4>
                            <p>Include house number, street name, area, and postal code</p>
                        </div>
                    </div>
                    
                    <div class="tip-item">
                        <div class="tip-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20,6 9,17 4,12"/>
                            </svg>
                        </div>
                        <div class="tip-content">
                            <h4>Add Landmarks</h4>
                            <p>Mention nearby landmarks for easier navigation</p>
                        </div>
                    </div>
                    
                    <div class="tip-item">
                        <div class="tip-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20,6 9,17 4,12"/>
                            </svg>
                        </div>
                        <div class="tip-content">
                            <h4>Clear Instructions</h4>
                            <p>Provide clear directions for delivery personnel</p>
                        </div>
                    </div>
                    
                    <div class="tip-item">
                        <div class="tip-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20,6 9,17 4,12"/>
                            </svg>
                        </div>
                        <div class="tip-content">
                            <h4>Unique Names</h4>
                            <p>Use descriptive names to distinguish between addresses</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Examples Card -->
            <div class="examples-card">
                <div class="examples-header">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        Address Examples
                    </h3>
                </div>
                <div class="examples-list">
                    <div class="example-item">
                        <div class="example-label">Home Address:</div>
                        <div class="example-text">123 Jalan Bukit Bintang, Bukit Bintang, 55100 Kuala Lumpur, Malaysia (Near Pavilion Mall)</div>
                    </div>
                    <div class="example-item">
                        <div class="example-label">Office Address:</div>
                        <div class="example-text">Level 15, Menara ABC, No. 456 Jalan Ampang, 50450 Kuala Lumpur, Malaysia (Opposite LRT Station)</div>
                    </div>
                    <div class="example-item">
                        <div class="example-label">Apartment:</div>
                        <div class="example-text">Unit 12-3A, Residensi Vista, Jalan PJU 1A/7A, Ara Damansara, 47301 Petaling Jaya, Selangor</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>