<?php

session_start();

require_once(__DIR__ . "/../config/utils.php");
$db_conn = require(__DIR__ . "/../db/db_conn.php");
require_once(__DIR__ . "/students.php");

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


    if (isset($_FILES['proofOfImage'])) {
        if ($_FILES['proofOfImage']['error'] === UPLOAD_ERR_NO_FILE) {
            // No file was uploaded, this is an expected case
        } else {
            // An error occurred during upload (even if you're not saving it)
            error_log("Image upload error (not saved): " . $_FILES['proofOfImage']['error']);
            $response = ['status' => 'error', 'message' => 'Error with image upload. Please try again.'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    // Call the submitGmcRequest function from students.php with NULL for proofImgUrl
    if (submitGmcRequest(
        $db_conn, $firstName, $middleName, $lastName, $suffix,
        $studentNo, $programId, $startSchoolYear, $endSchoolYear,
        $lastSemester, $reason, $specifyReason, NULL, $contactNo, $email
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