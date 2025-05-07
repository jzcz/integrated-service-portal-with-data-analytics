<?php

session_start();

// check session first exists first
if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
    header("location: ../public/counselor-admin-login-page.php");
    exit();
}

include(__DIR__ . "/../../config/utils.php");
require_once(__DIR__ . "/../../queries/good-moral-cert-reqs.php");
require(__DIR__ . "/../../services/email-service.php");
$db_conn = require( __DIR__ . "/../../db/db_conn.php");
require(__DIR__ . "/../../queries/students.php");

$programs = getAllPrograms($db_conn);

$schoolYears = [];
for ($y = 1994; $y <= 2024; $y++) {
    $schoolYears[] = "$y-" . ($y + 1);
}

$enrollmentStatus = [
    "I am a BACHELOR Degree Student of this university",
    "I was a SENIOR HIGH SCHOOL Graduate/Undergraduate of this university.",
    "I was a TECHNICAL-VOCATIONAL Course Graduate/Undergraduate of this university.",
];

$reasons = [
    "Scholarship or Financial Assistance",
    "Enrollment or Transfer to other school",
    "Work/Employment",
    "Masteral/Graduate Studies",
    "PNP Application",
    "On-the-Job application or Internship (OJT)",
    "Application for 2nd course (For graduates only)"
];

$mailer = createMailerService();

function formatRedirectUrl($url, $key, $value) {
    $params = [ $key => $value ];
    
    if (parse_url($url, PHP_URL_QUERY)) {
        $newUrl = $url . '&' . http_build_query($params);
    } else { 
        $newUrl = $url . '?' . http_build_query($params);
    }

    return $newUrl;
}

$page = 1;
$statusOption = null;
$dateRange = null;
$success = null;
$err = null;


$statusClassName = array(
    "Pending" => "text-warning",
    "Approved" => "upcoming-status",
    "Declined" => "text-danger",
    "Completed" => "text-info",
    "For Pickup" => "declined-status",
);


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        
    if (isset($_GET["filter_form"])) {
        $statusOption = isset($_GET['status']) ? $_GET['status'] : null;
        $dateRange = isset($_GET['dateRange']) ? $_GET['dateRange'] : null;
    } 

    if(isset($_GET["page_form"])) {
        $statusOption = isset($_GET['status']) ? $_GET['status'] : null;
        $dateRange = isset($_GET['dateRange']) ? $_GET['dateRange'] : null;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
    }

    if(isset($_GET["err"])) {
        $success = null;
        $err = $_GET["err"];
    }

    if(isset($_GET["success"])) {
        $err = null;
        $success = $_GET["success"];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['form-action']) && $_POST['form-action'] == 'approve') {
        $gmcReqId = $_POST['gmc_req_id'];
        $email = $_POST['email'];

        $res = approveGoodMoralCertReq($db_conn, $gmcReqId);;
    
        if($res) {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success',
            "Successfully approved request! An email notification will be sent to " . $res['first_name'] . ' ' . $res['last_name'] . " for request approval.")
        );
        
        $mailer->clearAddresses();
        $mailer->clearAttachments();
        $mailer->clearAllRecipients();
        $mailer->addAddress($email, $res['first_name'] . ' ' . $res['last_name']);
        $mailer->Subject = "Good Moral Certificate Request Approved";
        $mailer->Body = "
            Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
            Your request for Good Moral Certificate has been approved and confirmed by the QCU Guidance and Counseling Office! Below are the details of your confirmed request:<br><br>
            <b>Request ID:</b> " . $res['gmc_req_id'] . "<br>
            <b>Request Status:</b> " . $res['status'] . "<br>
            <b>Requested on:</b> " . date("M j, Y, D", strtotime($res['created_at'])) . "<br>
            <i><b>Note:</b> Your request will now be processed by the QCU Guidance Counseling Office.</i><br><br>
            Thank you for your patience. You will be notified via email if there are any changes or updates to your request. <br><br>
            <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
            Best regards,<br>
            QCU Guidance and Counseling Office<br>
        ";

        $mailer->send();
        } else {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to approve request. Please try again later."));
        }
    } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'decline') {
    
        $gmcReqId = $_POST['gmc_req_id'];
        $declineReason = $_POST['decline-input'];
        $email = $_POST['email'];

        $res = declineGMCReq($db_conn, $gmcReqId, $declineReason);

        if($res) {
        header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully declined the request! An email notification will be sent to " . $res['first_name'] . ' ' . $res['last_name'] . " for request decline."));
            $mailer->clearAddresses();
            $mailer->clearAttachments();
            $mailer->clearAllRecipients();
            $mailer->addAddress($email, $res['first_name'] . ' ' . $res['last_name']);
            $mailer->Subject = "Good Moral Certificate Request Declined";
            $mailer->Body = "
                Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
                We regret to inform you that your Good Moral Certificate request submitted on " . date("M j, Y, D", strtotime($res['created_at'])) . " was declined by the QCU Guidance and Counseling Office. Below are the details of your declined request:<br><br>
                <b>Request ID:</b> " . $res['gmc_req_id'] . "<br>
                <b>Request Status:</b> " . $res['status'] . "<br>
                <b>Decline Reason:</b> " . $res['decline_reason'] . "<br><br>
                If you have any questions or would like to request another Good Moral Certificate request, please feel free to submit a new one through the QCU Guidance and Counseling service portal at your convenience.<br><br>
                <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
                Best regards,<br>
                QCU Guidance and Counseling Office<br>
            ";
            
        $mailer->send();
        } else {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to decline request. Please try again later."));
        }
    } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'set-pickup-date') {
        $gmcReqId = $_POST['gmc_req_id'];
        $date = $_POST['pickup_date'];
        $email = $_POST['email'];

        $res = setPickUpDate($db_conn, $gmcReqId, $date);

        if($res) {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully added a pickup date! An email notification will be sent to " . $res['first_name'] . ' ' . $res['last_name'] . " for the pickup date."));
            $mailer->clearAddresses();
            $mailer->clearAttachments();
            $mailer->clearAllRecipients();
            $mailer->addAddress($email, $res['first_name'] . ' ' . $res['last_name']);
            $mailer->Subject = "Good Moral Certificate Ready for Pickup";
            $mailer->Body = "
                Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
                We would like to inform you that your request for Good Moral Certificate has been processed successfully! The request submitted on " . date("M j, Y, D", strtotime($res['created_at'])) . " has been updated. Below are the details of your request:<br><br>
                <b>Request ID:</b> " . $res['gmc_req_id'] . "<br>
                <b>Request Status:</b> " . $res['status'] . "<br>
                <b>Pickup Date:</b> " . date("M j, Y, D", strtotime($res['pickup_date'])) . "<br>
                <b>Note:</b> Please bring any kind of proof of identity when claiming your good moral certificate.<br><br>
                Thank you for your patience. You will be notified via email if there are any changes to your request. <br><br>
                <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
                Best regards,<br>
                QCU Guidance and Counseling Office<br>
                ";
                
        $mailer->send();
        } else {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to cancel appointment. Please try again later."));
        }
    } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'update') {
        $gmcReqId = $_POST['gmc_req_id'];
        $pickUpDate = $_POST['pickup_date'];
        $email = $_POST['email'];
        $res = setPickUpDate($db_conn, $gmcReqId, $pickUpDate);

        if($res) {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully updated pickup date! An email notification will be sent to " . $res['first_name'] . ' ' . $res['last_name'] . " for request updates."));
            $mailer->clearAddresses();
            $mailer->clearAttachments();
            $mailer->clearAllRecipients();
            $mailer->addAddress($email, $res['first_name'] . ' ' . $res['last_name']);
            $mailer->Subject = "Updated Pickup Date Details";
            $mailer->Body = "
            Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
            We would like to inform you that your pickup date request submitted on ". date("M j, Y, D", strtotime($res['created_at'])). " has been updated. Below are the new details of your updated pickup date:<br><br>
            <b>Request ID:</b> " . $res['gmc_req_id'] . "<br>
            <b>Request Status:</b> " . $res['status'] . "<br>
            <b>Pickup Date:</b> " . date("M j, Y, D", strtotime($res['pickup_date'])) . "<br>
            <i><b>Note:</b> Please bring any kind of proof of identity when claiming your good moral certiicate.</i><br><br>
            Thank you for your patience. You will be notified via email if there are any changes to your request. <br><br>
            <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
            Best regards,<br>
            QCU Guidance and Counseling Office<br>
            ";
            
        $mailer->send();
        } else {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to update appointment details. Please try again later."));
        }

    } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'complete') {
        $apptId = $_POST['gmc_req_id'];
        $res = completeGMCReq($db_conn, $apptId);

        if($res) {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully completed appointment!"));
        } else {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to complete appointment. Please try again later."));
        }
    } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'delete') {
        $gmcId = $_POST['gmc_req_id'];
          
        $res = deleteGMCReq($db_conn, $gmcId);
        if($res) {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully deleted request!"));
        } else {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to delete request. Please try again later."));
        }
    } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'add-new-req') {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $middle_name = $_POST['middle_name'] ? $_POST['middle_name'] : null;
        $suffix = $_POST['suffix'] ? $_POST['suffix'] : null;
        $student_no = $_POST['student_no'];
        $program_id = $_POST['program_id'];
        $email = $_POST['email'];
        $contact_no = $_POST['contact_no'];
        $start_school_year = $_POST['start_school_year'];
        $last_school_year = $_POST['last_school_year'];
        $semester = $_POST['semester'];
        $graduate_status = $_POST['graduate_status'];
        $reason_desc = $_POST['reason_des'];
        $pickup_date = $_POST['pickup-date'];
        $graduate_status = $_POST['graduate_status'];

        $res = addGMCReq(
            $db_conn,
            $first_name,
            $last_name,
            $middle_name,
            $suffix,
            $student_no,
            $program_id,
            $email,
            $contact_no,
            $start_school_year,
            $last_school_year,
            $semester,
            $graduate_status,
            $reason_desc,
            $pickup_date
        );

        if($res) {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully added request!"));
        } else {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to add request. Please try again later."));
        }
    }
}

$results = getGoodMoralCertReqs($db_conn, $page, $statusOption, $dateRange);
$db_conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/counselor.css">
    <style> 
        .appt-tbl {
            font-size: 15px;
        }

        .status-circle {
            font-size: 6px;
        }

        .form-select {  
            font-size: 14px;
        }

        table {
            font-size: 14px;
        }

        thead {
            border-bottom: 2px #dee1e6 solid;
            background-color: red;
        }

        .appt-header-bar {
            width: 100%;
            background-color:rgb(222, 237, 251);
            border-bottom: 2px #9DCEFF solid;
        }

        .appt-header-bar h5{
            color: var(--primary-color);
        }

        .table-wrapper, .appt-controls-bar, .appt-page-nav-wrapper {
            padding: 0 34px;
        }
        .appt-controls-bar {
            margin-bottom: 12px
        }
        .add-appt-btn {
            background-color: var(--primary-color);
            border: none;
        }
        .page-nav-link{
            background-color: var(--primary-color);
            color: white;
        }

        .page-nav-link:hover{
            background-color: var(--primary-color);
            color: white;
        }

        label {
            font-size: 14px;
        }

        .upcoming-status {
            color:rgb(71, 194, 132);
        }

        .appt-action-btn {
            transition: transform .2s;
        }

        .appt-action-btn:hover {
            transform: scale(1.5)
        }

        main .modal-header {
            background-color: white;
        }

        .status-input-wrapper {
            position: relative;
        }

        .status-input-wrapper > i {
            position: absolute;
            top: 12px;
            left: 15px;
        }

        .status-input-wrapper > input {
            padding-left: 28px;
        }

        .status-input-wrapper > select {
            padding-left: 28px;
        }

        .col > label {
            font-size: 12px;
        }

        .view-analytics-appt-btn {
            width: 160px;
        }

        .declined-status {
            color:rgb(255, 133, 67);
        }

        .upcoming-status {
            color:rgb(71, 194, 132);
        }
    </style>
</head>

<body>
    <?php 
        include(__DIR__ . '/../components/counselor/sidebar.php');
    ?>
        <!-- MESSAGE MODAL START -->
    <div class="modal fade" id="message-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header 
          <?php echo $err ? "bg-danger"  : "bg-success" ?>
          ">
            <h5 class="modal-title text-white fs-6" id="exampleModalLabel">
            <i class="bi bi-check-circle"></i>
              Success
            </h5>
          </div>
          <div class="modal-body">
            <?php echo $err ??  $err ?>
            <?php echo $success ??  $success ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light border-secondary-subtle" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- MESSAGE MODAL END -->

    <main>
        <div class="appt-header-bar px-4 py-3 d-flex align-items-center mb-4">
            <h5 class="mb-0 fw-bold">Good Moral Certificate Requests</h5>
        </div>
        <div class="appt-controls-bar d-flex gap-3 align-items-center">
            <form action="" name="filter_form" class="d-flex gap-3 align-items-center" method="get">
                <input type="hidden" name="filter_form" value="1">
                    <div class="d-flex align-items-center gap-2">
                        <label for="status">Status:</label>
                        <select name="status" id="" class="form-select" >
                            <option value="" <?php echo $statusOption == null ? 'selected' : ''; ?>>Default</option>
                            <option value="Pending" <?php echo ($statusOption == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="For Pickup" <?php echo ($statusOption == 'For Pickup') ? 'selected' : ''; ?>>For Pickup</option>
                            <option value="Approved" <?php echo ($statusOption == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="Completed" <?php echo ($statusOption == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="Declined" <?php echo ($statusOption == 'Declined') ? 'selected' : ''; ?>>Declined</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2 ">
                        <label for="" class="w-100">Date Range:</label>
                        <select name="dateRange" id="" class="form-select" value>
                            <option value="" selected>Default</option>
                            <option value="today" <?php echo ($dateRange == 'today') ? 'selected' : ''; ?>>Today</option>
                            <option value="this week" <?php echo ($dateRange == 'this week') ? 'selected' : ''; ?>>This Week</option>
                            <option value="this month" <?php echo ($dateRange == 'this month') ? 'selected' : ''; ?>>This Month</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-md" style="background-color: var(--primary-color); color: white;">Filter</button>
                </form>
            
            <div class="ms-auto ">
                <button class="btn btn-primary add-appt-btn" type="button" data-bs-toggle="modal" data-bs-target="#add-new-req-modal">
                    <i class="bi bi-plus-circle"></i>
                    Add New
                </button>
            </div>

            <!-- Add Modal -->
                <div class="modal fade" id="add-new-req-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <form action="" method="post">
                        <input type="text" name="form-action" id="add-new-req" value="add-new-req" hidden>
                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                            <div class="modal-content">
                            <div class="modal-header">
                                <p class="modal-title fw-bold" id="exampleModalLabel">Good Moral Certificate Request Form</p>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <div class="row mb-2">
                                        <p class="fw-bold mb-0">Student Details</p>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="">First Name</label>
                                            <input class="form-control" value="" type="text" name="first_name" id="">
                                        </div>
                                        <div class="col">
                                            <label for="">Last Name</label>
                                            <input class="form-control" value="" type="text" name="last_name" id="">
                                        </div>
                                        <div class="col">
                                            <label for="">Middle Name</label>
                                            <input class="form-control" value="" type="text" name="middle_name" id="">
                                        </div>
                                        <div class="col">
                                            <label for="">Suffix</label>
                                            <select class="form-select" name="suffix" id="">
                                                <option value="" selected>None</option>
                                                <option value="Jr." >Jr</option>
                                                <option value="Sr." >Sr</option>
                                                <option value="III" >III</option>
                                                <option value="IV." >IV</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="">Student No.</label>
                                            <input class="form-control" value="" placeholder="Ex. xx-xxxx" type="text" name="student_no" id="">
                                        </div>
                                        <div class="col">
                                            <label for="">Program</label>
                                            <select class="form-select" name="program_id" id="">
                                                <option value="" selected disabled>Select Program</option>
                                                <?php foreach($programs as $program) { ?>
                                                    <option value="<?php echo $program['program_id'] ?>"><?php echo $program['program_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="">Email</label>
                                            <input class="form-control" value="" type="email" name="email" id="">
                                        </div>
                                        <div class="col">
                                            <label for="">Contact No.</label>
                                            <input class="form-control" value="" type="text" name="contact_no" id="">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="">Starting School Year</label>
                                            <select class="form-select" name="start_school_year" id="">
                                                <option value="" selected disabled>Select School Year</option>
                                                <?php foreach($schoolYears as $s) { ?>
                                                    <option value="<?php echo $s ?>"><?php echo $s ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="">Last School Year</label>
                                            <select class="form-select" name="last_school_year" id="">
                                                <option value="" selected disabled>Select School Year</option>
                                                <?php foreach($schoolYears as $s) { ?>
                                                    <option value="<?php echo $s ?>"><?php echo $s ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="">Last Semester Attended</label>
                                            <select class="form-select" name="semester" id="">
                                                <option value="" selected disabled>Semester</option>
                                                <option value="1st">1st Semester</option>
                                                <option value="2nd">2nd Semester</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="">Graduate Status</label>
                                            <select class="form-select" name="graduate_status" id="">
                                                <option value="" selected disabled>Graduate Status</option>
                                                <?php foreach($enrollmentStatus as $status) { ?>
                                                    <option value="<?php echo $status ?>"><?php echo $status ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <p class="fw-bold mb-0">Request Details</p>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="">Reason for request</label>
                                            <select class="form-select" name="reason_des" id="">
                                                <option value="" selected disabled>Reason for request</option>
                                                <?php foreach($reasons as $r) { ?>
                                                    <option value="<?php echo $r ?>"><?php echo $r ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label for="">Pickup Date</label>
                                            <input type="text" value="<?php echo date('Y-m-d') ?>" class="form-control" name="pickup-date" id="" hidden>
                                            <input type="text" value="<?php echo date('M j, Y, D') ?>" class="form-control" id="" readonly>
                                            <label for=""><i>*Walk-in requests for the Good Moral Certificate are processed and released the same day. </i></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Request</button>
                            </div>
                            </div>
                        </div>
                    </form>
                </div>
            <!-- Add Modal -->
        </div>
        <div class="table-wrapper">
            <table class="table-bordered rounded table text-center">
                <thead class="">
                    <tr class="">
                        <th scope="col mb-2" class="border-end-0">#</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Name</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Student No.</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Request Submitted At</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Pick Up Date</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Status</th>
                        <th scope="col mb-2" class="border-start-0">Actions</th>
                    </tr>
                </thead>
                <tbody class="appt-tbl">
                <?php foreach($results as $i => $a) { ?>
                    <tr>
                        <td class="border-end-0 text-light-emphasis"><?php echo $a['gmc_req_id'] ?></td>
                        <td class="border-start-0 border-end-0 text-light-emphasis"><?php echo $a['first_name'] . ' ' . $a['last_name'] ?></td>
                        <td class="border-start-0 border-end-0 text-light-emphasis"><?php echo $a['student_no'] ?></td>
                        <td class="border-start-0 border-end-0 text-light-emphasis">
                            <?php echo date("M j, Y, D", strtotime($a['created_at'])) ?>
                        </td>
                        <td class="border-start-0 border-end-0 text-light-emphasis">
                            <?php echo ($a['pickup_date']) ?  date("M j, Y, D", strtotime($a['pickup_date'])) : '--.--' ?>
                        </td>
                        <td class="border-start-0 border-end-0 text-light-emphasis">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-circle-fill text-in status-circle <?php echo $statusClassName[$a['status']] ?>"></i>
                                <span><?php echo $a['status'] ?></span>
                            </div>
                        </td>
                        <td class="border-start-0 text-light-emphasis d-flex align-items-center justify-content-center gap-3">
                            <i style="cursor: pointer;" class="appt-action-btn bi bi-eye-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'appt-details-modal' . ($i + 1) ?>"></i>
                            <i style="cursor: pointer;" class="appt-action-btn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'edit-appt-modal' . ($i + 1) ?>"></i>
                            <i style="cursor: pointer;" class="appt-action-btn bi bi-trash3-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'delete-appt-modal' . ($i + 1) ?>"></i>
                        </td>
                    </tr>

                        <!--  VIEW MODAL START -->
                        <div class="modal fade" id="<?php echo 'appt-details-modal' . $i + 1?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">  <!-- modal-fullscreen -->
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Request Details</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="px-1 gap-2">
                                        <div class="row">
                                            <p class="fw-bold">Student Details</p>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">First Name</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['first_name']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Last Name</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['last_name']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Middle Name</label>
                                                <input type="text" name="" class=" form-control form-control-sm" id="" value="<?php echo ($a['middle_name'] ? $a['middle_name'] : '') ?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Suffix</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo ($a['suffix'] ? $a['suffix'] : '') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                                <div class="col">
                                                    <label class="text-secondary mb-1" for="">Email</label>
                                                    <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['email']?>" readonly>
                                                </div>
                                                <div class="col">
                                                    <label class="text-secondary mb-1" for="">Contact Number</label>
                                                    <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['contact_no']?>" readonly>
                                                </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Student No.</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['student_no']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Program</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['program_name']?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Start School Year</label>
                                                <input type="text" name="" class=" form-control form-control-sm" id="" value="<?php echo $a['start_school_year'] ?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">End School Year</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['last_school_year'] ?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Last Semester Attended</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['semester'] ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Graduate Status</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['graduate_status'] ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <p class="fw-bold">Request Details</p>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Pick Up Date</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['pickup_date'] ? date("M j, Y, D", strtotime($a['pickup_date'])) : "--.--"?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Request Status</label>
                                                <div class="status-input-wrapper">
                                                    <i class="bi bi-circle-fill text-in status-circle <?php echo $statusClassName[$a['status']]?>"></i>
                                                    <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['status']?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($a['status'] == "Declined") { ?>
                                            <div class="row mb-2">
                                                <div class="col">
                                                    <label class="text-secondary mb-1" for="">Reason for declining request:</label>
                                                    <textarea type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['decline_reason'] ?>" readonly><?php echo $a['decline_reason'] ?></textarea>
                                                </div>
                                            </div>
                                        <?php }?>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Reason for Request</label>
                                                <textarea type="text" name="" height="2" class="form-control form-control-sm" id="" value="" readonly><?php echo $a['reason_desc'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Additional information about request</label>
                                                <textarea type="text" name="" height="2" class="form-control form-control-sm" id="" value="" readonly><?php echo $a['additional_req_des'] ? $a['additional_req_des'] : "None"?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="form-label">Proof Image</label>
                                                <div class="input-group input-group-sm flex-nowrap">   
                                                <input type="text" readonly class="form-control" value="<?php echo $a['proof_img_url'] ? basename($a['proof_img_url']) : 'No file for walk-in requests.' ?>" aria-describedby="addon-wrapping">
                                                <?php if( $a['proof_img_url']) { ?>
                                                    <a href="<?php echo $a['proof_img_url']?>" class="input-group-text link-underline-opacity-0" target="_blank" rel="noopener noreferrer" id="addon-wrapping"><i class="bi bi-eye-fill"></i></a>    
                                                <?php }  ?>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Request submitted on</label>
                                                <input type="text" class="form-control-sm form-control" value="<?php echo date("M j, Y, D", strtotime($a['created_at'])) ?>" aria-describedby="addon-wrapping" readonly>
                                            </div>
                                        </div>
                                </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                                </div>
                            </div>
                        </div>
                        <!--  DELETE MODAL START -->
                        <div class="modal fade" id="<?php echo 'delete-appt-modal' . $i + 1?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Confirm Deletion</h5>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete the Request?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger delete-btn" data-id-target="<?php echo $i?>" >Delete</button>
                                </div>
                                </div>
                            </div>
                        </div>
                        <!--  DELETE MODAL END -->

                        <!--  EDIT MODAL START -->
                        <div class="modal fade" id="<?php echo 'edit-appt-modal' . $i + 1?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <form action="" method="POST" id="form-<?php echo $i ?>">  
                            <input type="text" name="gmc_req_id" id="" value="<?php echo $a['gmc_req_id']?>" hidden>
                            <input type="text" name="form-action" id="form-action-<?php echo $i ?>" value="" hidden>
                            <input type="text" name="email" value="<?php echo $a['email']?>" hidden>  
                            <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Request</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Current Status of the Good Moral Certificate Request:</label>
                                                    <div class="status-input-wrapper">
                                                        <i class="bi bi-circle-fill text-in status-circle <?php echo $statusClassName[$a['status']]?>" id="status-circle-<?php echo $i ?>"></i>
                                                        <input type="text" name="" class="form-control form-control-sm" id="status-text-<?php echo $i ?>" value="<?php echo $a['status']?>" readonly>
                                                    </div>
                                                </div>
                                                <?php if($a['status'] === "Pending") { ?>
                                                <div class="mb-2">
                                                    <label class="text-secondary mb-1" for="">Approve or Decline pending request for Good Moral Certificate?</label>
                                                    <div>
                                                        <button class="btn btn-success approve-btn" data-id-target="<?php echo $i ?>" type="button">Approve</button>
                                                        <button class="btn btn-danger decline-btn" data-id-target="<?php echo $i ?>" id="" type="button">Decline</button>
                                                    </div>
                                                </div>
                                                <div id="add-actions-div-confirm-decline">
                                                    <div id="decline-input-div-<?php echo $i ?>" class="visually-hidden">
                                                        <label class="d-inline-block" for="">State your reason for declining:</label>
                                                        <textarea name="decline-input" class="form-control mb-3" style="font-size: 14px" id="" value=""></textarea>
                                                    </div>
                                                    <div id="approve-input-div-<?php echo $i ?>" class="visually-hidden">
                                                        <div id="approve-text-div-<?php echo $i ?>" class="">
                                                            <label class="mb-2" for=""><b><i>Note:</i></b> The request status will be updated to Approved.</label>
                                                        </div>
                                                </div>
                                                <?php } ?>
                                                <?php if($a['status'] === "For Pickup") { ?>
                                                    <div>
                                                        <div class="mb-3">
                                                            <label class="mb-1" for="">Pickup Date</label>
                                                            <input type="date" class="form-control form-control-sm update-date-info-input" name="pickup_date"  data-id-target="<?php echo $i ?>" value="<?php echo $a['pickup_date']?>">
                                                            <label for="" class="fst-italic form-text">Click input to change pickup date.</label>
                                                        </div>
                                                        
                                                            <label class="mb-1" for="">Complete the request?</label>
                                                            <button class="btn btn-sm btn-info px-2 complete-btn" data-id-target="<?php echo $i ?>" id="complete-btn-<?php echo $i ?>" type="button">Complete</button>
                                                        
                                                        <div id="complete-text-div-<?php echo $i ?>" class="visually-hidden">
                                                            <label class="" for="">The request status will be updated to completed.</label>
                                                        </div>
                                                        <label class="mb-2" for=""><b><i>Note:</i></b> Updating the pickup date will notify the requestor of changes.</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if($a['status'] === "Approved") { ?>
                                                    <div>
                                                        <div class="mb-3">
                                                            <label class="mb-1" for="">Pickup Date</label>
                                                            <input type="date" class="form-control form-control-sm add-date-info-input" name="pickup_date"  data-id-target="<?php echo $i ?>" value="<?php echo $a['pickup_date']?>">
                                                            <label for="" class="fst-italic form-text">Click input to add a pickup date for Good Moral Certificate request</label>
                                                        </div>
                                                        <label class="mt-1 mb-2" for=""><b><i>Note:</i></b> Adding pickup date will notify the requestor of changes.</label>
                                                    </div>
                                                <?php } ?>
                                                <?php if($a['status'] === "Declined") { ?>
                                                    <div>
                                                        <label class="mb-1" for="">Reason for Declining Appointment:</label>
                                                        <textarea class="form-control mb-3" name="" value="<?php echo $a['decline_reason']; ?>" id="" style="font-size: 14px" readonly><?php echo $a['decline_reason']; ?></textarea>
                                                        <label for="" class="fst-italic form-text">*Declined requests cannot be edited further.</label> 
                                                    </div>
                                                <?php } ?>
                                                <?php if($a['status'] === "Completed") { ?>
                                                    <div>
                                                        <div class="mb-3">
                                                            <label class="mb-1" for="">Pickup Date</label>
                                                            <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo date('M j, Y, D', strtotime($a['pickup_date']))?>" readonly>
                                                        </div>
                                                        <label for="" class="fst-italic form-text">*Completed requests cannot be edited further.</label>
                                                    </div>
                                                <?php } ?>
                                                <div class="extra-actions-div">
                                                </div>
                                            
                                            </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <?php if($a['status'] === "Pending" || $a['status'] === "For Pickup" || $a['status'] === "Approved") { ?>
                                                <button type="submit" id="save-changes-btn-<?php echo $i ?>" data-id-target="<?php echo $i; ?>" class="btn btn-success save-changes-btn">Save changes</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!--  EDIT MODAL END -->
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="appt-page-nav-wrapper mt-4">
            <form action="" method="get">
            <input type="hidden" name="page_form" value="1">
            <input type="hidden" name="status" value="<?php echo $statusOption ?>">
            <input type="hidden" name="dateRange" value="<?php echo $dateRange; ?>">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item">
                            <button type="submit" name="page" value="<?php echo $page - 1; ?>" class="page-link page-nav-link <?php echo ($page == 1) ? 'disabled' : ''; ?>" <?php echo ($page == 1) ? 'disabled' : ''; ?>">Previous</button>
                        </li>
                        <li class="page-item"><button class="page-link text-black"><?php echo $page ?></button></li>
                        <li class="page-item">
                            <button type="submit" name="page" value=<?php echo $page + 1?> class="page-link page-nav-link">Next</button>
                        </li>
                    </ul>
                </nav>
            </form>
        </div>

        <!-- CONFIRM APPOINTMENT MODAL  -->
        <!-- CONFIRM APPOINTMENT MODAL  --> 
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const messageModal = new bootstrap.Modal(document.getElementById("message-modal"));
        const completeBtns = document.querySelectorAll('.complete-btn');
        const declineBtns = document.querySelectorAll('.decline-btn');
        const approveBtns = document.querySelectorAll('.approve-btn');
        const saveBtns = document.querySelectorAll('.save-changes-btn');
        const updateInfoInputs = document.querySelectorAll('.update-date-info-input');
        const addDateInputs = document.querySelectorAll('.add-date-info-input');
        const cancelBtns = document.querySelectorAll('.cancel-btn');
        const cancelInputDiv = document.getElementById('cancel-input-div');
        const saveChangesBtn = document.getElementById('save-changes-btn');
        const deleteBtns = document.querySelectorAll('.delete-btn');

        <?php  if($err || $success) { echo "messageModal.show();"; }  ?>

        declineBtns.forEach(function(btn) {
           btn.addEventListener('click', function() {
                const targetId =  btn.dataset.idTarget;
                const targetDeclineDiv = document.getElementById(`decline-input-div-${targetId}`);
                const targetApproveDiv = document.getElementById(`approve-input-div-${targetId}`);
                const targetForm = document.getElementById(`form-${targetId}`);
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                const targetStatusCircle = document.getElementById(`status-circle-${targetId}`);
                const targetStatusText = document.getElementById(`status-text-${targetId}`);

                targetStatusCircle.classList.remove("text-warning");
                targetStatusCircle.classList.remove("upcoming-status");
                targetStatusCircle.classList.add("text-danger");
                targetStatusText.value = 'Declined';
                
                targetFormAction.value = 'decline';
                targetApproveDiv.classList.remove("visually-hidden");
                targetApproveDiv.classList.add("visually-hidden");

                targetDeclineDiv.classList.remove("visually-hidden");            
            });
        });

        approveBtns.forEach(function(btn) {
           btn.addEventListener('click', function() {
            const targetId =  btn.dataset.idTarget;
                const targetDeclineDiv = document.getElementById(`decline-input-div-${targetId}`);
                const targetApproveDiv = document.getElementById(`approve-input-div-${targetId}`);
                const targetForm = document.getElementById(`form-${targetId}`);
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                const targetStatusCircle = document.getElementById(`status-circle-${targetId}`);
                const targetStatusText = document.getElementById(`status-text-${targetId}`);

                targetStatusCircle.classList.remove("text-warning");
                targetStatusCircle.classList.remove("text-danger");
                targetStatusCircle.classList.add("upcoming-status");
                targetStatusText.value = 'Approved';
                
                targetFormAction.value = 'approve';
                targetDeclineDiv.classList.remove("visually-hidden");
                targetDeclineDiv.classList.add("visually-hidden");

                targetApproveDiv.classList.remove("visually-hidden");            
            });
        });

        saveBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId =  btn.dataset.idTarget;
                const form = document.getElementById(`form-${targetId}`);
                form.submit();
            });
        });

        cancelBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId =  btn.dataset.idTarget;
                const targetCompleteTextDiv = document.getElementById(`complete-text-div-${targetId}`);
                const targetCancelDiv = document.getElementById(`cancel-input-div-${targetId}`);
                const targetForm = document.getElementById(`form-${targetId}`);
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                const targetStatusCircle = document.getElementById(`status-circle-${targetId}`);
                const targetStatusText = document.getElementById(`status-text-${targetId}`);

                targetStatusCircle.classList.remove("upcoming-status");
                targetStatusCircle.classList.remove("text-info");
                targetStatusCircle.classList.add("text-danger");
                targetStatusText.value = 'Cancelled';
                targetFormAction.value = 'cancel';
                targetCompleteTextDiv.classList.remove("visually-hidden");
                targetCompleteTextDiv.classList.add("visually-hidden");
                targetCancelDiv.classList.remove("visually-hidden");            
            });
        });

        updateInfoInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                const targetId =  input.dataset.idTarget;
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                
                targetFormAction.value = 'update';     
            });
        });

        addDateInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                const targetId =  input.dataset.idTarget;
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                
                targetFormAction.value = 'set-pickup-date';      
            });
        });

        completeBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
            const targetId =  btn.dataset.idTarget;
                const targetCompleteTextDiv = document.getElementById(`complete-text-div-${targetId}`);
                const targetForm = document.getElementById(`form-${targetId}`);
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                const targetStatusCircle = document.getElementById(`status-circle-${targetId}`);
                const targetStatusText = document.getElementById(`status-text-${targetId}`);
                const targetCancelDiv = document.getElementById(`cancel-input-div-${targetId}`);

                targetStatusCircle.classList.remove("upcoming-status");
                targetStatusCircle.classList.remove("text-danger");
                targetStatusCircle.classList.add("text-info");
                targetStatusText.value = 'Completed';
                targetFormAction.value = 'complete';
                targetCompleteTextDiv.classList.remove("visually-hidden");
            });
        });


        deleteBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId =  btn.dataset.idTarget;
                const targetForm = document.getElementById(`form-${targetId}`);
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                
                targetFormAction.value = 'delete';
                targetForm.submit(); 
            });
        });

</script>

</body>
</html>