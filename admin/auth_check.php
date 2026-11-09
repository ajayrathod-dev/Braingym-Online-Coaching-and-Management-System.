<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Optional: Add role-based access control
$allowed_roles = ['superadmin', 'admin'];
if (isset($_SESSION['admin_role']) && !in_array($_SESSION['admin_role'], $allowed_roles)) {
    // Role exists but is not allowed
    session_destroy();
    header("Location: login.php?error=unauthorized");
    exit();
}