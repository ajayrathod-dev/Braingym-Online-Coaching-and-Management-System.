<?php
// about.php
$page_title = "Brain Gym Abacus | Mental Math Experts";
$current_year = date("Y");
include('header.php');
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Unlock Your Child's Full Potential</h1>
            <p class="hero-subtitle">Transformative Abacus Training Since 2018</p>
            <div class="cta-container">
                <a href="#contact" class="cta-button">Book Free Demo</a>
                <a href="tel:+919689881705" class="cta-button secondary">Call Now</a>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <div class="stats-bar">hhj
        <div class="stat-item">
            <div class="stat-number">5+</div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">1500+</div>
            <div class="stat-label">Students Trained</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">97%</div>
            <div class="stat-label">Success Rate</div>
        </div>
    </div>

    <!-- Programs Grid -->
    <section class="programs-section">
        <h2 class="section-title">Our Signature Programs</h2>
        <div class="programs-grid">
            <div class="program-card">
                <div class="program-icon">🧮</div>
                <h3>Foundation Abacus</h3>
                <p>For beginners aged 5-8 years</p>
            </div>
            <!-- Add more program cards -->
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="contact-container">
            <div class="contact-info">
                <h2>Start Your Journey Today</h2>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <a href="tel:+919689881705">+91 96898 1705</a>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:monikaimpact2018@gmail.com">monikaimpact2018@gmail.com</a>
                </div>
                <div class="social-links">
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="contact-form">
                <h3>Quick Inquiry</h3>
                <form>
                    <!-- Form fields -->
                </form>
            </div>
        </div>
    </section>
</main>

<?php include('footer.php'); ?>
<style>
  /* Modern Color Scheme */
:root {
    --primary: #2563eb;    /* Professional blue */
    --secondary: #4f46e5;  /* Royal purple */
    --accent: #f59e0b;     /* Warm orange */
    --dark: #1e293b;
    --light: #f8fafc;
}

/* Hero Section */
.hero {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 8rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero-content {
    max-width: 1200px;
    margin: 0 auto;
}

.hero h1 {
    font-size: 3.5rem;
    margin-bottom: 1.5rem;
    font-weight: 700;
}

.hero-subtitle {
    font-size: 1.5rem;
    margin-bottom: 3rem;
    opacity: 0.9;
}

/* CTA Buttons */
.cta-button {
    display: inline-block;
    padding: 1rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin: 0 1rem;
}

.cta-button {
    background: var(--accent);
    color: white;
    border: 2px solid var(--accent);
}

.cta-button.secondary {
    background: transparent;
    border: 2px solid white;
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Stats Bar */
.stats-bar {
    background: white;
    padding: 4rem 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin: -4rem auto 4rem auto;
    max-width: 1200px;
    border-radius: 15px;
    position: relative;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
}

.stat-label {
    color: var(--dark);
    opacity: 0.8;
}

/* Programs Grid */
.programs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 4rem 0;
}

.program-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: transform 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.program-card:hover {
    transform: translateY(-10px);
}

.program-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

/* Contact Section */
.contact-section {
    background: var(--light);
    padding: 6rem 2rem;
}

.contact-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.contact-info {
    background: white;
    padding: 3rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.contact-item {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
    font-size: 1.1rem;
}

.contact-item i {
    margin-right: 1rem;
    color: var(--primary);
    font-size: 1.5rem;
    width: 30px;
}

.social-links {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
}

.social-links a {
    font-size: 1.5rem;
    color: var(--dark);
    transition: color 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-container {
        grid-template-columns: 1fr;
    }
    
    .hero h1 {
        font-size: 2.5rem;
    }
}

body{
                background:rgb(190, 235, 238);

}
</style>