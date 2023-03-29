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
// Define CSV file path
$csv_file = __DIR__ . "/users.csv";

// Check if CSV file exists
if (!file_exists($csv_file)) {
    die("CSV file does not exist");
}

// Define SQL query to create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL
)";

// Create users table
if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Parse CSV file
$row = 1;
if (($handle = fopen($csv_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Skip header row
        if ($row == 1) {
            $row++;
            continue;
        }
        // Capitalize name and surname, and lowercase email
        $name = ucwords(strtolower($data[0]));
        $surname = ucwords(strtolower($data[1]));
        $email = strtolower($data[2]);
        // Insert user data into database
        $sql = "INSERT INTO users (name, surname, email) VALUES ('$name', '$surname', '$email')";
        if ($conn->query($sql) === FALSE) {
            echo "Error inserting record: " . $conn->error . "\n";
        }
        $row++;
    }
    fclose($handle);
}

// Close database connection
$conn->close();