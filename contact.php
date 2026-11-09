<?php 
session_start(); // Added session start
include 'header.php'; 

require_once __DIR__ . '/connection.php'; // Moved connection to the top

// Get user information if logged in
$user_info = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM login WHERE id = $user_id");
    if ($result && $result->num_rows > 0) {
        $user_info = $result->fetch_assoc();
    }
}
?>
<html>
<head>
<div class="contact-hero">
  <h1>Contact Us</h1>
  <p>We'd love to hear from you — let's make something awesome together.</p>
</div>
<style>.contact-hero {
  text-align: center;
  padding: 50px;
  background: linear-gradient(to right, #ff6a00, #ffb347);
  color: white;
}

.contact-container {
  display: flex;
  flex-wrap: wrap;
  padding: 40px;
  gap: 30px;
  justify-content: center;
}

.contact-form, .contact-info {
  flex: 1 1 300px;
  background: #ffffff;
  padding: 30px;
  border-radius: 20px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.contact-form input, .contact-form textarea, .contact-form select {
  width: 100%;
  padding: 14px;
  margin-bottom: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  font-size: 16px;
}

.contact-form button {
  padding: 14px;
  background: linear-gradient(to right, #ff6a00, #ffb347);
  color: white;
  border: none;
  font-size: 18px;
  border-radius: 12px;
  cursor: pointer;
}

.contact-map iframe {
  width: 100%;
  height: 400px;
  border: none;
  margin: 30px 0;
  border-radius: 20px;
}

.faq-section {
  padding: 50px;
  background: #fefefe;
}

.faq-section details {
  margin-bottom: 20px;
  background: #f9f9f9;
  padding: 15px;
  border-radius: 10px;
  cursor: pointer;
}

body{
    background:rgb(190, 235, 238);
}
</style>
</head>
<body>
<div class="contact-container">
  <div class="contact-form">
    <form action="send_message.php" method="POST">
      <!-- Add user_id hidden field -->
      <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">
      
      <input type="text" name="full_name" placeholder="Full Name" required 
             value="<?= $user_info['full_name'] ?? '' ?>">
      
      <input type="email" name="email" placeholder="Email Address" required 
             value="<?= $user_info['email'] ?? '' ?>">
      
      <input type="text" name="phone" placeholder="Phone Number (optional)"
             value="<?= $user_info['mobile'] ?? '' ?>">
      
      <select name="inquiry_type">
        <option value="">-- Select Inquiry Type --</option>
        <option value="business">Business Inquiry</option>
        <option value="feedback">Feedback</option>
        <option value="support">Support</option>
        <option value="collaboration">Collaboration</option>
      </select>
      
      <textarea name="message" placeholder="Your Message" required></textarea>
      
      <button type="submit">Send Message</button>
    </form>
  </div>

  <div class="contact-info">
    <h2>Quick Contact</h2>
    <p><i class="fa fa-phone"></i> +91 96898 81705</p>
    <p><i class="fa fa-envelope"></i> monikaimpact2018@gmail.com</p>
    <p><i class="fa fa-map-marker"></i> PUNE, Maharashtra</p>

    <h3>Working Hours</h3>
    <p>Mon - Fri: 9AM – 8PM</p>
    <p>Sat: 10AM – 5PM</p>
    <p>Sun: Closed</p>
  </div>
</div>

<div class="contact-map">
  <iframe src="https://maps.google.com/maps?q=pune&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" allowfullscreen></iframe>
</div>

<div class="faq-section">
  <h2>Frequently Asked Questions</h2>
  <details>
    <summary>How long does it take to get a response?</summary>
    <p>Usually within 24 hours, depending on the inquiry type.</p>
  </details>
  <details>
    <summary>Do you offer custom services?</summary>
    <p>Yes! Contact us and we'll get back with a tailored plan.</p>
  </details>
</div>
</body>
<?php include 'footer.php'; ?>
</html>