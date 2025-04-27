<?php 
    session_start();

    include(__DIR__ . "/../../config/utils.php");

    // check session first exists first
    if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
        header("location: ../public/counselor-admin-login-page.php");
        exit();
    }

    // appointment request type and brief information about the concern
    $statuses = ["Pending", "Upcoming", "Declined", "Cancelled", "Completed"];
    $concerns = ["Academic", "Career", "Personal"];
    $firstNames = ["Jazelle", "Carlos", "Maria", "Liam", "Sofia", "Noah", "Isla", "Mateo", "Luna", "Elijah"];
    $lastNames = ["Cruz", "Santos", "Reyes", "Gomez", "Torres", "Garcia", "Rivera", "Morales", "Dela Cruz", "Navarro"];

    $statusClassName = array(
        "Pending" => "text-warning",
        "Upcoming" => "upcoming-status",
        "Declined" => "declined-status",
        "Completed" => "text-info",
        "Cancelled" => "text-danger"
    );

    // test data 
    $appointments = [];

    for ($i = 0; $i < 12; $i++) {
        $first = $firstNames[array_rand($firstNames)];
        $last = $lastNames[array_rand($lastNames)];
        $date = date("Y-m-d", strtotime("+".rand(0, 30)." days")); // random date within the next 30 days
        $startHour = rand(8, 16); // between 8 AM and 4 PM
        $startMin = ["00", "30"][rand(0,1)];
        $startTime = sprintf("%02d:%s", $startHour, $startMin);
        $endTime = sprintf("%02d:%s", $startHour + 1, $startMin); // 1 hour duration
        $status = $statuses[array_rand($statuses)];
        $concern = $concerns[array_rand($concerns)];

        $appointments[] = [
            "firstName" => $first,
            "lastName" => $last,
            "date" => $date,
            "concern" => $concern,
            "startTime" => $startTime,
            "endTime" => $endTime,
            "status" => $status
        ];
    }

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
    <main>
        <div class="appt-header-bar px-4 py-3 d-flex align-items-center mb-4">
            <h5 class="mb-0 fw-bold">Appointments</h5>
        </div>
        <div class="appt-controls-bar d-flex gap-3 align-items-center">
            <div class="d-flex align-items-center gap-2">
                <label for="sttext-secondary atus">Status:</label>
                <select name="status" id="" class="form-select">
                    <option value="" disabled selected>Default</option>
                    <option value="">Pending</option>
                    <option value="">Upcoming</option>
                    <option value="">Completed</option>
                    <option value="">Cancelled</option>
                    <option value="">Declined</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2 ">
                <label for="" class="w-100">Date Range:</label>
                <select name="" id="" class="form-select">
                    <option value="" disabled selected>Default</option>
                    <option value="">Today</option>
                    <option value="">This Week</option>
                    <option value="">This Month</option>
                </select>
            </div>
            <a href="./appts-analytics.php" class="btn btn-warning view-analytics-appt-btn">
                <i class="bi bi-bar-chart-fill"></i>                    
                View Analytics
            </a>
            <div class="ms-auto ">
                <button class="btn btn-primary add-appt-btn">
                    <i class="bi bi-plus-circle"></i>
                    Add New
                </button>
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
                    <?php foreach($appointments as $i=>$a) { ?>
                        <tr>
                            <td class="border-end-0 text-light-emphasis"><?php echo $i + 1?></td>
                            <td class="border-start-0 border-end-0 text-light-emphasis"><?php echo $a['firstName'] . ' ' . $a['lastName']?></td>
                            <td class="border-start-0 border-end-0 text-light-emphasis"><?php echo $a['concern']?></td>
                            <td class="border-start-0 border-end-0 text-light-emphasis">
                                <?php echo $a['status'] == "Pending" || $a['status'] == "Declined" ? '--.--' : date("M j, Y, D", strtotime($a['date']))?>
                            </td>
                            <td class="border-start-0 border-end-0 text-light-emphasis">
                                <?php echo $a['status'] == "Pending" || $a['status'] == "Declined" ? '--.--' : date('h:i A', strtotime($a['startTime'])) . ' - ' . date('h:i A', strtotime($a['endTime'])) ?>
                            </td>
                            <td class="border-start-0 border-end-0 text-light-emphasis">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-circle-fill text-in status-circle <?php echo $statusClassName[$a['status']]?>"></i>
                                    <span><?php echo $a['status']?></span>
                                </div>
                            </td>
                            <td class="border-start-0 text-light-emphasis d-flex align-items-center justify-content-center gap-3">
                                <i style="cursor: pointer;" class="appt-action-btn bi bi-eye-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'appt-details-modal' . $i + 1?>"></i>
                                <i style="cursor: pointer;" class="appt-action-btn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'edit-appt-modal' . $i + 1?>"></i>
                                <i style="cursor: pointer;" class="appt-action-btn bi bi-trash3-fill" data-bs-toggle="modal" data-bs-target="#<?php echo 'delete-appt-modal' . $i + 1?>"></i>
                            </td>
                        </tr>
                        <!--  VIEW MODAL START -->
                        <div class="modal fade" id="<?php echo 'appt-details-modal' . $i + 1?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">  <!-- modal-fullscreen -->
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Appointment Details</h1>
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
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['firstName']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Last Name</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['lastName']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Middle Name</label>
                                                <input type="text" name="" class=" form-control form-control-sm" id="" value=null readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Suffix</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value=null readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Gender</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['firstName']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Birthdate</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['lastName']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Contact No</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['lastName']?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col col-2">
                                                <label class="text-secondary mb-1" for="">Student No.</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['firstName']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Program</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="Bacherlor of Science in Accounting Management" readonly>
                                            </div>
                                            <div class="col col-2">
                                                <label class="text-secondary mb-1" for="">Year Level</label>
                                                <input type="text" name="" class=" form-control form-control-sm" id="" value=null readonly>
                                            </div>
                                            <div class="col col-4">
                                                <label class="text-secondary mb-1" for="">Email</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value=null readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Guardian Name</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['firstName']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Guardian Contact No.</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['lastName']?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <p class="fw-bold">Appointment Details</p>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Appointment Date</label>
                                                <input type="date" name="" class="form-control form-control-sm" id="" value="<?php echo $a['date']?>" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Appointment Time</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Appointment Concern</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="" readonly>
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
                                                <textarea type="text" name="" class="form-control form-control-sm" id="" value="" readonly></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Student's prefered day of appointment</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="" readonly>
                                            </div>
                                            <div class="col">
                                                <label class="text-secondary mb-1" for="">Student's prefered time of appointment</label>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="" readonly>
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
                                    Are you sure you want to delete the appointment?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger">Delete</button>
                                </div>
                                </div>
                            </div>
                        </div>
                        <!--  DELETE MODAL END -->

                        <!--  EDIT MODAL START -->
                        <div class="modal fade" id="<?php echo 'edit-appt-modal' . $i + 1?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Appointment</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="mb-1" for="">Current Status of the Appointment:</label>
                                            <div class="status-input-wrapper">
                                                <i class="bi bi-circle-fill text-in status-circle <?php echo $statusClassName[$a['status']]?>"></i>
                                                <input type="text" name="" class="form-control form-control-sm" id="" value="<?php echo $a['status']?>">
                                            </div>
                                        </div>
                                        <?php if($a['status'] === "Pending") { ?>
                                        <div class="mb-2">
                                            <label class="text-secondary mb-1" for="">Confirm or Decline pending request for appointment?</label>
                                            <div>
                                                <button class="btn btn-success">Confirm</button>
                                                <button class="btn btn-danger">Decline</button>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Upcoming") { ?>
                                            <div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Appointment Date</label>
                                                    <input type="date" name="" class="form-control form-control-sm" id="" value="<?php echo $a['date']?>">
                                                    <label for="" class="fst-italic form-text">Click input to change appointment date.</label>
                                                </div>
                                                <div>
                                                    <label class="mb-1" for="">Appointment Time</label>
                                                    <div class="d-flex gap-3 align-items-center">
                                                        <div>
                                                            <label for="">Start Time</label>
                                                            <input type="time" class="form-control" name="" id="" value="">
                                                        </div>
                                                        <div class="align-self-center pt-4">to</div>
                                                        <div>
                                                            <label for="">End Time</label>
                                                            <input type="time" class="form-control" name="" id="" value="">
                                                        </div>
                                                    </div>
                                                    <label for="" class="fst-italic form-text">Click input to change appointment time.</label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Cancelled") { ?>
                                            <div>
                                                <label class="mb-1" for="">Reason for Cancellation:</label>
                                                <textarea class="form-control mb-3" name="" id="" readonly></textarea>
                                                <label for="" class="fst-italic form-text">*Cancelled appointments cannot be edited further.</label> 
                                            </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Declined") { ?>
                                            <div>
                                                <label class="mb-1" for="">Reason for Declining Appointment:</label>
                                                <textarea class="form-control mb-3" name="" id="" readonly></textarea>
                                                <label for="" class="fst-italic form-text">*Declined appointments cannot be edited further.</label> 
                                            </div>
                                        <?php } ?>
                                        <?php if($a['status'] === "Completed") { ?>
                                            <div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Completed Appointment Date</label>
                                                    <input type="date" name="" class="form-control form-control-sm" id="" value="<?php echo $a['date']?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="mb-1" for="">Completed Appointment Time</label>
                                                    <div class="d-flex gap-3 align-items-center">
                                                        <div>
                                                            <label for="">Start Time</label>
                                                            <input type="time" class="form-control" name="" id="" value="14:00">
                                                        </div>
                                                        <div class="align-self-center pt-4">to</div>
                                                        <div>
                                                            <label for="">End Time</label>
                                                            <input type="time" class="form-control" name="" id="" value="">
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
                                            <button type="button" class="btn btn-success">Save changes</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  EDIT MODAL END -->
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="appt-page-nav-wrapper mt-4">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end">
                    <li class="page-item">
                        <a class="page-link page-nav-link" href="#">Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link text-black" href="#">1</a></li>
                    <li class="page-item">
                        <a class="page-link page-nav-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- CONFIRM APPOINTMENT MODAL  -->
        <!-- CONFIRM APPOINTMENT MODAL  --> 
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const statusClassNames = {
            "Decline" : "Declined"
            "Pending" : "text-warning",
            "Upcoming" : "upcoming-status",
            "Decline" : "declined-status",
            "Completed" : "text-info",
            "Cancelled" : "text-danger"
        }

        
    </script>
</body>
</html>