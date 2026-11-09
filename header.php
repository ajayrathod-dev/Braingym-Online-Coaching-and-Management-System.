<?php
// Display flash messages in header
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    
    $safeMsg = addslashes(htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8'));
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('{$safeMsg}');
        });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Welcome', ENT_QUOTES, 'UTF-8') ?> - Brain Gym Academy</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Professional Abacus Training & Mental Math Development for Children">
    <meta name="keywords" content="Abacus, Math Classes, Brain Development, Child Education">
    
    <!-- External Resources -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="favicon.ico">

    <style>
        /* Sticky Footer Global Styles */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
    </style>
</head>
<body>




    <!-- Commercial Header -->
    <header class="commercial-header">
        <div class="brand-container">
            <a href="index.php" class="logo-link">
                <img src="https://i.postimg.cc/nccWxysL/bn-E03-Bf-removebg-preview.png" 
                     alt="Brain Gym Logo" 
                     class="brand-logo">
            </a>
            <div class="brand-text">
                <h1 class="main-title">Brain Gym Academy</h1>
                <p class="tagline">Developing Young Minds Through Abacus Learning</p>
            </div>
        </div>

        <div class="header-cta">
            <a href="tel:+919689881705" class="cta-phone">
                <i class="fas fa-phone"></i> +91 96898 1705
            </a>
            <a href="mailto:monikaimpact2018@gmail.com" class="cta-email">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </div>

      <?php if (isset($_SESSION['username'])): ?>
    <div class="user-greeting" style="
        position: absolute;
        top: 15px;
        right: 25px;
        background: #f8f9fa;
        color: #2c3e50;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        font-family: 'Segoe UI', 'Helvetica Neue', sans-serif;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    ">
        <i class="fas fa-user-check" style="color: #6c5ce7;"></i>
        <span style="position: relative; top: 1px;">
            <?= htmlspecialchars($_SESSION['username']) ?>
        </span>
    </div>
<?php endif; ?>
    </header>

    <!-- Professional Navigation -->
    <nav class="commercial-nav">
        <ul class="main-menu">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
         <li><a href="admission.php"><i class="fas fa-user-graduate"></i> Admission</a></li>
            <li><a href="competition_view.php"><i class="fas fa-trophy"></i> Competitions</a></li>
            <li><a href="book_appointment.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                        <li>  <a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
                                    <li><a href="notices.php"><i class="fas fa-bullhorn"></i> Notice</a></li>


            <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
            <li><a href="contact.php"><i class="fas fa-address-book"></i> Contact</a></li>
            <li class="logout-item">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <style>
        /* ===== Commercial Style Variables ===== */
        :root {
            --primary-blue:rgb(185, 28, 51);
            --accent-orange:rgb(21, 224, 136);
            --dark-text:rgb(10, 12, 16);
            --light-bg:rgb(191, 210, 224);
            --header-gradient: linear-gradient(135deg,rgb(131, 24, 86) 0%,rgb(241, 8, 113) 100%);
    
        }

        /* ===== Professional Header Styles ===== */
        .commercial-header {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            padding: 1rem 5%;
            background: var(--header-gradient);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .brand-logo {
            width: 120px;
            transition: transform 0.3s ease;
        }

        .brand-text {
            color: white;
        }

        .main-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .tagline {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        /* ===== Header CTA Styles ===== */
        .header-cta {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .cta-phone, .cta-email {
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .cta-phone:hover, .cta-email:hover {
            background: var(--accent-orange);
            transform: translateY(-2px);
        }

        /* ===== Professional Navigation ===== */
        .commercial-nav {
            background:rgb(245, 243, 241);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 0 5%;
        }

        .main-menu {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 0;
            padding: 1rem 0;
        }

        .main-menu a {
            color: var(--dark-text);
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .main-menu a:hover {
            color: var(--primary-blue);
            background: var(--light-bg);
        }

        .logout-item {
            margin-left: auto;
        }

        .logout-btn {
            background: #dc3545;
            color: white !important;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
        }

        .logout-btn:hover {
            background: #bb2d3b;
        }

        /* ===== Responsive Design ===== */
        @media (max-width: 992px) {
            .commercial-header {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                text-align: center;
            }

            .brand-container {
                justify-content: center;
            }

            .header-cta {
                justify-content: center;
                flex-wrap: wrap;
            }

            .main-menu {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .logout-item {
                margin-left: 0;
                width: 100%;
                text-align: center;
            }
        }
    </style>