<?php
require_once '../connect.php';
require_once '../_base.php';

require_once '../admin/layout.php';
auth('Admin');

// Fetch top 3 users by number of orders
$stm = $_db->query('
    SELECT u.username, COUNT(o.order_id) AS total_orders
    FROM user u
    JOIN `order` o ON u.user_id = o.user_id
    GROUP BY u.user_id
    ORDER BY total_orders DESC
    LIMIT 3
');
$top_users = $stm->fetchAll();

// Fetch top 3 products by quantity sold
$stm = $_db->query('
    SELECT p.product_name, SUM(ol.quantity) AS total_quantity
    FROM orderlist ol
    JOIN product p ON ol.product_id = p.product_id
    GROUP BY p.product_id
    ORDER BY total_quantity DESC
    LIMIT 3
');
$top_products = $stm->fetchAll();

// Fetch top 3 orders by total price
$stm = $_db->query('
    SELECT o.order_id, o.tprice
    FROM `order` o
    ORDER BY o.tprice DESC
    LIMIT 3
');
$top_orders = $stm->fetchAll();

// Get dashboard stats
$total_users = $_db->query('SELECT COUNT(*) FROM user WHERE role = "Member"')->fetchColumn();
$total_products = $_db->query('SELECT COUNT(*) FROM product')->fetchColumn();
$total_orders = $_db->query('SELECT COUNT(*) FROM `order`')->fetchColumn();
$total_revenue = $_db->query('SELECT SUM(tprice) FROM `order`')->fetchColumn();
?>

<link rel="stylesheet" href="../css/admin-modern.css">

<!-- Fix header positioning -->
<style>
/* Ensure proper header positioning */
body {
    margin: 0 !important;
    padding: 0 !important;
}

.modern-admin-header {
    position: sticky !important;
    top: 0 !important;
    z-index: 1000 !important;
    margin: 0 !important;
    width: 100% !important;
}

/* Remove any top margins from first elements */
.admin-hero {
    margin-top: 0 !important;
}

/* Hide any conflicting elements */
.admin-header {
    display: none !important;
}
</style>

<!-- Hero Section -->
<section class="admin-hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="brand-highlight">Admin Dashboard</span>
            </h1>
            <p class="hero-subtitle">Manage your food delivery platform with ease</p>
            
            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_users) ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19,7H18V6A2,2 0 0,0 16,4H8A2,2 0 0,0 6,6V7H5A1,1 0 0,0 4,8V19A3,3 0 0,0 7,22H17A3,3 0 0,0 20,19V8A1,1 0 0,0 19,7M8,6H16V7H8V6M18,19A1,1 0 0,1 17,20H7A1,1 0 0,1 6,19V9H18V19Z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_products) ?></div>
                        <div class="stat-label">Products</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17,18C17.56,18 18,18.44 18,19C18,19.56 17.56,20 17,20C16.44,20 16,19.56 16,19C16,18.44 16.44,18 17,18M1,2V4H3L6.6,11.59L5.24,14.04C5.09,14.32 5,14.65 5,15C5,16.1 5.9,17 7,17H19V15H7.42C7.28,15 7.17,14.89 7.17,14.75L7.2,14.63L8.1,13H15.55C16.3,13 16.96,12.58 17.3,11.97L20.88,5H5.21L4.27,3H1M7,18C7.56,18 8,18.44 8,19C8,19.56 7.56,20 7,20C6.44,20 6,19.56 6,19C6,18.44 6.44,18 7,18Z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= number_format($total_orders) ?></div>
                        <div class="stat-label">Orders</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">RM <?= number_format($total_revenue, 2) ?></div>
                        <div class="stat-label">Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Navigation Section -->
<section class="admin-navigation">
    <div class="section-container">
        <div class="nav-header">
            <h2 class="section-title">Quick Actions</h2>
            <div class="title-decoration"></div>
        </div>
        
        <div class="admin-nav-grid">
            <a href="/admin/product.php" class="admin-nav-card">
                <div class="nav-card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,7H18V6A2,2 0 0,0 16,4H8A2,2 0 0,0 6,6V7H5A1,1 0 0,0 4,8V19A3,3 0 0,0 7,22H17A3,3 0 0,0 20,19V8A1,1 0 0,0 19,7M8,6H16V7H8V6M18,19A1,1 0 0,1 17,20H7A1,1 0 0,1 6,19V9H18V19Z"/>
                    </svg>
                </div>
                <div class="nav-card-content">
                    <h3>Products</h3>
                    <p>Manage menu items and inventory</p>
                </div>
                <div class="nav-card-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </div>
            </a>
            
            <a href="/admin/order.php" class="admin-nav-card">
                <div class="nav-card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,18C17.56,18 18,18.44 18,19C18,19.56 17.56,20 17,20C16.44,20 16,19.56 16,19C16,18.44 16.44,18 17,18M1,2V4H3L6.6,11.59L5.24,14.04C5.09,14.32 5,14.65 5,15C5,16.1 5.9,17 7,17H19V15H7.42C7.28,15 7.17,14.89 7.17,14.75L7.2,14.63L8.1,13H15.55C16.3,13 16.96,12.58 17.3,11.97L20.88,5H5.21L4.27,3H1M7,18C7.56,18 8,18.44 8,19C8,19.56 7.56,20 7,20C6.44,20 6,19.56 6,19C6,18.44 6.44,18 7,18Z"/>
                    </svg>
                </div>
                <div class="nav-card-content">
                    <h3>Orders</h3>
                    <p>View and manage customer orders</p>
                </div>
                <div class="nav-card-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </div>
            </a>
            
            <a href="/admin/member.php" class="admin-nav-card">
                <div class="nav-card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="nav-card-content">
                    <h3>Members</h3>
                    <p>Manage user accounts and profiles</p>
                </div>
                <div class="nav-card-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </div>
            </a>
            
            <a href="/admin/setting.php" class="admin-nav-card">
                <div class="nav-card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                    </svg>
                </div>
                <div class="nav-card-content">
                    <h3>Settings</h3>
                    <p>Configure system preferences</p>
                </div>
                <div class="nav-card-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Analytics Section -->
<section class="analytics-section">
    <div class="section-container">
        <div class="analytics-header">
            <h2 class="section-title">Analytics Overview</h2>
            <div class="title-decoration"></div>
        </div>
        
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Top Users by Orders</h3>
                    <div class="chart-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                        </svg>
                    </div>
                </div>
                <div class="chart-content">
                    <canvas id="userChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Top Products by Sales</h3>
                    <div class="chart-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19,7H18V6A2,2 0 0,0 16,4H8A2,2 0 0,0 6,6V7H5A1,1 0 0,0 4,8V19A3,3 0 0,0 7,22H17A3,3 0 0,0 20,19V8A1,1 0 0,0 19,7M8,6H16V7H8V6M18,19A1,1 0 0,1 17,20H7A1,1 0 0,1 6,19V9H18V19Z"/>
                        </svg>
                    </div>
                </div>
                <div class="chart-content">
                    <canvas id="productChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Top Orders by Value</h3>
                    <div class="chart-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                        </svg>
                    </div>
                </div>
                <div class="chart-content">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart configuration with modern styling
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { 
        legend: { display: false }
    },
    scales: {
        y: { 
            beginAtZero: true,
            grid: {
                color: 'rgba(231, 76, 60, 0.1)'
            },
            ticks: {
                color: '#7f8c8d'
            }
        },
        x: {
            grid: {
                color: 'rgba(231, 76, 60, 0.1)'
            },
            ticks: {
                color: '#7f8c8d'
            }
        }
    }
};

const userChart = new Chart(document.getElementById('userChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($top_users, 'username')) ?>,
        datasets: [{
            label: 'Orders',
            data: <?= json_encode(array_column($top_users, 'total_orders')) ?>,
            backgroundColor: 'rgba(231, 76, 60, 0.8)',
            borderColor: '#e74c3c',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: chartOptions
});

const productChart = new Chart(document.getElementById('productChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($top_products, 'product_name')) ?>,
        datasets: [{
            label: 'Quantity Sold',
            data: <?= json_encode(array_column($top_products, 'total_quantity')) ?>,
            backgroundColor: 'rgba(243, 156, 18, 0.8)',
            borderColor: '#f39c12',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: chartOptions
});

const orderChart = new Chart(document.getElementById('orderChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_map(fn($o) => 'Order #' . $o->order_id, $top_orders)) ?>,
        datasets: [{
            label: 'Total Price (RM)',
            data: <?= json_encode(array_column($top_orders, 'tprice')) ?>,
            backgroundColor: 'rgba(52, 152, 219, 0.8)',
            borderColor: '#3498db',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: chartOptions
});
</script>