# BrainGym - Online Coaching and Management System

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B%20%7C%208.x-777bb4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B%20%7C%20MariaDB-4479a1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Razorpay](https://img.shields.io/badge/Payments-Razorpay-0c2340?logo=razorpay&logoColor=3395ff)](https://razorpay.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**BrainGym** is an end-to-end web platform designed for educational institutions, coaching academies, and training centers. It bridges the gap between students, educators, and administrators by combining admission handling, fee processing, slot-based appointment booking, competition management, notice broadcasting, and an interactive media gallery into a unified dashboard.

---

## 🌟 Key Features

### 👨‍🎓 Student & Public Portal
- **User Authentication:** Secure registration and login with encrypted password storage (`password_hash`).
- **Online Admission & Enrollment:** Digital admission form with personal and educational details, course selection, and installment plans.
- **Payment Gateway Integration:** Integrated with **Razorpay** for online payment processing (full and installment options) and instant transaction status verification.
- **Automated Invoicing & Receipts:** PDF receipt and admission summary generation powered by **Dompdf**.
- **Appointment Scheduling:** Book, view, reschedule, or cancel counseling or coaching sessions with real-time duplicate-booking prevention.
- **Competitions & Events:** View upcoming competitions, register as participants by roll number and class, and monitor enrollment stages.
- **Interactive Showcase Gallery:** Media gallery displaying photos and video showcases with likes and user comment streams.
- **Notice Board:** Real-time updates and circulars published by academy administrators.
- **Contact Inquiries:** Direct inquiry submission form categorized by inquiry type with admin notification alerts.

### 🛡️ Admin Management Dashboard
- **Executive Analytics:** Overview counters for total admissions, paid vs. pending revenue, active notices, and recent applications.
- **Admission Management:** Inspect, update status (pending, approved, paid), search, and manage student enrollments.
- **Appointment Control:** Centralized calendar grid of appointments grouped by date with cancellation controls.
- **Notice Board Broadcasting:** Create, edit, publish/unpublish notices, and set automated expiry dates.
- **Competition Creator:** Schedule and publish academic competitions with levels, rules, and timings.
- **Media Moderation:** Upload, toggle visibility, and organize gallery media.
- **System Settings:** Configurable operational hours, appointment thresholds, and system flags.

---

## 📂 Project Structure

```text
braingym/
├── admin/                     # Admin control panel
│   ├── includes/              # Admin header, navigation & footer
│   ├── admission.php          # Admin admission processing
│   ├── appointments.php       # Appointment management
│   ├── competitions.php       # Competition management
│   ├── gallery.php            # Gallery moderation
│   ├── index.php              # Admin analytics dashboard
│   ├── login.php              # Admin login
│   ├── notices.php            # Notice board manager
│   ├── notifications.php      # System notifications center
│   ├── register.php           # First-time admin onboarding
│   ├── settings.php           # Global platform settings
│   └── users.php              # Enrolled student directory
├── api/                       # Asynchronous API endpoints (likes, comments)
├── database/                  # SQL schema & database dumps
│   └── braingym.sql           # Complete MySQL database schema
├── dompdf/                    # Dompdf PDF rendering engine
├── razorpay-php/              # Razorpay official PHP SDK
├── uploads/                   # Runtime upload directories
│   ├── gallery/               # Gallery photos & videos
│   └── notices/               # Circulars and attachments
├── about.php                  # About us page
├── admission.php              # Student admission form
├── book_appointment.php       # Slot booking interface
├── checkout.php               # Payment initialization
├── competition.php            # Competition listings & registration
├── connection.php             # Active database connection
├── connection.example.php     # Database connection template
├── contact.php                # Contact inquiry page
├── gallery.php                # Public multimedia gallery
├── header.php / footer.php    # Global layouts
├── index.php                  # Main landing page
├── login.php / logout.php     # Student session management
├── pay.php                    # Razorpay checkout handler
├── success.php                # Post-payment verification & receipt
└── U_register.php             # Student registration page
```

---

## 🚀 Getting Started

### Prerequisites
- **Web Server:** Apache (via [XAMPP](https://www.apachefriends.org/), WampServer, or LAMP stack)
- **PHP:** Version 7.4 or higher (cURL and OpenSSL extensions enabled)
- **Database:** MySQL 5.7+ or MariaDB 10.4+
- **Browser:** Modern desktop or mobile browser

---

### Installation & Setup

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/ajayrathod-dev/Braingym-Online-Coaching-and-Management-System..git braingym
   ```
   Place the project inside your web root directory (e.g., `C:/xampp/htdocs/braingym`).

2. **Database Setup:**
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or your MySQL client.
   - Create a new database named `codecrav_boss` (or any custom name).
   - Import the database schema from:
     ```text
     database/braingym.sql
     ```

3. **Configure Database Connection:**
   - Verify or adjust `connection.php` (refer to `connection.example.php`):
     ```php
     $servername = "localhost";
     $username   = "root";
     $password   = "";
     $dbname     = "codecrav_boss"; // Your database name
     ```

4. **Razorpay Configuration (Optional / Sandbox):**
   - In `pay.php` and `checkout.php`, update the Razorpay Key ID:
     ```javascript
     key: "YOUR_RAZORPAY_KEY_ID"
     ```

5. **Run the Application:**
   - Start Apache and MySQL in your XAMPP Control Panel.
   - Open your browser and navigate to:
     - **Student Portal:** `http://localhost/braingym/`
     - **Admin Portal:** `http://localhost/braingym/admin/`

---

## 🔐 Admin Setup & First-Time Access

1. Open `http://localhost/braingym/admin/register.php` in your browser.
2. Enter your desired administrator credentials.
3. When prompted for the invitation code, provide the master setup code:
   ```text
   BRAIN-GYM-ADMIN-2024
   ```
4. Sign in via `http://localhost/braingym/admin/login.php`.

---

## 🔒 Security Best Practices Implemented

- **Prepared Statements:** Parameterized queries via MySQLi to defend against SQL Injection.
- **Password Encryption:** Native PHP `password_hash()` using Bcrypt algorithm.
- **Session Protection:** Role-based access verification in `admin/auth_check.php`.
- **Protected Environment:** `.gitignore` configured to keep logs, IDE files, and temporary artifacts out of version control.

---

## 👨‍💻 Author

**Ajay Rathod**  
- GitHub: [@ajayrathod-dev](https://github.com/ajayrathod-dev)
- Email: [mr.developer9607@gmail.com](mailto:mr.developer9607@gmail.com)

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
