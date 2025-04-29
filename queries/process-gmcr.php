<?php

session_start();

require_once(__DIR__ . "/../config/utils.php");
$db_conn = require(__DIR__ . "/../db/db_conn.php");
require_once(__DIR__ . "/students.php");
require_once(__DIR__ . "/../db/media_store.php");

// Check if the student is logged in
if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
    header("location: ../service-portal/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $studentId = $_SESSION['studentId'];
    $firstName = sanitizeData($db_conn, $_POST['firstName'] ?? '');
    $middleName = sanitizeData($db_conn, $_POST['middleName'] ?? '');
    $lastName = sanitizeData($db_conn, $_POST['lastName'] ?? '');
    $suffix = sanitizeData($db_conn, $_POST['suffix'] ?? '');
    $studentNo = sanitizeData($db_conn, $_POST['studentNo'] ?? '');
    $programId = sanitizeData($db_conn, $_POST['program'] ?? '');
    $startSchoolYear = sanitizeData($db_conn, $_POST['startSchoolYear'] ?? '');
    $endSchoolYear = sanitizeData($db_conn, $_POST['endSchoolYear'] ?? '');
    $lastSemester = sanitizeData($db_conn, $_POST['lastSemester'] ?? '');
    $reason = sanitizeData($db_conn, $_POST['reason'] ?? '');
    $specifyReason = sanitizeData($db_conn, $_POST['specifyReason'] ?? '');
    $contactNo = sanitizeData($db_conn, $_POST['contact_no'] ?? '');
    $email = sanitizeData($db_conn, $_POST['email'] ?? '');

    $proofImgUrl = NULL; // Initialize proofImgUrl

    if (isset($_FILES['proofOfImage']) && $_FILES['proofOfImage']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['proofOfImage']['tmp_name'];

        try {
            $mediaStore = getMediaStore();
            $uploadResult = $mediaStore->uploadApi()->upload($file, []); 
            $proofImgUrl = $uploadResult['secure_url']; 
        } catch (\Cloudinary\Api\Exception\ApiException $e) {
            error_log("Cloudinary upload error: " . $e->getMessage());
            $response = ['status' => 'error', 'message' => 'Error uploading image. Please try again.'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    } elseif (isset($_FILES['proofOfImage']) && $_FILES['proofOfImage']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors
        error_log("File upload error: " . $_FILES['proofOfImage']['error']);
        $response = ['status' => 'error', 'message' => 'Error uploading image. Please try again.'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // Call the submitGmcRequest function from students.php with the Cloudinary URL
    if (submitGmcRequest(
        $db_conn, $firstName, $middleName, $lastName, $suffix,
        $studentNo, $programId, $startSchoolYear, $endSchoolYear,
        $lastSemester, $reason, $specifyReason, $proofImgUrl, $contactNo, $email
    )) {
        $response = ['status' => 'success', 'message' => 'Your Good Moral Certificate request has been submitted successfully.'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        $response = ['status' => 'error', 'message' => 'There was an error submitting your request. Please try again.'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    $db_conn->close();

} else {
    // If accessed directly, redirect to the form
    header("Location: ../../views/service-portal/good-moral-cert-req-form.php");
    exit();
}

?>