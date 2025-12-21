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
?>


<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/admin-dashboard.css">

<div class="layout">
    <div class="admin-cards">
        <a href="/admin/product.php" class="admin-card">Product</a>
        <a href="/admin/order.php" class="admin-card">Order</a>
        <a href="/admin/member.php" class="admin-card">Member</a>
        <a href="/admin/setting.php" class="admin-card">Setting</a>
    </div>
</div>

<div class="charts-container">
    <div class="chart-card">
        <h2>Top 3 Users by Orders</h2>
        <canvas id="userChart"></canvas>
    </div>

    <div class="chart-card">
        <h2>Top 3 Products by Sales</h2>
        <canvas id="productChart"></canvas>
    </div>

    <div class="chart-card">
        <h2>Top 3 Orders by Total Price</h2>
        <canvas id="orderChart"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const userChart = new Chart(document.getElementById('userChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($top_users, 'username')) ?>,
        datasets: [{
            label: 'Orders',
            data: <?= json_encode(array_column($top_users, 'total_orders')) ?>,
            backgroundColor: '#6a11cb'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

const productChart = new Chart(document.getElementById('productChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($top_products, 'product_name')) ?>,
        datasets: [{
            label: 'Quantity Sold',
            data: <?= json_encode(array_column($top_products, 'total_quantity')) ?>,
            backgroundColor: '#2575fc'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

const orderChart = new Chart(document.getElementById('orderChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_map(fn($o) => 'Order #' . $o->order_id, $top_orders)) ?>,
        datasets: [{
            label: 'Total Price (RM)',
            data: <?= json_encode(array_column($top_orders, 'tprice')) ?>,
            backgroundColor: '#ff6a00'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>

