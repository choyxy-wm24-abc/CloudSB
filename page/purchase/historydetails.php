<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

$user_id = $_SESSION['user_id'] ?? null;

$order_id = $_GET['order_id'];

$stm = $_db->prepare('
    SELECT *
    FROM `order` AS o
    JOIN orderlist AS ol ON o.order_id = ol.order_id
    JOIN product AS p ON ol.product_id = p.product_id
    WHERE o.user_id = ? && o.order_id = ?
    ORDER BY o.order_id DESC
');
$stm->execute([$user->user_id, $order_id]);
$arr = $stm->fetchAll();

// Get order summary info
$order_summary = null;
if (!empty($arr)) {
    $order_summary = $arr[0]; // First item contains order info
    $total_amount = array_sum(array_column($arr, 'subtotal'));
    $total_items = array_sum(array_column($arr, 'quantity'));
}

?>

<link rel="stylesheet" href="/css/historydetails-modern.css">

<!-- Hide old styles -->
<style>
header nav .menu { display: none !important; }
body .order_history { display: none !important; }
body h1.product_history { display: none !important; }
body hr { display: none !important; }
</style>

<!-- Modern History Details Page -->
<div class="modern-historydetails-page">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="./orderhistory.php?user_id=<?= $_GET['user_id'] ?>&cat=All%20Products" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to History
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
                <p>Order details not found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <?php if (empty($arr)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v4"/>
                        <path d="M12 16h.01"/>
                    </svg>
                </div>
                <h2>Order Not Found</h2>
                <p>The order details you're looking for could not be found or you don't have permission to view them.</p>
                <a href="./orderhistory.php?user_id=<?= $_GET['user_id'] ?>&cat=All%20Products" class="back-to-history-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    Back to Order History
                </a>
            </div>
            <?php else: ?>
            
            <!-- Order Summary Card -->
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

            <!-- Order Items -->
            <div class="order-items-card">
                <div class="items-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3"/>
                            <circle cx="9" cy="20" r="1"/>
                            <circle cx="20" cy="20" r="1"/>
                        </svg>
                        Ordered Items
                    </h2>
                </div>
                
                <div class="items-list">
                    <?php foreach ($arr as $index => $item): ?>
                    <div class="order-item">
                        <div class="item-image">
                            <img src="../../image/<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->product_name) ?>">
                        </div>
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item->product_name) ?></h3>
                            <div class="item-meta">
                                <div class="meta-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M7 7h.01"/>
                                        <path d="M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 2 12V7a5 5 0 0 1 5-5z"/>
                                    </svg>
                                    <span>Qty: <?= $item->quantity ?></span>
                                </div>
                                <div class="meta-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"/>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                    <span>RM <?= number_format($item->subtotal, 2) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="item-status">
                            <div class="status-badge delivered">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                                Delivered
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="timeline-card">
                <div class="timeline-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        Order Timeline
                    </h2>
                </div>
                
                <div class="timeline">
                    <div class="timeline-item completed">
                        <div class="timeline-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20,6 9,17 4,12"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3>Order Placed</h3>
                            <p><?= date('F d, Y \a\t g:i A', strtotime($order_summary->order_date)) ?></p>
                        </div>
                    </div>
                    
                    <div class="timeline-item completed">
                        <div class="timeline-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3>Payment Confirmed</h3>
                            <p><?= date('F d, Y \a\t g:i A', strtotime($order_summary->order_date . ' +5 minutes')) ?></p>
                        </div>
                    </div>
                    
                    <div class="timeline-item completed">
                        <div class="timeline-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 9V5a3 3 0 0 0-6 0v4"/>
                                <rect x="2" y="9" width="20" height="11" rx="2" ry="2"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3>Order Prepared</h3>
                            <p><?= date('F d, Y \a\t g:i A', strtotime($order_summary->order_date . ' +1 hour')) ?></p>
                        </div>
                    </div>
                    
                    <div class="timeline-item completed">
                        <div class="timeline-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="3" width="15" height="13"/>
                                <polygon points="16,8 20,8 23,11 23,16 16,16 16,8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3>Out for Delivery</h3>
                            <p><?= date('F d, Y \a\t g:i A', strtotime($order_summary->order_date . ' +2 hours')) ?></p>
                        </div>
                    </div>
                    
                    <div class="timeline-item completed">
                        <div class="timeline-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <h3>Delivered</h3>
                            <p><?= date('F d, Y \a\t g:i A', strtotime($order_summary->order_date . ' +3 hours')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
</div>

