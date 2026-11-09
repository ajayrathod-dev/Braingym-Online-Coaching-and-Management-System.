<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../connection.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<!-- Rest of header --><html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Brain Gym</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8fafc;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #1a2236;
            color: #fff;
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 24px 0;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
        }

        .sidebar h2.logo {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 40px;
            color: #fff;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 28px;
            color: #a3aec7;
            text-decoration: none;
            transition: all 0.25s ease;
            font-size: 0.925rem;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.03);
            color: #fff;
            border-left-color: #4f46e5;
        }

        .sidebar ul li a i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .main-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-navbar {
            background: #ffffff;
            padding: 0 32px;
            height: 64px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-navbar .admin-profile {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .top-navbar .admin-name {
            font-weight: 500;
            color: #1a2236;
            font-size: 0.925rem;
        }

        .top-navbar .top-actions a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .top-navbar .top-actions a:hover {
            color: #4f46e5;
        }

        .admin-content {
            padding: 32px;
            flex-grow: 1;
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <h2 class="logo">
                <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 0C7.163 0 0 7.163 0 16s7.163 16 16 16 16-7.163 16-16S24.837 0 16 0zm-4.5 24.5l-6-6 2.827-2.827L11.5 18.344l8.673-8.673 2.827 2.827L11.5 24.5z" fill="#4F46E5"/>
                </svg>
                Brain Gym Admin
            </h2>
            <ul>
                <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>"><i class="fas fa-chart-line"></i>Dashboard</a></li>
                <li><a href="gallery.php" class="<?= ($current_page == 'gallery.php') ? 'active' : '' ?>"><i class="fas fa-images"></i> Gallery</a></li>
                <li><a href="competitions.php" class="<?= ($current_page == 'competitions.php') ? 'active' : '' ?>"><i class="fas fa-trophy"></i>Competitions</a></li>
                <li><a href="appointments.php" class="<?= ($current_page == 'appointments.php') ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i>Appointments</a></li>
                <li><a href="notices.php" class="<?= ($current_page == 'notices.php' || $current_page == 'add-notice.php' || $current_page == 'edit-notice.php') ? 'active' : '' ?>"><i class="fas fa-bullhorn"></i>Notices</a></li>
                <li><a href="users.php" class="<?= ($current_page == 'users.php' || $current_page == 'edit-user.php') ? 'active' : '' ?>"><i class="fas fa-users"></i>Students Management</a></li>
                <li><a href="settings.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"><i class="fas fa-cog"></i>System Settings</a></li>
            </ul>
        </aside>

        <main class="main-panel">
            <div class="top-navbar">
                <div class="admin-profile">
                    <div class="admin-name">Welcome back, <strong>Admin</strong></div>
                </div>
                <div class="top-actions">
                    <a href="notifications.php"><i class="fas fa-bell"></i>Notifications</a>
                    <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
                </div>
            </div>
            