<?php
require_once '../../connect.php';
require_once '../../_base.php';

auth('Member');

$user_id = $_SESSION['user_id'] ?? null;

$stm = $_db->prepare('
    SELECT *
    FROM `order`
    WHERE user_id = ?
    ORDER BY order_id DESC
');
$stm->execute([$user->user_id]);
$arr = $stm->fetchAll();

?>

<link rel="stylesheet" href="../../css/orderhistory-modern.css">

<!-- Hero Section -->
<section class="history-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="back-navigation">
                <a href="./shopnow.php?user_id=<?= $_GET['user_id'] ?>" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    <span>Back to Shop</span>
                </a>
            </div>
            <h1 class="hero-title">
                <span class="brand-highlight">My Order History</span>
            </h1>
            <p class="hero-subtitle">Track your delicious food journey</p>
        </div>
    </div>
</section>

<!-- Order History Section -->
<section class="history-section">
    <div class="section-container">
        <?php if (empty($arr)): ?>
            <!-- Empty History State -->
            <div class="empty-history">
                <div class="empty-history-icon">📋</div>
                <h2>No Orders Yet</h2>
                <p>You haven't placed any orders yet. Start exploring our delicious menu!</p>
                <a href="./shopnow.php?user_id=<?= $_GET['user_id'] ?>" class="shop-now-btn">
                    Start Shopping
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </a>
            </div>
        <?php else: ?>
            <!-- Order History Content -->
            <div class="history-content">
                <div class="history-header">
                    <h2 class="section-title">Your Orders</h2>
                    <div class="orders-count"><?= count($arr) ?> orders found</div>
                </div>
                
                <div class="orders-grid">
                    <?php foreach ($arr as $o): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-id">
                                    <span class="order-label">Order #</span>
                                    <span class="order-number"><?= $o->order_id ?></span>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-completed">Completed</span>
                                </div>
                            </div>
                            
                            <div class="order-details">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19,7H18V6A2,2 0 0,0 16,4H8A2,2 0 0,0 6,6V7H5A1,1 0 0,0 4,8V19A3,3 0 0,0 7,22H17A3,3 0 0,0 20,19V8A1,1 0 0,0 19,7M8,6H16V7H8V6M18,19A1,1 0 0,1 17,20H7A1,1 0 0,1 6,19V9H18V19Z"/>
                                        </svg>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Items</span>
                                        <span class="detail-value"><?= $o->tquantity ?> items</span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                                        </svg>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Total</span>
                                        <span class="detail-value total-price">RM <?= sprintf('%.2f', $o->tprice) ?></span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z"/>
                                        </svg>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Date</span>
                                        <span class="detail-value"><?= date('M j, Y', strtotime($o->order_date)) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="order-actions">
                                <a href="../purchase/historydetails.php?user_id=<?= $user_id ?>&order_id=<?= $o->order_id ?>" class="details-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                                    </svg>
                                    View Details
                                </a>
                                
                                <button class="reorder-btn" onclick="window.location.href='./shopnow.php?user_id=<?= $_GET['user_id'] ?>'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17,18C17.56,18 18,18.44 18,19C18,19.56 17.56,20 17,20C16.44,20 16,19.56 16,19C16,18.44 16.44,18 17,18M1,2V4H3L6.6,11.59L5.24,14.04C5.09,14.32 5,14.65 5,15C5,16.1 5.9,17 7,17H19V15H7.42C7.28,15 7.17,14.89 7.17,14.75L7.2,14.63L8.1,13H15.55C16.3,13 16.96,12.58 17.3,11.97L20.88,5H5.21L4.27,3H1M7,18C7.56,18 8,18.44 8,19C8,19.56 7.56,20 7,20C6.44,20 6,19.56 6,19C6,18.44 6.44,18 7,18Z"/>
                                    </svg>
                                    Order Again
                                </button>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>