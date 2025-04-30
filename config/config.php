<?php 
    require __DIR__ . '/../vendor/autoload.php';

    if (file_exists(__DIR__ . '/../.env')) {
        Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();
    }
    
    // Define constants that will be used for database connection
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_NAME', $_ENV['DB_NAME']);
    
    // Define constants for media data store
    define('MEDIA_STORE_API_KEY', $_ENV['MEDIA_STORE_API_KEY']);
    define('MEDIA_STORE_API_SECRET', $_ENV['MEDIA_STORE_API_SECRET']);
    define('MEDIA_STORE_NAME', $_ENV['MEDIA_STORE_NAME']);

    // Define constants for email service
    define('EMAIL_SERVICE_PASSWORD', $_ENV['EMAIL_SERVICE_PASSWORD']);
    define('EMAIL_SERVICE_SENDER', $_ENV['EMAIL_SERVICE_SENDER']);
    define('EMAIL_SERVICE_HOST', $_ENV['EMAIL_SERVICE_HOST']);
    define('EMAIL_SERVICE_PORT', $_ENV['EMAIL_SERVICE_PORT']);
?>