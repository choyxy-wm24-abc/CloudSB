</main>

<footer class="galaxy-footer">
    <div class="footer-content">
        <div class="footer-brand">
            <img src="/icon/teddy-bear.png" alt="Logo" class="footer-logo">
            <span class="footer-text">&copy; 2024 CTRL + EAT. All rights reserved.</span>
        </div>
        <div class="footer-links">
            <a href="/page/user/about.php" class="footer-link">About</a>
            <a href="/page/purchase/shopnow.php" class="footer-link">Shop</a>
            <a href="#" class="footer-link">Contact</a>
        </div>
    </div>
</footer>

<style>
    /* Ensure body and html take full height */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    /* Main content wrapper */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    main {
        flex: 1;
    }

    /* Galaxy Footer Styles */
    .galaxy-footer {
        background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
        margin-top: auto;
        padding: 2rem 0;
        position: relative;
        overflow: hidden;
        box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Animated galaxy background for footer */
    .galaxy-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 10% 20%, rgba(255, 107, 107, 0.2) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(254, 202, 87, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 90% 10%, rgba(255, 159, 243, 0.2) 0%, transparent 50%);
        animation: galaxyFloat 20s ease-in-out infinite reverse;
        z-index: 0;
    }

    /* Twinkling stars for footer */
    .galaxy-footer::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(1px 1px at 20px 20px, rgba(255, 255, 255, 0.6), transparent),
            radial-gradient(1px 1px at 60px 50px, rgba(255, 255, 255, 0.4), transparent),
            radial-gradient(1px 1px at 100px 30px, rgba(255, 255, 255, 0.7), transparent),
            radial-gradient(1px 1px at 140px 60px, rgba(255, 255, 255, 0.5), transparent);
        background-repeat: repeat;
        background-size: 160px 80px;
        animation: twinkle 6s ease-in-out infinite alternate;
        z-index: 1;
    }

    .footer-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .footer-logo {
        height: 32px;
        width: 32px;
        border-radius: 50%;
        filter: drop-shadow(0 4px 8px rgba(255, 255, 255, 0.3));
        animation: logoGlow 4s ease-in-out infinite alternate;
    }

    .footer-text {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        font-weight: 500;
        background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.7) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .footer-links {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .footer-link {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .footer-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .footer-link:hover::before {
        left: 100%;
    }

    .footer-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
    }

    /* Responsive footer */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
            padding: 0 1rem;
        }
        
        .footer-brand {
            flex-direction: column;
            gap: 0.8rem;
        }
        
        .footer-text {
            font-size: 0.9rem;
        }
        
        .footer-logo {
            height: 28px;
            width: 28px;
        }
        
        .footer-links {
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .footer-link {
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
        }
    }

    @media (max-width: 480px) {
        .galaxy-footer {
            padding: 1.5rem 0;
        }
        
        .footer-content {
            padding: 0 0.8rem;
        }
        
        .footer-text {
            font-size: 0.85rem;
        }
        
        .footer-logo {
            height: 24px;
            width: 24px;
        }
        
        .footer-links {
            gap: 0.8rem;
        }
        
        .footer-link {
            font-size: 0.85rem;
            padding: 0.3rem 0.6rem;
        }
    }

    /* Accessibility improvements for footer */
    .footer-link:focus {
        outline: 2px solid #ff6b6b;
        outline-offset: 2px;
    }

    @media (prefers-reduced-motion: reduce) {
        .galaxy-footer::before,
        .galaxy-footer::after,
        .footer-logo {
            animation: none !important;
        }
    }

    /* High contrast mode support for footer */
    @media (prefers-contrast: high) {
        .galaxy-footer {
            background: #000000;
            border-top: 2px solid #ffffff;
        }
        
        .footer-link {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid #ffffff;
        }
        
        .footer-text {
            color: #ffffff;
        }
    }
</style>

</body>
</html>
