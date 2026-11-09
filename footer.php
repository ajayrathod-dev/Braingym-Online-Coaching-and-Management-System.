<footer>
    <div class="footer-container">
        <div class="footer-logo">
            <img src="https://i.postimg.cc/nccWxysL/bn-E03-Bf-removebg-preview.png" alt="Brain Gym Logo">
            <h2>Brain Gym</h2>
        </div>
        <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="competition.php">Competition</a></li>
                <li><a href="book_appointment.php">Appointment</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h3>Contact Us</h3>
            <p>Email: contact@braingym.com</p>
            <p>Phone: +91 12345 67890</p>
        </div>
        <div class="footer-credits">
            <h3>Developed By</h3>
            <div class="credit-badge">
                <div class="dev-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M12,3C7.03,3 3,7.03 3,12C3,16.97 7.03,21 12,21C16.97,21 21,16.97 21,12C21,7.03 16.97,3 12,3M12,19C8.13,19 5,15.87 5,12C5,8.13 8.13,5 12,5C15.87,5 19,8.13 19,12C19,15.87 15.87,19 12,19Z" />
                    </svg>
                </div>
                <div class="dev-info">
                    <strong>Code Crave</strong>
                    <span>Lead Developer & Designer</span>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Brain Gym. All Rights Reserved.</p>
    </div>
</footer>

<style>
    footer {
        background: linear-gradient(to right, #ff4500, #ff6699);
        color: white;
        padding: 40px 20px;
        text-align: center;
        box-shadow: 0px -5px 15px rgba(0, 0, 0, 0.2);
        border-radius: 20px 20px 0 0;
        position: relative;
        overflow: hidden;
    }

    .footer-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-logo img {
        width: 100px;
        animation: float 3s infinite alternate ease-in-out;
        filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.7));
    }

    @keyframes float {
        from { transform: translateY(0px); }
        to { transform: translateY(8px); }
    }

    .footer-links ul {
        list-style: none;
        padding: 0;
    }

    .footer-links a {
        color: white;
        text-decoration: none;
        transition: color 0.3s ease-in-out;
        display: block;
        padding: 5px 0;
    }

    .footer-links a:hover {
        color: yellow;
        transform: translateX(5px);
    }

    .footer-contact p {
        margin: 8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .footer-contact p:before {
        content: "•";
        font-size: 1.5em;
        color: white;
    }

    .footer-social a {
        color: white;
        font-size: 20px;
        margin: 0 10px;
        transition: transform 0.3s;
    }

    .footer-social a:hover {
        transform: scale(1.2);
        color: yellow;
    }

    .footer-bottom {
        margin-top: 30px;
        font-size: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.3);
        padding-top: 20px;
    }

    /* Credit Section Styles */
    .footer-credits {
        max-width: 300px;
        text-align: left;
    }

    .credit-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .credit-badge:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-5px);
    }

    .dev-icon {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dev-icon svg {
        width: 30px;
        height: 30px;
    }

    .dev-info {
        line-height: 1.4;
    }

    .dev-info strong {
        font-size: 1.1em;
        display: block;
        color: #fff;
    }

    .dev-info span {
        font-size: 0.85em;
        color: rgba(255, 255, 255, 0.9);
    }

    /* Footer background animation */
    footer:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 20%),
            radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 20%);
        animation: rotate 20s linear infinite;
        pointer-events: none;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .footer-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .footer-credits {
            text-align: center;
            align-items: center;
        }
        
        .credit-badge {
            justify-content: center;
        }
        
        .footer-links a {
            justify-content: center;
        }
    }
</style>