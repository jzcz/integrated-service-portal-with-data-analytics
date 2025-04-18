<?php 
    require __DIR__ . '/../vendor/autoload.php';
    Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..')->load();

    // Define constants that will be used for database connection
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_NAME', $_ENV['DB_NAME']);
?>