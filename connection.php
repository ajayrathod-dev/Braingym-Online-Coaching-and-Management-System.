<?php
// Centralized Database configuration
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "codecrav_boss";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} 
// Purposely omitting closing PHP tag per best practices to avoid headers-already-sent errors
