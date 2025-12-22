<?php

require_once '../connect.php';
require_once '../_base.php';
require_once '../admin/layout.php';

auth('Admin');

$user = 1;

$page = req('page', 1);

$order = $_db->query('SELECT * FROM `order` o JOIN user u ON u.user_id = o.user_id
                    ORDER BY o.order_id DESC')->fetchAll();

if ($page) {
    // Use pagination for full product list
    require_once '../lib/SimplePager.php';
    $o = new SimplePager('SELECT * FROM `order` o JOIN user u ON u.user_id = o.user_id
                        ORDER BY o.order_id DESC', [], 15, $page);
    $order = $o->result;
}

?>

<link rel="stylesheet" href="../css/admin-orders-modern.css">

<!-- Hero Section -->
<section class="orders-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="back-navigation">
                <a href="admin.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>
            <h1 class="hero-title">
                <span class="brand-highlight">Order Management</span>
            </h1>
            <p class="hero-subtitle">Monitor and manage customer orders</p>
        </div>
    </div>
</section>

<!-- Orders Section -->
<section class="orders-section">
    <div class="section-container">
        <div class="orders-header">
            <h2 class="section-title">Order Summary</h2>
            <div class="orders-count"><?= count($order) ?> orders found</div>
        </div>
        
        <?php if ($page): ?>
            <div class="pagination-top">
                <?= $o->html($user) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($order)): ?>
            <div class="orders-grid">
                <?php foreach ($order as $ord): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">
                                <span class="order-label">Order #</span>
                                <span class="order-number"><?= $ord->order_id ?></span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge completed">Completed</span>
                            </div>
                        </div>
                        
                        <div class="order-customer">
                            <div class="customer-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                </svg>
                            </div>
                            <div class="customer-info">
                                <span class="customer-label">Customer</span>
                                <span class="customer-name"><?= $ord->username ?></span>
                            </div>
                        </div>
                        
                        <div class="order-details">
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z"/>
                                    </svg>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Date</span>
                                    <span class="detail-value"><?= date('M j, Y', strtotime($ord->order_date)) ?></span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19,7H18V6A2,2 0 0,0 16,4H8A2,2 0 0,0 6,6V7H5A1,1 0 0,0 4,8V19A3,3 0 0,0 7,22H17A3,3 0 0,0 20,19V8A1,1 0 0,0 19,7M8,6H16V7H8V6M18,19A1,1 0 0,1 17,20H7A1,1 0 0,1 6,19V9H18V19Z"/>
                                    </svg>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Items</span>
                                    <span class="detail-value"><?= $ord->tquantity ?> items</span>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                                    </svg>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Total</span>
                                    <span class="detail-value total-price">RM <?= sprintf('%.2f', $ord->tprice) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-actions">
                            <a href="../admin/user_orderdetails.php?user_id=<?= $ord->user_id ?>&order_id=<?= $ord->order_id ?>" class="details-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <div class="no-orders-icon">📋</div>
                <h3>No Orders Found</h3>
                <p>There are currently no orders in the system.</p>
            </div>
        <?php endif; ?>
        
        <?php if ($page && count($order) > 0): ?>
            <div class="pagination-bottom">
                <?= $o->html($user) ?>
            </div>
        <?php endif; ?>
    </div>
</section>