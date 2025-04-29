<?php 
    require_once __DIR__ . '/../config/config.php'; 

    // Initialize database connection
    $db_conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Print error message and stop script execution if connection failed
    if ($db_conn->connect_error) {
        die("Connection to database failed: " . $db_conn->connect_error);
    }

    return $db_conn;
?>