<?php
session_start();

include(__DIR__ . "/../../config/utils.php");

if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
  header("location: ../service-portal/login.php");
  exit();
}
class Database {
    private $conn;

    public function __construct() {
        $this->conn = require __DIR__ . "/../../db/db_conn.php";
    }

    public function getPrograms() {
        $result = $this->conn->query("SELECT program_id, program_name FROM programs");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function saveAppointment($attendeeData, $appointmentData) {
       $this->conn->begin_transaction();      
        try {
            $stmt1 = $this->conn->prepare("INSERT INTO appt_attendee 
                (first_name, last_name, middle_name, suffix, student_no, program_id, current_year_level, gender, personal_contact_no, student_email, guardian_name, guardian_contact_no) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt1->bind_param("ssssssisssss", 
                $attendeeData['firstName'], $attendeeData['lastName'], $attendeeData['middleName'], $attendeeData['suffix'], 
                $attendeeData['student_no'], $attendeeData['program_id'], $attendeeData['yearLevel'], $attendeeData['gender'], 
                $attendeeData['personal_contact_no'], $attendeeData['student_email'], $attendeeData['guardianName'], $attendeeData['guardianContact']);
            $stmt1->execute();
            $attendeeId = $stmt1->insert_id;
            $stmt1->close();

            $status = 'Upcoming';
            $stmt2 = $this->conn->prepare("INSERT INTO appointments 
                (attendee_id, counseling_concern, appt_start_time, appt_end_time, appt_date, status) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("isssss", 
                $attendeeId, $appointmentData['counselConcern'], $appointmentData['startTime'], $appointmentData['endTime'], $appointmentData['counselingDate'], $status);
            $stmt2->execute();
            $stmt2->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
           $this->conn->rollback();
            echo $e->getMessage(); exit();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();

    $attendeeData = [
        'firstName' => trim($_POST["firstName"]),
        'lastName' => trim($_POST["lastName"]),
        'middleName' => trim($_POST["middleName"]),
        'suffix' => $_POST["suffix"] ?? "",
        'student_no' => trim($_POST["student_no"]),
        'program_id' => $_POST["program_id"],
        'yearLevel' => $_POST["year-level"],
        'gender' => $_POST["gender"],
        'personal_contact_no' => trim($_POST["personal_contact_no"]),
        'student_email' => trim($_POST["student_email"]),
        'guardianName' => trim($_POST["guardianName"]),
        'guardianContact' => trim($_POST["guardianContact"]),
    ];

    $appointmentData = [
        'counselConcern' => $_POST["counselConcern"],
        'startTime' => $_POST["startTime"],
        'endTime' => $_POST["endTime"],
        'counselingDate' => $_POST["counselingDate"]
    ];

    $result = $db->saveAppointment($attendeeData, $appointmentData);

    if ($result === true) {
        echo "<script>alert('Appointment saved successfully!'); window.location.href='appointments.php';</script>";
    } else {
        echo "<script>alert('Error: $result'); window.history.back();</script>";
    }
}

$db = new Database();
$programs = $db->getPrograms();
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
        .appt-header-bar {
            width: 100%;
            background-color:rgb(222, 237, 251);
            border-bottom: 2px #9DCEFF solid;
        }
        .appt-header-bar h5{
            color: var(--primary-color);
        }

        .btn-primary {
          background-color: #004085;
          color: white;
          border: none;
          width: 200px;
        }

        .btn-primary:hover {
          background-color: #002752;
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

        <div class="px-4">

  <form action="" method="POST">
    <h6 class="fw-bold mb-3">Appointment's Detail</h6>

    <!-- First Row -->
    <div class="row mb-3">
      <div class="col">
        <label for="firstName" class="form-label fw-bold">First Name</label>
        <input type="text" id="firstName" name="firstName" class="form-control" value="" required/>
      </div>

      <div class="col">
        <label for="lastName" class="form-label fw-bold">Last Name</label>
        <input type="text" id="lastName" name="lastName" class="form-control" value="" required/>
      </div>

      <div class="col">
        <label for="middleName" class="form-label fw-bold">Middle Name</label>
        <input type="text" id="middleName" name="middleName" class="form-control" value="N/A" required/>
      </div>

      <div class="col">
        <label for="suffix" class="form-label fw-bold">Suffix</label>
        <select name="suffix" id="suffix" class="form-select">
          <option value="">Not Applicable</option>
          <option value="Sr">Sr</option>
          <option value="Jr">Jr</option>
          <option value="III">III</option>
          <option value="IV">IV</option>
        </select>
      </div>

      <div class="col">
        <label for="student_no" class="form-label fw-bold">Student Number</label>
        <input type="text" id="student_no" name="student_no" class="form-control" value="00-0000" pattern="^\d{2}-\d{4}$" title="Format: 00-0000" required/>
      </div>
    </div>

    <!-- Second Row -->

    <div class="row mb-3">
  <div class="col">
    <label for="gender" class="form-label fw-bold">Gender</label>
    <select name="gender" id="gender" class="form-select" required>
      <option value="" disabled selected>Select gender...</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      <option value="Other">Other</option>
    </select>
  </div>

  <div class="col">
      <label class="form-label">Program</label>
      <select name="program_id" class="form-select" required>
        <option value="">Select Program</option>
        <?php foreach ($programs as $program): ?>
          <option value="<?= $program['program_id'] ?>"><?= htmlspecialchars($program['program_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

      <div class="col">
        <label for="year-level" class="form-label fw-bold">Year Level</label>
        <select name="year-level" id="year-level" class="form-select" required>
        <option value="" disabled selected>Select your year level...</option>
          <option value="1st">1st Year</option>
          <option value="2nd">2nd Year</option>
          <option value="3rd">3rd Year</option>
          <option value="4th">4th Year</option>
        </select>
      </div>

      <div class="col">
        <label for="contactNum" class="form-label fw-bold">Personal Contact Number</label>
        <input type="tel" id="personal_contact_no" name="personal_contact_no" class="form-control" value="" required/>
      </div>
    </div>

    <!-- Email -->
    <div class="mb-3">
    <label for="student_email" class="form-label fw-bold">Student Email</label>
    <input type="email" id="student_email" name="student_email" class="form-control" value="" required/>
    </div>

    <!-- Guardian Info -->
    <div class="row mb-3">
      <div class="col">
        <label for="guardianName" class="form-label fw-bold">Guardian's Name</label>
        <input type="text" id="guardianName" name="guardianName" class="form-control" value="" required/>
      </div>

      <div class="col">
        <label for="guardianContact" class="form-label fw-bold">Guardian's Contact Number</label>
        <input type="tel" id="guardianContact" name="guardianContact" class="form-control" value="" required/>
      </div>
    </div>

    <!-- Counseling Schedule -->
    <div class="row mb-3">
  <div class="col">
    <label for="counselConcern" class="form-label fw-bold">Counseling Concern</label>
    <select name="counselConcern" id="counselConcern" class="form-select" required>
    <option value="" disabled selected>Select your concern...</option>
      <option value="Academic">Academic</option>
      <option value="Career">Career</option>
      <option value="Personal">Personal</option>
    </select>
  </div>

  <div class="col">
    <label for="startTime" class="form-label fw-bold">Start Time</label>
    <input type="time" id="startTime" name="startTime" class="form-control" value="9:00 AM" required/>
  </div>

  <div class="col">
    <label for="endTime" class="form-label fw-bold">End Time</label>
    <input type="time" id="endTime" name="endTime" class="form-control" value="4:00 PM" required/>
  </div>

  <div class="col">
    <label for="counselingDate" class="form-label fw-bold">Preferred Date</label>
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
      <input type="date" id="counselingDate" name="counselingDate" class="form-control" required/>
    </div>
  </div>
</div>
</div>

    <!-- Submit -->
    <div class="text-center mt-4">
      <button type="submit" value="submit" class="btn btn-primary px-4">Save</button>
    </div>
  </form>
</div>