<?php
    require(__DIR__ . "/../db/db_conn.php");
    require_once(__DIR__ . "/../config/utils.php");
    require_once(__DIR__ . "/../db/media_store.php");

    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'delete' && isset($_POST['announcement_id'])) {
            // Your existing delete logic - no image path changes here
            $announcement_id = filter_input(INPUT_POST, 'announcement_id', FILTER_SANITIZE_NUMBER_INT);
            if ($announcement_id) {
                $sql = "DELETE FROM announcements WHERE announcement_id = ?";
                $stmt = $db_conn->prepare($sql);
                $stmt->bind_param("i", $announcement_id);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Announcement deleted successfully.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Error deleting announcement: ' . $db_conn->error];
                }
                $stmt->close();
            } else {
                $response = ['status' => 'error', 'message' => 'Invalid announcement ID for deletion.'];
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();

        } elseif ($action === 'edit' && isset($_POST['announcement_id']) && isset($_POST['title']) && isset($_POST['description'])) {
            $announcement_id = filter_input(INPUT_POST, 'announcement_id', FILTER_SANITIZE_NUMBER_INT);
            $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $response = ['status' => 'success', 'message' => 'Announcement updated successfully.']; // Assume success initially

            if ($announcement_id && !empty($title) && !empty($description)) {
                $sql = "UPDATE announcements SET title = ?, description = ?, updated_at = NOW() WHERE announcement_id = ?";
                $stmt = $db_conn->prepare($sql);
                $stmt->bind_param("ssi", $title, $description, $announcement_id);

                if (!$stmt->execute()) {
                    $response['status'] = 'error';
                    $response['message'] = 'Error updating announcement details: ' . $db_conn->error;
                }
                $stmt->close();
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Missing or invalid data for updating announcement.';
            }

            // Handle image upload if a new image is present using direct Cloudinary
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image']['tmp_name'];
                $filename = $_FILES['image']['name'];
                $mimeType = $_FILES['image']['type'];

                $mediaStore = getMediaStore();
                try {
                    $uploadResult = $mediaStore->uploadApi()->upload(
                        $file,
                        ['folder' => 'announcements'] 
                    );

                    error_log("Cloudinary Upload Result: " . print_r($uploadResult, true)); // Log the Cloudinary result

                    if ($uploadResult && isset($uploadResult['secure_url'])) {
                        $imageUrl = $uploadResult['secure_url'];
                        $sqlImage = "UPDATE announcements SET img_url = ?, updated_at = NOW() WHERE announcement_id = ?";
                        $stmtImage = $db_conn->prepare($sqlImage);
                        $stmtImage->bind_param("si", $imageUrl, $announcement_id);
                        if ($stmtImage->execute()) {
                            error_log("Database Image URL updated successfully to: " . $imageUrl); // Log successful update
                            $response['new_image_url'] = $imageUrl; // Send back the URL from media_store
                        } else {
                            $response['warning'] = 'Announcement updated, but failed to update image URL in database: ' . $db_conn->error;
                            error_log("Database Image URL update failed: " . $db_conn->error); // Log database error
                        }
                        $stmtImage->close();
                    } else {
                        $response['warning'] = 'Announcement updated, but media store did not return a valid image URL.';
                        error_log("Cloudinary did not return a valid image URL."); // Log Cloudinary issue
                    }
                } catch (\Cloudinary\Api\Exception\ApiException $e) {
                    error_log("Cloudinary upload error during edit: " . $e->getMessage());
                    $response['warning'] = 'Announcement updated, but failed to upload new image to media store.';
                }
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit();

        } else {
            $response = ['status' => 'error', 'message' => 'Invalid action requested.'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    } else {
        $response = ['status' => 'error', 'message' => 'No action specified.'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    $db_conn->close();
?>