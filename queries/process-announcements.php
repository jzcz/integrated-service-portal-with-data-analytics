<?php
session_start();
$db_conn = require(__DIR__ . "/../db/db_conn.php");
require_once("C:/xampp/htdocs/integrated-service-portal-with-data-analytics/config/config.php");
include(__DIR__ . "/../config/utils.php");
require_once(__DIR__ . "/../db/media_store.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['counselorId']) && isset($_SESSION['userId']) && $_SESSION['userRole'] === 'Counselor') {
    $title = sanitizeData($db_conn, $_POST['title']);
    $description = sanitizeData($db_conn, $_POST['description']);
    $createdBy = $_SESSION['counselorId'];
    $response = ['status' => '', 'message' => ''];
    $img_url = 'default_image.jpg'; // Default value if no image is not uploaded or upload fails


    // Handle image upload if an image is present
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['image']['tmp_name'];
        $mediaStore = getMediaStore();
        try {
            $uploadResult = $mediaStore->uploadApi()->upload($uploadedFile, [
                'folder' => 'announcements' // Optional: Specify a folder
            ]);
            if ($uploadResult && isset($uploadResult['secure_url'])) {
                $img_url = $uploadResult['secure_url'];
            } else {
                $response['status'] = 'warning';
                $response['message'] = 'Announcement created successfully, but image upload failed.';
            }
        } catch (\Cloudinary\Api\Exception\ApiException $e) {
            error_log("Cloudinary upload error: " . $e->getMessage());
            $response['status'] = 'warning';
            $response['message'] = 'Announcement created successfully, but image upload failed.';
        }
    }


    $sql = "INSERT INTO announcements (title, description, created_by, img_url) VALUES (?, ?, ?, ?)";
    $stmt = $db_conn->prepare($sql);
    $stmt->bind_param("ssis", $title, $description, $createdBy, $img_url);


    if ($stmt->execute()) {
        if (empty($response['message'])) {
            $response['status'] = 'success';
            $response['message'] = 'Announcement created successfully!';
        } else if ($response['status'] !== 'warning') {
            $response['status'] = 'success'; // Ensure success status if only the image upload failed
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error creating announcement in the database: ' . $stmt->error;
    }


    header('Content-Type: application/json');
    echo json_encode($response);
    $stmt->close();
    $db_conn->close();


} else {
    $response['status'] = 'error';
    $response['message'] = 'Unauthorized access.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

