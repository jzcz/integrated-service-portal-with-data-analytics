<?php 
    session_start();

        // check session first exists first
    // if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
    //     header("location: ../public/counselor-admin-login-page.php");
    //     exit();
    // }

    require(__DIR__ . "/../../queries/appointments.php");
    include(__DIR__ . "/../../config/utils.php");
    require(__DIR__ . "/../../services/email-service.php");
    require_once(__DIR__ . "/../../config/config.php");
    
    function formatRedirectUrl($url, $key, $value) {
        $params = [ $key => $value ];
        
        if (parse_url($url, PHP_URL_QUERY)) {
            $newUrl = $url . '&' . http_build_query($params);
        } else { 
            $newUrl = $url . '?' . http_build_query($params);
        }

        return $newUrl;
    }

    $mailer = createMailerService();

    $db_conn = require(__DIR__ . "/../../db/db_conn.php");

    $err = null;
    $success = null;
    
    $page = 1;
    $statusOption = null;
    $dateRange = null;

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
            $err = $_GET["err"];
        }

        if(isset($_GET["success"])) {
            $success = $_GET["success"];
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['form-action']) && $_POST['form-action'] == 'confirm') {
            $apptId = $_POST['appt_id'];
            $apptDate = $_POST['appt_date'];
            $startTime = $_POST['appt_start_time'];
            $endTime = $_POST['appt_end_time'];
            $studentEmail = $_POST['studentEmail'];
            
            $res = confirmAppointment($db_conn, $apptId, $apptDate, $startTime, $endTime);
            
            if($res) {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success',
                "Successfully confirmed and updated appointment! An email notification will be sent to " . $res['first_name'] . ' ' . $res['last_name'] . " for appointment confirmation.")
            );
            
            $mailer->clearAddresses();
            $mailer->clearAttachments();
            $mailer->clearAllRecipients();
            $mailer->addAddress($studentEmail, $res['first_name'] . ' ' . $res['last_name']);
            $mailer->Subject = "Appointment Confirmation";
            $mailer->Body = "
                Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
                Your requested appointment has been approved and confirmed by the QCU Guidance and Counseling Office! Below are the details of your confirmed appointment:<br><br>
                <b>Appointment ID:</b> " . $res['appt_id'] . "<br>
                <b>Appointment Status:</b> " . $res['status'] . "<br>
                <b>Appointment Date:</b> " . date("M j, Y, D", strtotime($res['appt_date'])) . "<br>
                <b>Appointment Time:</b> " . date('h:i A', strtotime($res['appt_start_time'])) . ' to ' . date('h:i A', strtotime($res['appt_end_time'])) . "<br>
                <b>Appointment Concern:</b> " . $res['counseling_concern'] . "<br><br>
                <i><b>Note:</b> You are advised to arrive 5 to 10 minutes early for your appointment to avoid any unexpected delays or inconveniences.</i><br><br>
                Thank you for your patience. You will be notified via email if there are any changes to your appointment. <br><br>
                <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
                Best regards,<br>
                QCU Guidance and Counseling Office<br>
            ";

            $mailer->send();
            } else {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to confirm and update appointment details. Please try again later."));
            }
        } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'decline') {
            $apptId = $_POST['appt_id'];
            $declineReason = $_POST['decline-input'];
            $studentEmail = $_POST['studentEmail'];

            $res = declineAppointment($db_conn, $apptId, $declineReason);

            if($res) {
            header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully declined the appointment! An email notification will be sent to " . $res['first_name'] . ' ' . $res['last_name'] . " for appointment decline."));
                $mailer->clearAddresses();
                $mailer->clearAttachments();
                $mailer->clearAllRecipients();
                $mailer->addAddress($studentEmail, $res['first_name'] . ' ' . $res['last_name']);
                $mailer->Subject = "Appointment Declined";
                $mailer->Body = "
                    Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
                    We regret to inform you that your appointment request submitted on " . date("M j, Y, D", strtotime($res['created_at'])) . " was declined by the QCU Guidance and Counseling Office. Below are the details of your declined appointment:<br><br>
                    <b>Appointment ID:</b> " . $res['appt_id'] . "<br>
                    <b>Appointment Concern:</b> " . $res['counseling_concern'] . "<br>
                    <b>Appointment Status:</b> " . $res['status'] . "<br>
                    <b>Decline Reason:</b> " . $res['decline_reason'] . "<br><br>
                    If you have any questions or would like to request another appointment, please feel free to submit a new one through the QCU Guidance and Counseling service portal at your convenience.<br><br>
                    <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
                    Best regards,<br>
                    QCU Guidance and Counseling Office<br>
                ";
                
            $mailer->send();
            } else {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to decline appointment. Please try again later."));
            }
        } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'cancel') {
            $apptId = $_POST['appt_id'];
            $cancelReason = $_POST['cancel-reason-input'];
            $studentEmail = $_POST['studentEmail'];

            $res = cancelAppointment($db_conn, $apptId, $cancelReason);

            if($res) {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully cancelled appointment! An email notification will be sent to appointee for appointment cancellation."));
                $mailer->clearAddresses();
                $mailer->clearAttachments();
                $mailer->clearAllRecipients();
                $mailer->addAddress($studentEmail, $res['first_name'] . ' ' . $res['last_name']);
                $mailer->Subject = "Appointment Cancelled";
                $mailer->Body = "
                    Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
                    We regret to inform you that your appointment scheduled on " . date("M j, Y, D", strtotime($res['appt_date'])) . " has been cancelled by the QCU Guidance and Counseling Office. Below are the details of your cancelled appointment:<br><br>
                    <b>Appointment ID:</b> " . $res['appt_id'] . "<br>
                    <b>Appointment Status:</b> " . $res['status'] . "<br>
                    <b>Cancellation Reason:</b> " . $res['cancellation_reason'] . "<br><br>
                    If you have any questions or would like to request another appointment, please feel free to submit a new one through the QCU Guidance and Counseling service portal at your convenience.<br><br>
                    <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
                    Best regards,<br>
                    QCU Guidance and Counseling Office<br>
                    ";
                    
            $mailer->send();
            } else {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to cancel appointment. Please try again later."));
            }
        } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'update') {
            $apptId = $_POST['appt_id'];
            $apptDate = $_POST['appt_date'];
            $apptStartTime = $_POST['appt_start_time'];
            $apptEndTime = $_POST['appt_end_time'];
            $studentEmail = $_POST['studentEmail'];

            $res = updateAppointment($db_conn, $apptId, $apptDate, $apptStartTime, $apptEndTime, $studentEmail);
        
            if($res) {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully updated appointment! An email notification will be sent to " . $res['first_name'] . ' ' . $res['last_name'] . " for appointment updates."));
                $mailer->clearAddresses();
                $mailer->clearAttachments();
                $mailer->clearAllRecipients();
                $mailer->addAddress($studentEmail, $res['first_name'] . ' ' . $res['last_name']);
                $mailer->Subject = "Updated Appointment Details";
                $mailer->Body = "
                Hello, " . $res['first_name'] . " " . $res['last_name'] . "!<br><br>
                We would like to inform you that your appointment request submitted on ". date("M j, Y, D", strtotime($res['created_at'])). " has been updated. Below are the new details of your updated appointment:<br><br>
                <b>Appointment ID:</b> " . $res['appt_id'] . "<br>
                <b>Appointment Status:</b> " . $res['status'] . "<br>
                <b>Appointment Date:</b> " . date("M j, Y, D", strtotime($res['appt_date'])) . "<br>
                <b>Appointment Time:</b> " . date('h:i A', strtotime($res['appt_start_time'])) . ' to ' . date('h:i A', strtotime($res['appt_end_time'])) . "<br>
                <b>Appointment Concern:</b> " . $res['counseling_concern'] . "<br>
                <i><b>Note:</b> You are advised to arrive 5 to 10 minutes early for your appointment to avoid any unexpected delays or inconveniences.</i><br><br>
                Thank you for your patience. You will be notified via email if there are any changes to your appointment. <br><br>
                <i>For any questions or concerns, please feel free to reach out to us at " . EMAIL_SERVICE_SENDER . ".</i><br><br>
                Best regards,<br>
                QCU Guidance and Counseling Office<br>
                ";
                
            $mailer->send();
            } else {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to update appointment details. Please try again later."));
            }

        } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'complete') {
            $apptId = $_POST['appt_id'];
            $res = completeAppointment($db_conn, $apptId);

            if($res) {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully completed appointment!"));
            } else {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to complete appointment. Please try again later."));
            }
        } else if(isset($_POST['form-action']) && $_POST['form-action'] == 'delete') {
            $apptId = $_POST['appt_id'];    
            $res = deleteAppointment($db_conn, $apptId);

            if($res) {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'success', "Successfully deleted appointment!"));
            } else {
                header("Location: " . formatRedirectUrl($_SERVER['REQUEST_URI'], 'err', "Failed to delete appointment. Please try again later."));
            }
        }
    }

    $appts = getAllAppointments($db_conn, $page, $statusOption, $dateRange); // retrieve all appointments asc id

    $statuses = ["Pending", "Upcoming", "Declined", "Cancelled", "Completed"];

    $statusClassName = array(
        "Pending" => "text-warning",
        "Upcoming" => "upcoming-status",
        "Declined" => "declined-status",
        "Completed" => "text-info",
        "Cancelled" => "text-danger"
    );

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

        .declined-status {
            color:rgb(255, 133, 67);
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
          <?php echo $err ? "bg-warning"  : "bg-success" ?>
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
            <h5 class="mb-0 fw-b">Appointments</h5>
        </div>

        <div class="appt-controls-bar d-flex gap-3 align-items-center">
            <form action="" name="filter_form" class="d-flex gap-3 align-items-center" method="get">
            <input type="hidden" name="filter_form" value="1">
                <div class="d-flex align-items-center gap-2">
                    <label for="status">Status:</label>
                    <select name="status" id="" class="form-select" >
                        <option value="" <?php echo $statusOption == null ? 'selected' : ''; ?>>Default</option>
                        <option value="Pending" <?php echo ($statusOption == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Upcoming" <?php echo ($statusOption == 'Upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="Completed" <?php echo ($statusOption == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo ($statusOption == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
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
                        <option value="this year" <?php echo ($dateRange == 'this year') ? 'selected' : ''; ?>>This Year</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-md" style="background-color: var(--primary-color); color: white;">Filter</button>
            </form>

            <a href="./appts-analytics.php" class="btn btn-warning view-analytics-appt-btn">
                <i class="bi bi-bar-chart-fill"></i>                    
                View Analytics
            </a>
            <div class="ms-auto ">
                <a href="appt-form.php">
                    <button class="btn btn-primary add-appt-btn">
                        <i class="bi bi-plus-circle"></i>
                        Add New
                    </button>
                </a>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="table-bordered rounded table text-center">
                <thead class="">
                    <tr class="">
                        <th scope="col mb-2" class="border-end-0">#</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Student Name</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Concern</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Date</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Time</th>
                        <th scope="col mb-2" class="border-start-0 border-end-0">Status</th>
                        <th scope="col mb-2" class="border-start-0">Actions</th>
                    </tr>
                </thead>
                <tbody class="appt-tbl">
                    <?php foreach($appts as $i => $a) { ?>
                        <tr>
                            <td class="border-end-0 text-light-emphasis"><?php echo $a['appt_id']?></td>
                            <td class="border-start-0 border-end-0 text-light-emphasis"><?php echo $a['first_name'] . ' ' . $a['last_name']?></td>
                            <td class="border-start-0 border-end-0 text-light-emphasis"><?php echo $a['counseling_concern']?></td>
                            <td class="border-start-0 border-end-0 text-light-emphasis">
                                <?php echo $a['status'] == "Pending" || $a['status'] == "Declined" ? '--.--' : date("M j, Y, D", strtotime($a['appt_date']))?>
                            </td>
                            <td class="border-start-0 border-end-0 text-light-emphasis">
                                <?php echo $a['status'] == "Pending" || $a['status'] == "Declined" ? '--.--' : date('h:i A', strtotime($a['appt_start_time'])) . ' - ' . date('h:i A', strtotime($a['appt_end_time'])) ?>
                            </td>
                            <td class="border-start-0 border-end-0 text-light-emphasis">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-circle-fill status-circle <?php echo $statusClassName[$a['status']]?>"></i>
                                    <span><?php echo $a['status']?></span>
                                </div>
                            </td>
                            <td class="border-start-0 text-light-emphasis d-flex align-items-center justify-content-center gap-3">
                                <i style="cursor: pointer;" class="appt-action-btn bi bi-eye-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'appt-details-modal' . $a['appt_id']?>"></i>
                                <i style="cursor: pointer;" class="appt-action-btn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'edit-appt-modal' . $a['appt_id']?>"></i>
                                <i style="cursor: pointer;" class="appt-action-btn bi bi-trash3-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'delete-appt-modal' . $a['appt_id']?>"></i>
                            </td>
                        </tr>

                        <!-- VIEW MODAL START -->
                        <div class="modal fade" id="<?php echo 'appt-details-modal' . $a['appt_id']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">  <!-- modal-fullscreen -->
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Appointment Details</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="px-1 gap-2">
                                        <div class="row">
                                            <p class="fw-b">Student Details</p>
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
                                                <input type="text" name="" class=" form-control form-control-sm" id="" value="<?php echo ($a['middle_name']) ? $a['middle_name']: "N/A"?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Suffix</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value=<?php echo ($a['suffix']) ? $a['suffix']: "N/A"?>  readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Gender</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['gender']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Contact No</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['personal_contact_no']?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col col-2">
                                                <label class="text-secondary mb-1" for="">Student No.</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['student_no']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Program</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['program_name']?>" readonly>
                                            </div>
                                            <div class="col col-2">
                                                <label class="text-secondary mb-1" for="">Year Level</label>
                                                <input type="text" name="" class=" form-control form-control-sm" id="" value="<?php echo $a['current_year_level']?>" readonly>
                                            </div>
                                            <div class="col col-4">
                                                <label class="text-secondary mb-1" for="">Email</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['student_email']?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Guardian Name</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['guardian_name']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Guardian Contact No.</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['guardian_contact_no']?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <p class="fw-b">Appointment Details</p>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Appointment Date</label>
                                                <input type="text" name="" class="form-control form-control-sm" value="<?php echo $a['appt_date'] ? date("M j, Y, D", strtotime($a['appt_date'])) : '--.--' ?>"
                                                readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Appointment Time</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['appt_start_time'] ? date('h:i A', strtotime($a['appt_start_time'])) . ' to ' . date('h:i A', strtotime($a['appt_end_time'])) : '--.--' ?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Appointment Concern</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['counseling_concern'] ?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Appointment Status</label>
                                                <div class="status-input-wrapper">
                                                    <i class="bi bi-circle-fill text-in status-circle <?php echo $statusClassName[$a['status']]?>"></i>
                                                    <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['status']?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Brief information about the concern</label>
                                                <textarea type="text" name="" class="form-control form-control-sm" id="" readonly><?php echo $a['add_concern_info'] ? $a['add_concern_info'] : 'None' ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Student's prefered day of appointment</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['preferred_day']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Student's prefered time of appointment</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['preferred_time']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Date the request was submitted:</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo date('M j, Y, D', strtotime($a['created_at']))?>" readonly>
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
                        <!-- VIEW MODAL END -->

                        <!--  DELETE MODAL START -->
                        <div class="modal fade" id="<?php echo 'delete-appt-modal' . $a['appt_id']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-b" id="exampleModalLabel">Confirm Deletion</h5>
                                </div>
                                <div class="modal-body">
                                Are you sure you want to delete this appointment? Deleting it will affect historical records and data accuracy. It's recommended to cancel the appointment instead.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" data-id-target="<?php echo $i; ?>" class="btn btn-danger delete-btn">Delete</button>
                                </div>
                                </div>
                            </div>
                        </div>
                        <!--  DELETE MODAL END -->

                        <!--  EDIT MODAL START -->
                        <div class="modal fade" id="<?php echo 'edit-appt-modal' . $a['appt_id']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <form action="" method="POST" id="form-<?php echo $i ?>">
                            <input type="text" name="appt_id" id="" value="<?php echo $a['appt_id']?>" hidden>
                            <input type="text" name="form-action" id="form-action-<?php echo $i ?>" value="" hidden>
                            <input type="text" name="studentEmail" value="<?php echo $a['student_email']?>" hidden>
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="">Edit Appointment</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="mb-1" for="">Current Status of the Appointment:</label>
                                            <div class="status-input-wrapper">
                                                <i class="bi bi-circle-fill text-in status-circle <?php echo $statusClassName[$a['status']]?>" id="status-circle-<?php echo $i ?>"></i>
                                                <input type="text" name="" class="form-control form-control-sm" id="status-text-<?php echo $i ?>" value="<?php echo $a['status']?>" readonly>
                                            </div>
                                        </div>
                                        <?php if($a['status'] === "Pending") { ?>
                                        <div class="mb-2">
                                            <label class="text-secondary mb-1" for="">Confirm or Decline pending request for appointment?</label>
                                            <div>
                                                <button class="btn btn-success confirm-btn" data-id-target="<?php echo $i ?>" type="button">Confirm</button>
                                                <button class="btn btn-danger decline-btn" data-id-target="<?php echo $i ?>" id="" type="button">Decline</button>
                                            </div>
                                        </div>
                                        <div id="add-actions-div-confirm-decline">
                                            <div id="decline-input-div-<?php echo $i ?>" class="visually-hidden">
                                                <label class="d-inline-block" for="">State your reason for declining:</label>
                                                <textarea name="decline-input" class="form-control mb-3" style="font-size: 14px" id="" value=""></textarea>
                                            </div>
                                            <div id="confirm-input-div-<?php echo $i ?>" class="visually-hidden">
                                            <div class="mb-3">
                                                    <label class="mb-1" for="">Set an appointment date:</label>
                                                    <input type="date" name="appt_date" class="form-control form-control-sm" id="" value="">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Set an appointment time:</label>
                                                    <div class="d-flex gap-3 align-items-center">
                                                        <div>
                                                            <label for="">Start Time</label>
                                                            <input type="time" class="form-control" name="appt_start_time" id="" value="">
                                                        </div>
                                                        <div class="align-self-center pt-4">to</div>
                                                        <div>
                                                            <label for="">End Time</label>
                                                            <input type="time" class="form-control" name="appt_end_time" id="" value="">
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Upcoming") { ?>
                                            <div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Appointment Date</label>
                                                    <input type="date" class="form-control form-control-sm update-info-input" name="appt_date"  data-id-target="<?php echo $i ?>" value="<?php echo $a['appt_date']?>">
                                                    <label for="" class="fst-italic form-text">Click input to change appointment date.</label>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Appointment Time</label>
                                                    <div class="d-flex gap-3 align-items-center">
                                                        <div>
                                                            <label for="">Start Time</label>
                                                            <input type="time" class="form-control update-info-input" name="appt_start_time" data-id-target="<?php echo $i ?>" value="<?php echo $a['appt_start_time']; ?>">
                                                        </div>
                                                        <div class="align-self-center pt-4">to</div>
                                                        <div>
                                                            <label for="">End Time</label>
                                                            <input type="time" class="form-control update-info-input" name="appt_end_time" data-id-target="<?php echo $i ?>" value="<?php echo $a['appt_end_time']; ?>">
                                                        </div>
                                                    </div>
                                                    <label for="" class="fst-italic form-text">Click input to change appointment time.</label>
                                                </div>
                                                <div class="d-flex flex-column gap-2 align-items-start justify-content-start mb-2">
                                                    <label for="" class="">Would you like to cancel or complete appointment?</label>
                                                    <div>
                                                        <button class="btn btn-sm btn-danger px-2 cancel-btn" data-id-target="<?php echo $i ?>" id="cancel-btn-<?php echo $i ?>" type="button">Cancel</button>
                                                        <button class="btn btn-sm btn-info px-2 complete-btn" data-id-target="<?php echo $i ?>" id="complete-btn-<?php echo $i ?>" type="button">Complete</button>
                                                    </div>
                                                </div>
                                                <div id="cancel-input-div-<?php echo $i ?>" class="visually-hidden">
                                                    <label class="" for="">State your reason for cancelling:</label>
                                                    <textarea name="cancel-reason-input" id="cancel-input-<?php echo $i; ?>" class="form-control mb-3" style="font-size: 14px"></textarea>
                                                </div>
                                                <div id="complete-text-div-<?php echo $i ?>" class="visually-hidden">
                                                    <label class="" for="">The appointment status will be updated to completed.</label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Cancelled") { ?>
                                            <div>
                                                <label class="mb-1" for="">Reason for Cancellation:</label>
                                                <textarea class="form-control mb-3" style="font-size: 14px" name="" id="" value="<?php echo $a['cancellation_reason']; ?>" readonly><?php echo $a['cancellation_reason']; ?></textarea>
                                                <label for="" class="fst-italic form-text">*Cancelled appointments cannot be edited further.</label> 
                                            </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Declined") { ?>
                                            <div>
                                                <label class="mb-1" for="">Reason for Declining Appointment:</label>
                                                <textarea class="form-control mb-3" name="" value="<?php echo $a['decline_reason']; ?>" id="" style="font-size: 14px" readonly><?php echo $a['decline_reason']; ?></textarea>
                                                <label for="" class="fst-italic form-text">*Declined appointments cannot be edited further.</label> 
                                            </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Completed") { ?>
                                            <div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Completed Appointment Date</label>
                                                    <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo date('M j, Y, D', strtotime($a['appt_date']))?>" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Completed Appointment Time</label>
                                                    <div class="d-flex gap-3 align-items-center">
                                                        <div>
                                                            <label for="">Start Time</label>
                                                            <input type="time" class="form-control" name="" id="" value="<?php echo $a['appt_start_time']; ?>" readonly>
                                                        </div>
                                                        <div class="align-self-center pt-4">to</div>
                                                        <div>
                                                            <label for="">End Time</label>
                                                            <input type="time" class="form-control" name="" id="" value="<?php echo $a['appt_end_time']; ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label for="" class="fst-italic form-text">*Completed appointments cannot be edited further.</label>
                                            </div>
                                        <?php } ?>
                                        <div class="extra-actions-div">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <?php if($a['status'] === "Pending" || $a['status'] === "Upcoming") { ?>
                                            <button type="submit" id="save-changes-btn-<?php echo $i ?>" data-id-target="<?php echo $i; ?>" class="btn btn-success save-changes-btn">Save changes</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
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
                            <button type="submit" name="page" value="<?php echo $page - 1; ?>" class="page-link page-nav-link <?php echo ($page == 1) ? 'disabled' : ''; ?>" <?php echo ($page == 1) ? 'disabled' : ''; ?>>Previous</button>
                        </li>
                        <li class="page-item"><button class="page-link text-black"><?php echo $page ?></button></li>
                        <li class="page-item">
                            <button type="submit" name="page" value=<?php echo $page + 1?> class="page-link page-nav-link">Next</button>
                        </li>
                    </ul>
                </nav>
            </form>
        </div>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const messageModal = new bootstrap.Modal(document.getElementById("message-modal"));
        const declineBtns = document.querySelectorAll('.decline-btn');
        const confirmBtns = document.querySelectorAll('.confirm-btn');
        const saveBtns = document.querySelectorAll('.save-changes-btn');
        const updateInfoInputs = document.querySelectorAll('.update-info-input');
        const cancelBtns = document.querySelectorAll('.cancel-btn');
        const completeBtns = document.querySelectorAll('.complete-btn');
        const cancelInputDiv = document.getElementById('cancel-input-div');
        const saveChangesBtn = document.getElementById('save-changes-btn');
        const deleteBtns = document.querySelectorAll('.delete-btn');

        <?php if($err || $success) { echo "messageModal.show();"; }  ?>

        declineBtns.forEach(function(btn) {
           btn.addEventListener('click', function() {
                const targetId =  btn.dataset.idTarget;
                const targetDeclineDiv = document.getElementById(`decline-input-div-${targetId}`);
                const targetConfirmDiv = document.getElementById(`confirm-input-div-${targetId}`);
                const targetForm = document.getElementById(`form-${targetId}`);
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                const targetStatusCircle = document.getElementById(`status-circle-${targetId}`);
                const targetStatusText = document.getElementById(`status-text-${targetId}`);

                targetStatusCircle.classList.remove("text-warning");
                targetStatusCircle.classList.remove("upcoming-status");
                targetStatusCircle.classList.add("declined-status");
                targetStatusText.value = 'Declined';
                
                targetFormAction.value = 'decline';
                targetConfirmDiv.classList.remove("visually-hidden");
                targetConfirmDiv.classList.add("visually-hidden");

                targetDeclineDiv.classList.remove("visually-hidden");            
            });
        });

        confirmBtns.forEach(function(btn) {
           btn.addEventListener('click', function() {
            const targetId =  btn.dataset.idTarget;
                const targetDeclineDiv = document.getElementById(`decline-input-div-${targetId}`);
                const targetConfirmDiv = document.getElementById(`confirm-input-div-${targetId}`);
                const targetForm = document.getElementById(`form-${targetId}`);
                const targetFormAction = document.getElementById(`form-action-${targetId}`);
                const targetStatusCircle = document.getElementById(`status-circle-${targetId}`);
                const targetStatusText = document.getElementById(`status-text-${targetId}`);

                targetStatusCircle.classList.remove("text-warning");
                targetStatusCircle.classList.remove("declined-status");
                targetStatusCircle.classList.add("upcoming-status");
                targetStatusText.value = 'Upcoming';
                
                targetFormAction.value = 'confirm';
                targetDeclineDiv.classList.remove("visually-hidden");
                targetDeclineDiv.classList.add("visually-hidden");

                targetConfirmDiv.classList.remove("visually-hidden");            
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
                cancelInputDiv.classList.remove("visually-hidden");
                cancelInputDiv.classList.add("visually-hidden");        
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
                targetCancelDiv.classList.remove("visually-hidden");
                targetCancelDiv.classList.add("visually-hidden");
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