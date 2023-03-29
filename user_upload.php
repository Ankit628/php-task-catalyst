<?php

// Define command line options
$options = getopt("u:p:h:d:", ["file:", "create_table", "dry_run", "help"]);

// Show help if requested or if no options were provided
if (isset($options['help']) || count($options) == 0) {
    echo "Usage: php user_upload.php [OPTIONS]\n";
    echo "Options:\n";
    echo "  --file [csv file name]  Name of the CSV file to be parsed\n";
    echo "  --create_table          Build the MySQL users table and exit\n";
    echo "  --dry_run               Parse the CSV file but don't insert into the database\n";
    echo "  -u                      MySQL username\n";
    echo "  -p                      MySQL password\n";
    echo "  -h                      MySQL host\n";
    echo "  -d                      MySQL database\n";
    echo "  --help                  Show this help message\n";
    exit();
}

// Set MySQL connection details
$username = $options['u'];
$password = $options['p'];
$servername = $options['h'];
$dbname = $options['d'];


// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define SQL query to create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE
)";

// Create users table
if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}
// Check if the create_table directive was provided
if (isset($options['create_table'])) {
    echo "Table 'users' created successfully.\n";
    exit();
}

// Check if the file directive was provided
if (!isset($options['file'])) {
    echo "Error: No CSV file specified.\n";
    exit(1);
}

// Load CSV file into memory
$filename = $options['file'];
if (!file_exists($filename)) {
    echo "Error: CSV file not found.\n";
    exit(1);
}

// Check if the dry_run directive was provided
$dry_run = isset($options['dry_run']);

// Parse CSV file
$row = 1;
if (($handle = fopen($filename, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Skip header row
        if ($row == 1) {
            $row++;
            continue;
        }

        $email = strtolower(filter_var($data[2], FILTER_SANITIZE_EMAIL));

        // Validate email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format: " . $data[2] . "\n";
            continue;
        }

        // Capitalize name and surname, and lowercase email
        $name = ucwords(strtolower(filter_var($data[0], FILTER_SANITIZE_ADD_SLASHES)));
        $surname = ucwords(strtolower(filter_var($data[1], FILTER_SANITIZE_ADD_SLASHES)));

        if (!$dry_run):
            // Prepare the SQL statement
            $stmt = $conn->prepare("INSERT INTO users (name, surname, email) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE email=email;");

            // Bind the parameters to the statement
            $stmt->bind_param("sss", $name, $surname, $email);

            // Execute the statement
            if ($stmt->execute() === FALSE) {
                echo "Error inserting record: " . $stmt->error . "\n";
            }

            // Close the statement and database connection
            $stmt->close();
        else:
            echo "Name: $name\nSurname: $surname\nEmail: $email\n\n";
        endif;
        $row++;
    }
    fclose($handle);
}

if (!$dry_run):
// Close database connection
    $conn->close();
endif;