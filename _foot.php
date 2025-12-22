</main>

<footer class="galaxy-footer">
    <div class="footer-content">
        <div class="footer-brand">
            <img src="/icon/teddy-bear.png" alt="CTRL + EAT Logo" class="footer-logo">
            <span class="footer-text">&copy; 2024 CTRL + EAT. All rights reserved.</span>
        </div>
        <div class="footer-links">
            <a href="/page/user/about.php" class="footer-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>
                </svg>
                About
            </a>
            <a href="/page/purchase/shopnow.php" class="footer-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19,7H18V6A2,2 0 0,0 16,4H8A2,2 0 0,0 6,6V7H5A1,1 0 0,0 4,8V19A3,3 0 0,0 7,22H17A3,3 0 0,0 20,19V8A1,1 0 0,0 19,7M8,6H16V7H8V6M18,19A1,1 0 0,1 17,20H7A1,1 0 0,1 6,19V9H18V19Z"/>
                </svg>
                Shop
            </a>
            <a href="#" class="footer-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,5.11 21.1,4 20,4M20,8L12,13L4,8V6L12,11L20,6V8Z"/>
                </svg>
                Contact
            </a>
        </div>
    </div>
</footer>

<style>
    /* 🌌 GALAXY FOOTER STYLES 🌌 */
    
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

    /* 🌟 GALAXY FOOTER 🌟 */
    .galaxy-footer {
        background: linear-gradient(135deg, #0a0a23 0%, #1a1a3e 25%, #2d1b69 50%, #1e1e3f 75%, #0f0f2a 100%);
        margin-top: auto;
        padding: 2.5rem 0;
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 -8px 32px rgba(0, 0, 0, 0.4),
            0 -4px 16px rgba(138, 43, 226, 0.3),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }

    /* Animated galaxy nebula background for footer */
    .galaxy-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(ellipse at 85% 25%, rgba(138, 43, 226, 0.3) 0%, transparent 60%),
            radial-gradient(ellipse at 15% 75%, rgba(75, 0, 130, 0.25) 0%, transparent 60%),
            radial-gradient(ellipse at 55% 10%, rgba(255, 20, 147, 0.2) 0%, transparent 50%),
            radial-gradient(ellipse at 30% 90%, rgba(0, 191, 255, 0.15) 0%, transparent 50%);
        animation: galaxyNebula 25s ease-in-out infinite reverse;
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
            radial-gradient(1px 1px at 30px 20px, rgba(255, 255, 255, 0.7), transparent),
            radial-gradient(1px 1px at 80px 50px, rgba(255, 255, 255, 0.5), transparent),
            radial-gradient(1px 1px at 130px 30px, rgba(255, 255, 255, 0.8), transparent),
            radial-gradient(1px 1px at 200px 60px, rgba(255, 255, 255, 0.6), transparent),
            radial-gradient(1px 1px at 280px 40px, rgba(255, 255, 255, 0.4), transparent);
        background-repeat: repeat;
        background-size: 320px 80px;
        animation: twinkleStars 8s ease-in-out infinite alternate;
        z-index: 1;
    }

    .footer-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .footer-brand {
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }

    .footer-logo {
        height: 36px;
        width: 36px;
        border-radius: 50%;
        filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.4));
        animation: logoGalaxyGlow 5s ease-in-out infinite alternate;
        border: 2px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .footer-logo:hover {
        transform: scale(1.1) rotate(5deg);
        filter: drop-shadow(0 6px 20px rgba(255, 20, 147, 0.6));
    }

    .footer-text {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        font-weight: 500;
        background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.8) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .footer-links {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .footer-link {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 0.8rem 1.5rem;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        box-shadow: 
            0 2px 8px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .footer-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .footer-link:hover::before {
        left: 100%;
    }

    .footer-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 
            0 8px 32px rgba(138, 43, 226, 0.4),
            0 4px 16px rgba(255, 20, 147, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .footer-link svg {
        filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.3));
        transition: all 0.3s ease;
    }

    .footer-link:hover svg {
        filter: drop-shadow(0 4px 8px rgba(255, 20, 147, 0.5));
        transform: scale(1.1);
    }

    /* 📱 RESPONSIVE FOOTER 📱 */
    @media (max-width: 1024px) {
        .footer-content {
            padding: 0 2rem;
        }
        
        .footer-links {
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .galaxy-footer {
            padding: 2rem 0;
        }
        
        .footer-content {
            flex-direction: column;
            gap: 2rem;
            text-align: center;
            padding: 0 1.5rem;
        }
        
        .footer-brand {
            flex-direction: column;
            gap: 1rem;
        }
        
        .footer-text {
            font-size: 0.9rem;
        }
        
        .footer-logo {
            height: 32px;
            width: 32px;
        }
        
        .footer-links {
            gap: 1.2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .footer-link {
            font-size: 0.9rem;
            padding: 0.6rem 1.2rem;
        }
    }

    @media (max-width: 480px) {
        .galaxy-footer {
            padding: 1.8rem 0;
        }
        
        .footer-content {
            padding: 0 1rem;
            gap: 1.5rem;
        }
        
        .footer-text {
            font-size: 0.85rem;
        }
        
        .footer-logo {
            height: 28px;
            width: 28px;
        }
        
        .footer-links {
            gap: 1rem;
        }
        
        .footer-link {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            gap: 0.4rem;
        }
        
        .footer-link svg {
            width: 14px;
            height: 14px;
        }
    }

    /* ♿ ACCESSIBILITY IMPROVEMENTS FOR FOOTER ♿ */
    .footer-link:focus {
        outline: 3px solid #ff1493;
        outline-offset: 3px;
        box-shadow: 
            0 0 0 6px rgba(255, 20, 147, 0.3),
            0 8px 32px rgba(138, 43, 226, 0.4);
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
            border-top: 3px solid #ffffff;
        }
        
        .footer-link {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid #ffffff;
            color: #ffffff;
        }
        
        .footer-text {
            color: #ffffff;
            -webkit-text-fill-color: #ffffff;
        }
    }

    /* 🎯 HOVER STATES FOR TOUCH DEVICES 🎯 */
    @media (hover: none) and (pointer: coarse) {
        .footer-link:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }
    }
</style>

</body>
</html>
