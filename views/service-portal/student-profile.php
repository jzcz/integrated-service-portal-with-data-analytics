<?php
session_start();

require(__DIR__ . "/../../queries/students.php");
include(__DIR__ . "/../../config/utils.php");
$db_conn = require(__DIR__."/../../db/db_conn.php");

// Check if the student is logged in
if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
    // Redirect to login if not logged in
    header("location: ../service-portal/login.php");
    exit();
}

$studentId = $_SESSION['studentId'];

// Fetch the student's data using the ID
$studentData = getStudentByStudId($db_conn, $studentId);

// Check if student data was found
if (!$studentData) {
    // Handle the case where student data couldn't be retrieved
    echo "Error: Could not retrieve student information.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/service-portal.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: auto !important;
            background-color: white;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <?php
        include(__DIR__ . '/../components/service-portal/navbar.php');
    ?>
    <main>
    <div class="container mt-4 d-flex flex-column align-items-center">
        <h2 class="text-primary-emphasis text-center pt-5 mb-3 student-profile-header"><b>Student Profile</b></h2>
        <div class="text-center mb-3">
        <img src="../../static/profile1.png" alt="Profile Icon" width="150" height="150">
        <h3 class="pt-3"><?php echo htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']); ?></h3>
        <p class="text-muted"><?php
            // Fetch program name based on program_id
            $programId = $studentData['program_id'] ?? null;
            if ($programId) {
                // Assuming you have a function to get program name by ID in utils.php
                echo htmlspecialchars(getProgramNameById($db_conn, $programId) ?? '');
            } else {
                echo "";
            }
        ?></p>
        </div>
        <div class="card bg-white student-details-card" style="max-width: 600px; min-height: 200px; border-radius: 0; margin-bottom: 16rem;">
        <div class="card-body py-2 px-3">
        <form>
            <div class="row gx-1 align-items-center">
            <label for="studentNumber" class="col-md-5 col-form-label fw-bold smaller">Student Number</label>
            <div class="col-md-7 text-end">
                <input type="text" class="form-control-plaintext smaller text-end" id="studentNumber" value="<?php echo htmlspecialchars($studentData['student_no'] ?? ''); ?>" readonly>
            </div>
            </div>
            <div class="row gx-1 align-items-center">
            <label for="qcuEmail" class="col-md-5 col-form-label fw-bold smaller">QCU Email</label>
            <div class="col-md-7 text-end">
            <input type="email" class="form-control-plaintext smaller text-end" id="qcuEmail" value="<?php echo htmlspecialchars($studentData['email'] ?? ''); ?>" readonly>
            </div>
            </div>
            <div class="row gx-1 align-items-center">
            <label for="birthdate" class="col-md-5 col-form-label fw-bold smaller">Birthdate</label>
            <div class="col-md-7 text-end">
                <input type="text" class="form-control-plaintext smaller text-end" id="birthdate" value="<?php echo htmlspecialchars($studentData['birthdate'] ?? ''); ?>" readonly>
            </div>
            </div>
            <div class="row gx-1 align-items-center">
            <label for="gender" class="col-md-5 col-form-label fw-bold smaller">Gender</label>
            <div class="col-md-7 text-end">
                <input type="text" class="form-control-plaintext smaller text-end" id="gender" value="<?php echo htmlspecialchars($studentData['gender'] ?? ''); ?>" readonly>
            </div>
            </div>
            <div class="row gx-1 align-items-center">
            <label for="campus" class="col-md-5 col-form-label fw-bold smaller">Campus</label>
            <div class="col-md-7 text-end">
                <input type="text" class="form-control-plaintext smaller text-end" id="campus" value="<?php echo htmlspecialchars($studentData['campus'] ?? ''); ?>" readonly>
            </div>
            </div>
            <div class="row gx-1 align-items-center">
            <label for="yearLevel" class="col-md-5 col-form-label fw-bold smaller">Current Year Level</label>
            <div class="col-md-7 text-end">
                <input type="text" class="form-control-plaintext smaller text-end" id="yearLevel" value="<?php echo htmlspecialchars($studentData['current_year_level'] ?? ''); ?>" readonly>
            </div>
            </div>
            <div class="vertical-line">
        </div>
        </div>
        </div>

   <style>
     .vertical-line {
       position: absolute;
       top: 0;
       bottom: 0;
       left: 50%; 
       width: 2px;
       background-color: lightgray;
       transform: translateX(-50%);
     }
   </style>
       </form>
       </div>  
      </div>

  
  <footer>
    <div class="form-title custom-footer-color text-white pb-3 pt-3">
      <p class="text-center mb-0">© 2024 QCU Guidance and Counseling. All rights reserved.</p>
    </div>
  </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html> 