<?php

require_once '../connect.php';
require_once '../_base.php';

auth('Admin');

?>

<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/allbackgroundimg.css">
<link rel="stylesheet" href="../css/modern-header-footer.css">

<div id="info"><?= temp('info') ?></div>

<!-- Modern Admin Header -->
<header class="modern-admin-header">
    <div class="admin-header-container">
        <!-- Admin Logo Section -->
        <div class="modern-admin-logo-section">
            <a href="../admin/admin.php" class="modern-admin-logo-link">
                <div class="admin-logo-icon">
                    <img src="/icon/teddy-bear.png" alt="Admin Dashboard" class="modern-admin-logo-img">
                </div>
                <div class="admin-brand-text">
                    <span class="admin-brand-name">CTRL + EAT</span>
                    <span class="admin-brand-tagline">Admin Dashboard</span>
                </div>
            </a>
        </div>
        
        <!-- Admin Navigation -->
        <nav class="modern-admin-nav">
            <ul class="modern-admin-nav-list">
                <li class="modern-admin-nav-item">
                    <a href="../admin/admin.php" class="modern-admin-nav-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="modern-admin-nav-item">
                    <a href="../admin/product.php" class="modern-admin-nav-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5 3H3"/>
                            <circle cx="9" cy="20" r="1"/>
                            <circle cx="20" cy="20" r="1"/>
                        </svg>
                        <span>Products</span>
                    </a>
                </li>
                <li class="modern-admin-nav-item">
                    <a href="../admin/order.php" class="modern-admin-nav-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        </svg>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="modern-admin-nav-item">
                    <a href="../admin/member.php" class="modern-admin-nav-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Members</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Admin Actions -->
        <div class="modern-admin-actions">
            <a href="../admin/setting.php" class="modern-admin-nav-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v6"/>
                    <path d="m21 12-6-6m-6 6-6-6"/>
                </svg>
                <span>Settings</span>
            </a>
            <a href="../index.php" class="modern-admin-nav-link back-to-site">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                <span>Back to Site</span>
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="admin-mobile-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- Hide old admin header -->
<style>
    .admin-header { display: none !important; }
</style>

