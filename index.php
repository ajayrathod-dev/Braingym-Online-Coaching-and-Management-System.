<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'header.php';
?>

<main>
<style>

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header Styles */
        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo i {
            font-size: 2rem;
            color: var(--primary);
        }

        .logo h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            font-size: 1.1rem;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-actions a {
            text-decoration: none;
            color: var(--dark);
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .user-actions a:hover {
            color: var(--primary);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 8px 20px rgba(42, 107, 197, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(42, 107, 197, 0.4);
        }

        /* Hero Section */
        .hero {
            padding: 5rem 0 3rem;
            position: relative;
            overflow: hidden;
        }

        .hero-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .hero-content h2 {
            font-size: 3.5rem;
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .hero-content h2 span {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: var(--text);
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text);
        }

        .hero-image {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .hero-image img {
            max-width: 100%;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            z-index: 2;
        }

        .hero-blob {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120%;
            height: 120%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            z-index: 1;
        }

        /* Carousel Section */
        .carousel-section {
            padding: 4rem 0;
        }

        .carousel-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slide {
            min-width: 100%;
            position: relative;
        }

        .slide img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            object-position: center;
            border-radius: 20px;
        }

        .caption {
            position: absolute;
            bottom: 20%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: var(--secondary);
            padding: 2rem 3rem;
            width: 80%;
            text-align: center;
            border-radius: 10px;
        }

        .caption h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }

        .caption p {
            font-size: 1.5rem;
            line-height: 1.6;
        }

        .carousel-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 1rem;
            transform: translateY(-50%);
        }

        .carousel-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: var(--secondary);
            padding: 1rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.5rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* Competitions Section */
        .section {
            padding: 6rem 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
            position: relative;
            display: inline-block;
            font-family: 'Playfair Display', serif;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .section-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 1.5rem auto 0;
            color: var(--text);
        }

        .competitions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
        }

        .competition-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
        }

        .competition-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .card-image {
            height: 220px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .competition-card:hover .card-image img {
            transform: scale(1.05);
        }

        .card-content {
            padding: 1.75rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-category {
            background: rgba(42, 107, 197, 0.1);
            color: var(--primary);
            padding: 0.35rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .card-date {
            color: var(--text);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--dark);
            font-family: 'Playfair Display', serif;
        }

        .card-details {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .detail-item i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 0.5rem 1.5rem;
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        /* Benefits Section */
        .benefits {
            background: linear-gradient(135deg, #e0ebff 0%, #d1e2ff 100%);
            position: relative;
            overflow: hidden;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .benefit-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            z-index: 2;
        }

        .benefit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .benefit-icon {
            width: 80px;
            height: 80px;
            background: rgba(42, 107, 197, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: var(--primary);
            transition: var(--transition);
        }

        .benefit-card:hover .benefit-icon {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .benefit-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
            font-family: 'Playfair Display', serif;
        }

        .benefit-card p {
            color: var(--text);
        }

        /* Learning Path Section */
        .learning-path {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--card-shadow);
            margin: 4rem auto;
            max-width: 1200px;
        }

        .path-container {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }

        .path-item {
            flex: 1;
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background: rgba(42, 107, 197, 0.05);
            transition: var(--transition);
        }

        .path-item:hover {
            transform: translateY(-10px);
            background: rgba(42, 107, 197, 0.1);
        }

        .path-item h4 {
            font-size: 1.5rem;
            margin: 1rem 0;
            color: var(--primary);
        }

        /* Testimonials */
        .testimonials-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        .testimonial-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            margin: 2rem 0;
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: 30px;
            font-size: 8rem;
            color: rgba(42, 107, 197, 0.1);
            font-family: Georgia, serif;
            line-height: 1;
        }

        .testimonial-content {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
            color: var(--text);
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary);
        }

        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-info h4 {
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .author-info p {
            color: var(--primary);
            font-weight: 500;
        }


        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .hero-stats {
                justify-content: center;
            }
            
            .nav-links {
                display: none;
            }
            
            .path-container {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .hero-content h2 {
                font-size: 2.8rem;
            }
            
            .section-header h2 {
                font-size: 2.2rem;
            }
            
            .stat-value {
                font-size: 2rem;
            }
            
            .slide img {
                height: 400px;
            }
            
            .caption {
                padding: 1.5rem;
            }
            
            .caption h2 {
                font-size: 2.5rem;
            }
            
            .caption p {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .hero-content h2 {
                font-size: 2.2rem;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 1.5rem;
            }
            
            .section {
                padding: 4rem 0;
            }
            
            .slide img {
                height: 300px;
            }
        }
</style>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-container">
            <div class="hero-content">
                <h2>Unlock Your <span>Brain's Potential</span></h2>
                <p>Master mental math skills through our proven abacus training program. Transform math anxiety into confidence with our interactive learning methods and build a brighter future.</p>
                <a href="admission.php" class="btn btn-primary">Get Started <i class="fas fa-arrow-right"></i></a>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-value">10K+</div>
                        <div class="stat-label">Students Trained</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">98%</div>
                        <div class="stat-label">Success Rate</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">50+</div>
                        <div class="stat-label">Certified Trainers</div>
                    </div>
                </div>
            </div>
            
            <div class="hero-image">
                <div class="hero-blob"></div>
                <img src="https://images.unsplash.com/photo-1596495577886-d920f1fb7238?q=80&w=1474&auto=format&fit=crop" alt="Brain Training">
            </div>
        </div>
    </section>

    <!-- Carousel Section -->
    <section class="carousel-section">
        <div class="carousel-container">
            <div class="carousel">
                <div class="slide">
                    <img src="https://i.imgur.com/dUzVnEO.jpeg" alt="Brain Training">
                    <div class="caption">
                        <h2>Unlock Your Brain's Potential</h2>
                        <p>Master mental math skills through our proven abacus training program</p>
                    </div>
                </div>
                <div class="slide">
                    <img src="https://i.imgur.com/WgaIVkH.jpeg" alt="Child Learning">
                    <div class="caption">
                        <h2>Build a Brighter Future</h2>
                        <p>Transform math anxiety into confidence with our interactive learning methods</p>
                    </div>
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1543269865-cbf427effbad?q=80&w=1470&auto=format&fit=crop" alt="Mental Math">
                    <div class="caption">
                        <h2>Excel in Competitions</h2>
                        <p>Join national and international math competitions to showcase your skills</p>
                    </div>
                </div>
            </div>
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn">❮</button>
                <button class="carousel-btn next-btn">❯</button>
            </div>
        </div>
    </section>

    <!-- Competitions Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Upcoming Competitions</h2>
                <p>Challenge yourself and showcase your skills in our exciting math competitions</p>
            </div>
            
            <div class="competitions-grid">
                <?php 
                require_once __DIR__ . '/connection.php';
                $comp_result = mysqli_query($conn, "SELECT * FROM competitions ORDER BY date DESC LIMIT 3");
                while ($comp = mysqli_fetch_assoc($comp_result)) : ?>
                <div class="competition-card">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1589998059171-988d887df646?q=80&w=1476&auto=format&fit=crop" alt="Math Competition">
                    </div>
                    <div class="card-content">
                        <div class="card-header">
                            <div class="card-category"><?= ucfirst($comp['mode']) ?></div>
                            <div class="card-date"><i class="far fa-calendar"></i> <?= htmlspecialchars($comp['date']) ?></div>
                        </div>
                        <h3 class="card-title"><?= htmlspecialchars($comp['competition_name']) ?></h3>
                        <div class="card-details">
                            <div class="detail-item">
                                <i class="fas fa-chart-line"></i>
                                <span>Level: <?= htmlspecialchars($comp['level']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-trophy"></i>
                                <span>Prizes: $5,000+</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>Duration: 2 hours</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="competition.php?comp_id=<?= $comp['id'] ?>" class="btn btn-primary">Enroll Now</a>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span>120+ Participants</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="section benefits">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose Brain Gym?</h2>
                <p>Discover the benefits of our unique mental math training program</p>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>Improved Concentration</h3>
                    <p>Enhance focus and memory retention through structured exercises and consistent practice.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Faster Calculations</h3>
                    <p>Develop lightning-fast mental math skills without relying on calculators or devices.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Better Problem Solving</h3>
                    <p>Strengthen logical thinking and analytical abilities to tackle complex problems with ease.</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Academic Excellence</h3>
                    <p>Improve overall academic performance with enhanced cognitive abilities and confidence.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Path Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Structured Learning Path</h2>
                <p>Our comprehensive curriculum designed for maximum skill development</p>
            </div>
            
            <div class="learning-path">
                <div class="path-container">
                    <div class="path-item">
                        <div class="benefit-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h4>Beginner Level</h4>
                        <p>Master basic abacus operations and simple calculations</p>
                    </div>
                    
                    <div class="path-item">
                        <div class="benefit-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>Intermediate Level</h4>
                        <p>Develop speed and accuracy with complex calculations</p>
                    </div>
                    
                    <div class="path-item">
                        <div class="benefit-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h4>Advanced Level</h4>
                        <p>Master mental math techniques and competition strategies</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Success Stories</h2>
                <p>What our students and parents say about their experience</p>
            </div>
            
            <div class="testimonials-container">
                <div class="testimonial-card">
                    <p class="testimonial-content">"Brain Gym completely transformed my child's approach to math. From struggling with basic arithmetic to winning national competitions, the progress has been incredible. The trainers are patient and truly care about each student's development."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=1376&auto=format&fit=crop" alt="Sarah Johnson">
                        </div>
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <p>Parent of 10-year old student</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <p class="testimonial-content">"The mental math techniques I learned at Brain Gym have not only helped me excel in mathematics but have also improved my performance in other subjects. My concentration and problem-solving skills are now my greatest strengths."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=1374&auto=format&fit=crop" alt="Michael Chen">
                        </div>
                        <div class="author-info">
                            <h4>Michael Chen</h4>
                            <p>Student, National Champion</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>


    <script>
        // Carousel Script
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.querySelector('.carousel');
            const slides = document.querySelectorAll('.slide');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            let currentIndex = 0;
            let autoSlideInterval;

            function updateCarousel() {
                carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % slides.length;
                updateCarousel();
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateCarousel();
            }

            function startAutoSlide() {
                autoSlideInterval = setInterval(nextSlide, 5000);
            }

            prevBtn.addEventListener('click', () => {
                clearInterval(autoSlideInterval);
                prevSlide();
                startAutoSlide();
            });

            nextBtn.addEventListener('click', () => {
                clearInterval(autoSlideInterval);
                nextSlide();
                startAutoSlide();
            });

            // Start automatic sliding
            startAutoSlide();

            // Pause on hover
            const carouselContainer = document.querySelector('.carousel-container');
            carouselContainer.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
            carouselContainer.addEventListener('mouseleave', startAutoSlide);
            
            // Animation on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });
            
            // Observe elements that should animate
            document.querySelectorAll('.competition-card, .benefit-card, .testimonial-card, .path-item').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>