<?php

require_once '../../connect.php';
require_once '../../_base.php';
require_once '../../_head.php';

?>

<link rel="stylesheet" href="../../css/about-modern.css">

<!-- Hero Section -->
<section class="about-hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">
                Welcome to <span class="highlight">CTRL + EAT</span>
            </h1>
            <p class="hero-subtitle">
                Your trusted partner for delicious food delivered fresh to your doorstep
            </p>
            <div class="hero-badge">
                <span class="badge-icon">🍽️</span>
                <span class="badge-text">Premium Food Delivery Experience</span>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="section-container">
        <div class="about-content">
            <div class="about-card">
                <div class="card-header">
                    <h2 class="section-title">About Us</h2>
                    <div class="title-decoration"></div>
                </div>
                <div class="card-content">
                    <p class="intro-text">
                        Welcome to CTRL + EAT, your premier destination for quality food delivery services! 🍽️✨
                    </p>
                    <p class="description-text">
                        At CTRL + EAT, we believe that great food should be accessible to everyone. That's why we've partnered with the finest local restaurants and chefs to bring you a diverse menu of mouth-watering dishes. From hearty burgers and crispy fries to fresh wraps and healthy options, every meal is prepared with care and delivered with speed to satisfy your cravings.
                    </p>
                    
                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">🍔</div>
                            <h3>Gourmet Burgers</h3>
                            <p>Juicy, flavorful, and satisfying</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">🍟</div>
                            <h3>Crispy Fries</h3>
                            <p>Golden and perfectly seasoned</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">🌯</div>
                            <h3>Fresh Wraps</h3>
                            <p>Healthy and delicious options</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">🥤</div>
                            <h3>Refreshing Drinks</h3>
                            <p>Perfect beverage pairings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="section-container">
        <div class="contact-content">
            <div class="contact-card">
                <div class="card-header">
                    <h2 class="section-title">Contact Us!</h2>
                    <div class="title-decoration"></div>
                </div>
                <div class="card-content">
                    <p class="contact-intro">
                        For inquiries, feedback, or to place a special order, reach out to us:
                    </p>
                    
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,5.11 21.1,4 20,4Z"/>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h3>Email</h3>
                                <a href="mailto:hello@ctrleat.com" class="contact-link">hello@ctrleat.com</a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h3>Phone</h3>
                                <a href="tel:+60123456789" class="contact-link">+60 12-345 6789</a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h3>Address</h3>
                                <div class="address">
                                    <p>CTRL + EAT Food Hub</p>
                                    <p>Jalan Bukit Bintang</p>
                                    <p>50200 Kuala Lumpur, Malaysia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cta-section">
                        <a href="../purchase/shopnow.php" class="cta-button">
                            Order Now & Get Delivered
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
    include '../../_foot.php'
?>