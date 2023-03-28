<?php
// Define database connection variables
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "catalyst_db";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
