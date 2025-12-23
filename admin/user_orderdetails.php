<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM orderlist ol JOIN product p ON ol.product_id = p.product_id WHERE order_id = ?');
    $stm->execute([$order_id]);
    $or = $stm->fetch();

    if ($or) {
        extract((array)$or);
        $_SESSION['image'] = $or->image;
    }
}

// Get all order items
$stm = $_db->prepare('SELECT * FROM orderlist ol JOIN product p ON ol.product_id = p.product_id WHERE order_id = ?');
$stm->execute([$order_id]);
$orders = $stm->fetchAll();

// Get order summary
$order_summary = null;
if (!empty($orders)) {
    $total_amount = array_sum(array_column($orders, 'subtotal'));
    $total_items = array_sum(array_column($orders, 'quantity'));
    
    // Get order info
    $stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ?');
    $stm->execute([$order_id]);
    $order_summary = $stm->fetch();
}

?>

<link rel="stylesheet" href="../css/admin-order-details-modern.css">

<!-- Hide old styles -->
<style>
.title { display: none !important; }
main .order { display: none !important; }
button[onclick="window.history.back()"] { display: none !important; }
</style>

<!-- Modern Admin Order Details Page -->
<div class="modern-admin-order-details">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="javascript:history.back()" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to User Details
                </a>
            </div>
            <div class="hero-text">
                <div class="order-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        <path d="M12 11v6"/>
                        <path d="M12 17l-2-2"/>
                        <path d="M12 17l2-2"/>
                    </svg>
                </div>
                <h1>Order Details</h1>
                <?php if ($order_summary): ?>
                <p>Order #<?= htmlspecialchars($order_id) ?> • <?= date('M d, Y', strtotime($order_summary->order_date)) ?></p>
                <?php else: ?>
                <p>Order details and item breakdown</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4"/>
                        <path d="M12 16h.01"/>
                    </svg>
                </div>
                <h2>No Order Items Found</h2>
                <p>This order appears to be empty or the order ID is invalid.</p>
                <a href="javascript:history.back()" class="back-to-orders-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Orders
                </a>
            </div>
            <?php else: ?>
            
            <!-- Order Summary Card -->
            <?php if ($order_summary): ?>
            <div class="order-summary-card">
                <div class="summary-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        Order Summary
                    </h2>
                </div>
                <div class="summary-content">
                    <div class="summary-item">
                        <span class="label">Order ID:</span>
                        <span class="value">#<?= htmlspecialchars($order_id) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Order Date:</span>
                        <span class="value"><?= date('F d, Y \a\t g:i A', strtotime($order_summary->order_date)) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Total Items:</span>
                        <span class="value"><?= $total_items ?> item<?= $total_items > 1 ? 's' : '' ?></span>
                    </div>
                    <div class="summary-item total">
                        <span class="label">Total Amount:</span>
                        <span class="value">RM <?= number_format($total_amount, 2) ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Order Items Card -->
            <div class="order-items-card">
                <div class="items-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3"/>
                            <circle cx="9" cy="20" r="1"/>
                            <circle cx="20" cy="20" r="1"/>
                        </svg>
                        Order Items (<?= count($orders) ?> items)
                    </h2>
                </div>
                
                <div class="items-list">
                    <?php foreach ($orders as $index => $item): ?>
                    <div class="order-item">
                        <div class="item-image">
                            <img src="../image/<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->product_name) ?>">
                        </div>
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item->product_name) ?></h3>
                            <div class="item-meta">
                                <div class="meta-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M7 7h.01"/>
                                        <path d="M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 2 12V7a5 5 0 0 1 5-5z"/>
                                    </svg>
                                    <span>Product ID: <?= $item->product_id ?></span>
                                </div>
                                <div class="meta-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"/>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                    <span>RM <?= number_format($item->price_per_unit, 2) ?> per unit</span>
                                </div>
                            </div>
                        </div>
                        <div class="item-quantity">
                            <div class="quantity-badge">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M7 7h.01"/>
                                    <path d="M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 2 12V7a5 5 0 0 1 5-5z"/>
                                </svg>
                                Qty: <?= $item->quantity ?>
                            </div>
                        </div>
                        <div class="item-total">
                            <div class="subtotal">RM <?= number_format($item->subtotal, 2) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="order-actions-card">
                <div class="actions-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M12 1v6m0 6v6"/>
                            <path d="m21 12-6-6m-6 6-6-6"/>
                        </svg>
                        Quick Actions
                    </h2>
                </div>
                <div class="actions-grid">
                    <button class="action-btn print-btn" onclick="window.print()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 6,2 18,2 18,9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        Print Order
                    </button>
                    <button class="action-btn export-btn" onclick="exportOrder()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7,10 12,15 17,10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Export Data
                    </button>
                    <a href="javascript:history.back()" class="action-btn back-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 19-7-7 7-7"/>
                            <path d="m19 12H5"/>
                        </svg>
                        Back to User
                    </a>
                </div>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function exportOrder() {
    // Simple CSV export functionality
    const orderData = [
        ['Order ID', 'Product ID', 'Product Name', 'Price Per Unit', 'Quantity', 'Subtotal'],
        <?php foreach ($orders as $item): ?>
        ['<?= $order_id ?>', '<?= $item->product_id ?>', '<?= addslashes($item->product_name) ?>', '<?= $item->price_per_unit ?>', '<?= $item->quantity ?>', '<?= $item->subtotal ?>'],
        <?php endforeach; ?>
    ];
    
    const csvContent = orderData.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'order_<?= $order_id ?>_details.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>