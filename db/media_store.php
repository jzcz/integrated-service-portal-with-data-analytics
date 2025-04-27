<?php

require __DIR__ . '/../vendor/autoload.php';
require_once(__DIR__ . '/../config/config.php');

use Cloudinary\Cloudinary;
use Cloudinary\Uploader;
use Cloudinary\Api\Exception\ApiException;

class MediaStore {
    private $cloudinary;

    public function __construct() {
        $this->cloudinary = new Cloudinary([
            'cloud_name' => MEDIA_STORE_NAME,
            'api_key' => MEDIA_STORE_API_KEY,
            'api_secret' => MEDIA_STORE_API_SECRET,
        ]);
    }

    public function uploadImage($file, $folder = null) {
        try {
            $uploadOptions = [];
            if ($folder) {
                $uploadOptions['folder'] = $folder;
            }
            return Uploader::upload($file, $uploadOptions);
        } catch (ApiException $e) {
            error_log("Cloudinary API error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("General Cloudinary error: " . $e->getMessage());
            return false;
        }
    }
}

function getMediaStore(): MediaStore {
    static $mediaStore = null;
    if ($mediaStore === null) {
        $mediaStore = new MediaStore();
    }
    return $mediaStore;
}

?>