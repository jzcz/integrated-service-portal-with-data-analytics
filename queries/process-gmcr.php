<?php

session_start();

require_once(__DIR__ . "/../config/utils.php");
$db_conn = require(__DIR__ . "/../db/db_conn.php");
require_once(__DIR__ . "/students.php");
require_once(__DIR__ . "/../db/media_store.php"); // Include media_store.php

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

    // Handle the uploaded image using Cloudinary through media_store.php
    $proofOfImageFilename = null;
    $uploadError = null;
    if (isset($_FILES['proofOfImage']) && $_FILES['proofOfImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpName = $_FILES['proofOfImage']['tmp_name'];

        try {
            $uploadResult = getMediaStore()->uploadImage($fileTmpName, 'gmc_proofs');
            if (isset($uploadResult['secure_url'])) {
                $proofOfImageFilename = $uploadResult['secure_url'];
            } else {
                $uploadError = "Failed to upload image to Media Store.";
                error_log("Media Store upload error for user ID: " . $_SESSION['userId'] . ", result: " . print_r($uploadResult, true));
                header("Location: " . $_SERVER["PHP_SELF"] . "?upload_error=" . urlencode($uploadError));
                exit();
            }
        } catch (Exception $e) {
            $uploadError = "Error interacting with Media Store: " . $e->getMessage();
            error_log("Media Store interaction error for user ID: " . $_SESSION['userId'] . ", error: " . $e->getMessage());
            header("Location: " . $_SERVER["PHP_SELF"] . "?upload_error=" . urlencode($uploadError));
            exit();
        }
    } elseif (!isset($_FILES['proofOfImage']) || $_FILES['proofOfImage']['error'] === UPLOAD_ERR_NO_FILE) {
        $uploadError = "Please upload proof of your student status.";
        header("Location: " . $_SERVER["PHP_SELF"] . "?error_noimage=" . urlencode($uploadError));
        exit();
    } elseif (isset($_FILES['proofOfImage'])) {
        $uploadError = "Error uploading image. Code: " . $_FILES['proofOfImage']['error'];
        header("Location: " . $_SERVER["PHP_SELF"] . "?error_uploading=" . urlencode($uploadError));
        exit();
    }

    // Call the submitGmcRequest function from students.php
    if ($uploadError === null) {
        if (submitGmcRequest(
            $db_conn, $firstName, $middleName, $lastName, $suffix,
            $studentNo, $programId, $startSchoolYear, $endSchoolYear,
            $lastSemester, $reason, $specifyReason, $proofOfImageFilename
        )) {
            $success = "Your Good Moral Certificate request has been submitted successfully.";
            header("Location: " . $_SERVER["PHP_SELF"] . "?success=" . urlencode($success));
            exit();
        } else {
            $error = "There was an error submitting your request. Please try again.";
            header("Location: " . $_SERVER["PHP_SELF"] . "?error=" . urlencode($error));
            exit();
        }
    } else {
        // Redirect back to the form with the upload error
        header("Location: " . $_SERVER["PHP_SELF"] . "?upload_error=" . urlencode($uploadError));
        exit();
    }

    $db_conn->close();

} else {
    // If accessed directly, redirect to the form
    header("Location: ../../views/service-portal/good-moral-cert-req-form.php");
    exit();
}

?>