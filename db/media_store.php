<?php 
    require __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../config/config.php';

    function getMediaStore() {
        $cloudinary = new \Cloudinary\Cloudinary([
            'cloud' => [
                'cloud_name' => MEDIA_STORE_NAME,
                'api_key' => MEDIA_STORE_API_KEY,
                'api_secret' => MEDIA_STORE_API_SECRET,
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
        return $cloudinary;
    }
?>