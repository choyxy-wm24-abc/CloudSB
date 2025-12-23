<?php

require_once '../connect.php';
require_once '../_base.php';

auth('Admin');

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

// Handle POST requests FIRST (before any HTML output)
if (is_post()) {
    $action = req('action');
    
    if ($action === 'update_status') {
        $status = req('status');

        $stm = $_db->prepare('
            UPDATE user
            SET status = ?
            WHERE user_id = ?
        ');
        $stm->execute([$status, $user_id]);

        temp('info', 'User Status updated');
        redirect('/admin/member.php');
    }
    elseif ($action === 'delete_user') {
        // First, delete all related orders and orderlist entries
        $stm = $_db->prepare('DELETE ol FROM orderlist ol JOIN `order` o ON ol.order_id = o.order_id WHERE o.user_id = ?');
        $stm->execute([$user_id]);
        
        // Delete orders
        $stm = $_db->prepare('DELETE FROM `order` WHERE user_id = ?');
        $stm->execute([$user_id]);
        
        // Delete user
        $stm = $_db->prepare('DELETE FROM user WHERE user_id = ?');
        $stm->execute([$user_id]);

        temp('info', 'User deleted successfully');
        redirect('/admin/member.php');
    }
}

// Handle GET requests
if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM user u WHERE user_id = ?');
    $stm->execute([$user_id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/admin/member.php');
    }

    extract((array)$u);
    $_SESSION['photo'] = $u->photo;
}

// Include layout AFTER processing all logic
require_once '../admin/layout.php';

// Get user orders
$stm = $_db->prepare('SELECT * FROM `order` WHERE user_id = ? ORDER BY order_date DESC');
$stm->execute([$user_id]);
$orders = $stm->fetchAll();

?>

<link rel="stylesheet" href="../css/admin-user-details-modern.css">

<!-- Hide old styles -->
<style>
.title { display: none !important; }
.layout { display: none !important; }
main .order { display: none !important; }
button[onclick*="member.php"] { display: none !important; }
</style>

<!-- Modern Admin User Details Page -->
<div class="modern-admin-user-details">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="back-nav">
                <a href="../admin/member.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="m19 12H5"/>
                    </svg>
                    Back to Members
                </a>
            </div>
            <div class="hero-text">
                <div class="user-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h1>User Details</h1>
                <p>Manage user information and view order history</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- User Information Card -->
            <div class="user-info-card">
                <div class="card-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        User Information
                    </h2>
                </div>
                
                <div class="user-layout">
                    <!-- User Photo -->
                    <div class="user-photo-section">
                        <div class="photo-container">
                            <img src="../photos/<?= htmlspecialchars($photo) ?>" alt="User Photo" class="user-photo">
                            <div class="photo-overlay">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="user-details-section">
                        <div class="detail-item">
                            <label>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                Username
                            </label>
                            <div class="detail-value"><?= htmlspecialchars($username) ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <label>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                                Email
                            </label>
                            <div class="detail-value"><?= htmlspecialchars($email) ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <label>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 2v4"/>
                                    <path d="M16 2v4"/>
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <path d="M3 10h18"/>
                                </svg>
                                Age
                            </label>
                            <div class="detail-value"><?= htmlspecialchars($age) ?> years old</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Management Card -->
            <div class="status-card">
                <div class="card-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        Account Status
                    </h2>
                </div>
                
                <form method="post" class="status-form">
                    <input type="hidden" name="action" value="update_status">
                    <div class="status-options">
                        <?= html_radios('status', $_status, '', 'class="status-radio"') ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="update-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20,6 9,17 4,12"/>
                            </svg>
                            Update Status
                        </button>
                    </div>
                </form>
                
                <!-- Delete User Section -->
                <div class="danger-zone">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        Danger Zone
                    </h3>
                    <p>Permanently delete this user and all associated data. This action cannot be undone.</p>
                    <form method="post" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user? This will also delete all their orders and cannot be undone.')">
                        <input type="hidden" name="action" value="delete_user">
                        <button type="submit" class="delete-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3,6 5,6 21,6"/>
                                <path d="M19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1 2-2h4a2,2 0 0,1 2,2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                            Delete User
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order History Card -->
            <div class="order-history-card">
                <div class="card-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        </svg>
                        Order History (<?= count($orders) ?> orders)
                    </h2>
                </div>
                
                <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <div class="no-orders-icon">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 8v4"/>
                            <path d="M12 16h.01"/>
                        </svg>
                    </div>
                    <h3>No Orders Found</h3>
                    <p>This user hasn't placed any orders yet.</p>
                </div>
                <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <div class="order-id">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M7 7h.01"/>
                                    <path d="M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 2 12V7a5 5 0 0 1 5-5z"/>
                                </svg>
                                Order #<?= $order->order_id ?>
                            </div>
                            <div class="order-date">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 2v4"/>
                                    <path d="M16 2v4"/>
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <path d="M3 10h18"/>
                                </svg>
                                <?= date('M d, Y', strtotime($order->order_date)) ?>
                            </div>
                        </div>
                        <div class="order-stats">
                            <div class="stat-item">
                                <span class="stat-label">Items:</span>
                                <span class="stat-value"><?= $order->tquantity ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Total:</span>
                                <span class="stat-value">RM <?= number_format($order->tprice, 2) ?></span>
                            </div>
                        </div>
                        <div class="order-actions">
                            <a href="../admin/user_orderdetails.php?user_id=<?= $user_id ?>&order_id=<?= $order->order_id ?>" class="details-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                                Details
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>