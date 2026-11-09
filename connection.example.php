<?php
/**
 * Database Configuration Template
 * 
 * Copy this file to connection.php and configure with your database credentials.
 */

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "braingym_db"; // Change to your local database name (e.g. codecrav_boss or braingym_db)

// Create MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Purposely omitting closing PHP tag per best practices to avoid headers-already-sent errors
