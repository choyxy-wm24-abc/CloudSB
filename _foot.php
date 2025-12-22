</main>

<footer class="footer">
    <p class="text">
        <img src="/icon/teddy-bear.png" alt="Logo">
        &copy; 2024 Your CTRL + EAT. All rights reserved.
    </p>
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

    /* Footer styles */
    .footer {
        background: linear-gradient(135deg, #0077be 0%, #00a8e8 100%);
        margin-top: auto;
        padding: 20px 0;
        box-shadow: 0 -4px 20px rgba(0,119,190,0.2);
    }

    .footer .text {
        text-align: center;
        color: white;
        margin: 0;
        font-size: 1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .footer img {
        height: 24px;
        width: 24px;
        filter: brightness(0) invert(1);
    }

    /* Responsive footer */
    @media (max-width: 768px) {
        .footer .text {
            font-size: 0.9rem;
            padding: 0 20px;
        }
        
        .footer img {
            height: 20px;
            width: 20px;
        }
    }
</style>

</body>
</html>
