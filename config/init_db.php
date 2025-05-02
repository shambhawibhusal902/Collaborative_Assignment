<?php
require_once 'db_config.php';

// Read and execute the schema file
$schema = file_get_contents('schema.sql');
$queries = explode(';', $schema);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if (!mysqli_query($conn, $query)) {
            die("Error executing query: " . mysqli_error($conn) . "\nQuery: " . $query);
        }
    }
}

echo "Database tables created successfully!\n";

// Insert some sample data
// $sampleQueries = [
//     "INSERT INTO users (username, email, password_hash) VALUES 
//     ('admin', 'admin@example.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "')",
// ];

// foreach ($sampleQueries as $query) {
//     if (!mysqli_query($conn, $query)) {
//         echo "Error inserting sample data: " . mysqli_error($conn) . "\n";
//     }
// }

// echo "Sample data inserted successfully!\n";
mysqli_close($conn);
?>