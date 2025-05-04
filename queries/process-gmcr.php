<?php

session_start();

require_once(__DIR__ . "/../config/utils.php");
$db_conn = require(__DIR__ . "/../db/db_conn.php");
require_once(__DIR__ . "/students.php");
require_once(__DIR__ . "/../db/media_store.php");
require_once(__DIR__ . "/../services/email-service.php"); // Include email service
require_once(__DIR__ . "/../config/config.php"); // Include config for EMAIL_SERVICE_SENDER

// Check if the student is logged in
if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
    header("location: ../service-portal/login.php");
    exit();
}

// Initialize mailer service
$mailer = createMailerService();

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

        // --- Email Sending Logic ---
        $mailer->clearAddresses();
        $mailer->clearAttachments();
        $mailer->clearAllRecipients();
        $mailer->addAddress($email, $firstName . ' ' . $lastName);
        $mailer->Subject = "Your Good Moral Certificate Request Confirmation";
        $mailer->Body = "
            Hello " . htmlspecialchars($firstName) . " " . htmlspecialchars($lastName) . "!<br><br>
            Thank you for submitting your request for a Good Moral Certificate. Below are the details you provided:<br><br>
            <b>Student Name:</b> " . htmlspecialchars($firstName . ' ' . $middleName . ' ' . $lastName . ($suffix ? ' ' . $suffix : '')) . "<br>
            <b>Student No.:</b> " . htmlspecialchars($studentNo) . "<br>
            <b>Email:</b> " . htmlspecialchars($email) . "<br>
            <b>Contact No.:</b> " . htmlspecialchars($contactNo) . "<br>
            <b>Program:</b> " . htmlspecialchars(getProgramName($db_conn, $programId)) . "<br>
            <b>Starting School Year:</b> " . htmlspecialchars($startSchoolYear) . "<br>
            <b>Last School Year:</b> " . htmlspecialchars($endSchoolYear) . "<br>
            <b>Last Semester Attended:</b> " . htmlspecialchars($lastSemester) . "<br>
            <b>Reason for Request:</b> " . htmlspecialchars($reason) . ($specifyReason ? ' (Specified: ' . htmlspecialchars($specifyReason) . ')' : '') . "<br>
            <b>Date of Request:</b> " . date("M j, Y") . "<br><br>
            We will process your request and notify you once it's ready for collection. Please keep an eye on your email for updates.<br><br>
            <i>For any questions, please contact us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
            Best regards,<br>
            QCU Guidance and Counseling Office
        ";

        if (!$mailer->send()) {
            $response['message'] .= ' However, there was an error sending the confirmation email. Please contact the office.';
        }

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

function getProgramName($db, $programId) {
    $stmt = $db->prepare("SELECT program_name FROM programs WHERE program_id = ?");
    $stmt->bind_param("i", $programId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['program_name'];
    }
    return 'N/A';
}