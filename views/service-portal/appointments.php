<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require(__DIR__ . "/../../queries/students.php");
include(__DIR__ . "/../../config/utils.php");

// Include the database connection
$db_conn = require(__DIR__ . "/../../db/db_conn.php");

if (!$db_conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Retrieve student ID from session
$studentId = $_SESSION['student_id'] ?? null;

$student = null; // Ensure $student is always defined
if ($studentId) {
    // Fetch student profile from the database
    $stmt = $db_conn->prepare("SELECT * FROM appt_attendee WHERE student_id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lastname = $_POST['lastname'] ?? $student['last_name'] ?? '';
    $firstname = $_POST['firstname'] ?? $student['first_name'] ?? '';
    $middlename = $_POST['middlename'] ?? $student['middle_name'] ?? '';
    $suffix = $_POST['suffix'] ?? $student['suffix'] ?? null;
    $studentNo = $_POST['student_no'] ?? $student['student_no'] ?? '';
    $programId = $_POST['course'] ?? $student['program_id'] ?? null; // Fixed dropdown name
    $currentYearLevel = $_POST['year'] ?? $student['current_year_level'] ?? ''; // Fixed dropdown name
    $gender = $_POST['gender'] ?? $student['gender'] ?? '';
    $personalContactNo = $_POST['personal-contact-no'] ?? $student['personal_contact_no'] ?? '';
    $studentEmail = $_POST['qcu-email'] ?? $student['student_email'] ?? '';
    $guardianName = $_POST["guardian's-name"] ?? $student['guardian_name'] ?? '';
    $guardianContactNo = $_POST['guardian-contact-no'] ?? $student['guardian_contact_no'] ?? '';
    $preferredDay = $_POST['preferred_day'] ?? ''; // Fixed dropdown name
    $preferredTime = $_POST['preferred_time'] ?? ''; // Fixed dropdown name
    $counselingConcern = $_POST['counseling_concern'] ?? '';
    $addConcernInfo = $_POST['add_concern_info'] ?? '';
    $status = "Pending";
    $apptReqType = "Online";
    $agreeTerms = 1;
    $agreePrivacy = 1;
    $agreeLimits = 1;
    $studentId = $studentId ?? null; // Ensure $studentId is defined and matches the placeholder

    // Check if attendee already exists
    if (!$student) {
        // Insert new attendee if not found
        $stmt_attendee = $db_conn->prepare("INSERT INTO appt_attendee (
            first_name, last_name, middle_name, suffix, student_no, program_id, current_year_level,
            gender, personal_contact_no, student_email, guardian_name, guardian_contact_no
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt_attendee) {
            error_log("Prepare failed for appt_attendee: " . $db_conn->error);
            die("Prepare failed for appt_attendee: " . $db_conn->error);
        }

        $stmt_attendee->bind_param(
            "ssssissssssi",
            $firstname, $lastname, $middlename, $suffix, $studentNo, $programId, $currentYearLevel,
            $gender, $personalContactNo, $studentEmail, $guardianName, $guardianContactNo
        );

        if (!$stmt_attendee->execute()) {
            error_log("Error inserting attendee: " . $stmt_attendee->error);
            echo "Error inserting attendee: " . $stmt_attendee->error;
            exit();
        }

        // Get the last inserted ID from appt_attendee
        $attendeeId = $db_conn->insert_id;
        $stmt_attendee->close();
    } else {
        // Use existing attendee ID
        $attendeeId = $student['attendee_id'];
    }

    // Debug attendee ID
    error_log("Attendee ID: " . $attendeeId);

    // Insert appointment details into the appointments table
    $stmt_appointment = $db_conn->prepare("INSERT INTO appointments (
        attendee_id, preferred_day, preferred_time, counseling_concern, add_concern_info, status,
        appt_req_type, agreedToTermsAndConditions, agreedToDataPrivacyPolicy, agreedToLimitations, appt_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt_appointment) {
        error_log("Prepare failed for appointments: " . $db_conn->error);
        die("Prepare failed for appointments: " . $db_conn->error);
    }

    $apptDate = date("Y-m-d H:i:s");

    $stmt_appointment->bind_param(
        "issssssiiis",
        $attendeeId, $preferredDay, $preferredTime, $counselingConcern, $addConcernInfo, $status,
        $apptReqType, $agreeTerms, $agreePrivacy, $agreeLimits, $apptDate
    );

    if ($stmt_appointment->execute()) {
        // Trigger modal via JS
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                var successModal = new bootstrap.Modal(document.getElementById('yourSuccessModalId'));
                successModal.show();
            });

            // Reset form fields for counseling_concern, preferred_time, and preferred_day
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('counseling_concern').selectedIndex = 0;
                document.getElementById('preferred_time').selectedIndex = 0;
                document.getElementById('preferred_day').selectedIndex = 0;
            });
        </script>";
    }

    $stmt_appointment->close();
}

$db_conn->close();
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
    <link rel="stylesheet" href="../../assets/css/appointment.css">
    <style>
      main {
        min-height: 50vh; 
        padding-bottom: 175px; 
        display: flex;
        flex-direction: column;
      }
    </style>
</head>
<body>
    <?php
        include(__DIR__ . '/../components/service-portal/navbar.php');
    ?>
    <main>
        <header class="header">
                <img src="../../static/appoint_header.jpg" alt="Appointment Header" width="1597" height="550" class="appoint-header-img" position="fixed" >
                <div class="appoint-text-content">
                    <h1>Schedule Appointment Form </h1>
                </div>
        </header>

        <div class="appoint-container">
    <label class="appoint-label" > 
        <input type="checkbox" required>
        Check here to indicate that you have read and agree to the 
        <a href="#">terms of conditions and agreement</a> of the 
        <a href="https://qcu.edu.ph/students/guidance-and-counseling/#:~:text=The%20Guidance%20and%20Counseling%20Unit,capabilities%20to%20resolve%20own%20problems.">Quezon City University Guidance and Counseling Unit</a>.
    </label>
</div>

<div class="appoint-info-text-container">
    <p class="appoint-info-text" >
        Please ensure that all information provided is accurate, as it will be recorded for appointment scheduling. Your details will be kept confidential.
    </p>
      </div>  

      <div class="appoint-form-container">
      <h1>PERSONAL INFORMATION</h1>
      <br>
        <form action="" method="POST">
            <div class="appoint-input-group">
                <label for="lastname">Full Name (Last Name, First Name, Middle Initial)</label>
                <div class="appoint-seperate-inputs">
                <input type="text" id="lastname" name="lastname" value="<?= isset($student['last_name']) ? htmlspecialchars($student['last_name']) : '' ?>" placeholder="Last Name">
                <input type="text" id="firstname" name="firstname" value="<?= isset($student['first_name']) ? htmlspecialchars($student['first_name']) : '' ?>" placeholder="First Name">
                <input type="text" id="mi" name="middlename" value="<?= isset($student['middle_name']) ? htmlspecialchars($student['middle_name']) : '' ?>" placeholder="Middle Name">
            </div>

          <!-- filepath: c:\xampp\htdocs\integrated-service-portal-with-data-analytics\views\service-portal\appointments.php -->
<div class="appoint-input-group">
    <label for="course/yr/sec">Course/Year/Section</label>
    <div class="appoint-seperate-inputs">
        <!-- Dropdown for Course -->
        <select id="course" name="course" class="form-select">
            <option value="" disabled selected>Please Select Course</option>
            <option value="1">Bachelor of Science in Accountancy (BSA)</option>
            <option value="2">Bachelor of Science in Management Accounting (BSMA)</option>
            <option value="3">Bachelor of Science in Information Technology (BSIT)</option>
            <option value="4">Bachelor of Science in Entrepreneurship (BSENT)</option>
            <option value="5">Bachelor of Science in Electronics Engineering (BSEcE)</option>
            <option value="6">Bachelor of Science in Industrial Engineering (BSIE)</option>
            <option value="7">Bachelor of Early Childhood Education (BECED)</option>
            <option value="8">Bachelor of Science in Information Systems (BSIS)</option>
            <option value="9">Bachelor of Science in Computer Science (BSCS)</option>
            <option value="10">Bachelor of Science in Computer Engineering (BSCpE)</option>
        </select>

        <!-- Dropdown for Year -->
        <select id="year" name="year" class="form-select">
            <option value="" disabled selected>Please Select Year</option>
            <option value="1st">1st Year</option>
            <option value="2nd">2nd Year</option>
            <option value="3rd">3rd Year</option>
            <option value="4th">4th Year</option>
        </select>
    </div>
</div>

                <div class="appoint-input-group">
                    <label for="personal-contact-no">Personal Contact Number</label>
                    <input type="text" id="personal-contact-no" name="personal-contact-no" value="<?= isset($student['personal_contact_no']) ? htmlspecialchars($student['personal_contact_no']) : '' ?>" placeholder="+63 000 000 0000">
                </div>
        

            <div class="appoint-input-group">
                <label for="qcu-email">QCU Email Address</label>
                <input type="text" id="qcu-email" name="qcu-email" value="<?= isset($student['qcu_email']) ? htmlspecialchars($student['qcu_email']) : '' ?>" placeholder="example@example.com">
            </div>
            <div class="appoint-row-group">
                <div class="appoint-input-group">
                    <label for="guardian's-name">Guardian's Name</label>
                    <div class="multi-input-group">
                    <input type="text" id="guardian's-name" name="guardian's-name" value="<?= isset($student['guardian_name']) ? htmlspecialchars($student['guardian_name']) : '' ?>" placeholder="Guardian's Full Name">
                    </div>
                </div>

                <div class="appoint-input-group">
                    <label for="guardian-contact-no">Guardian's Contact Number</label>
                    <input type="text" id="guardian-contact-no" name="guardian-contact-no" value="<?= isset($student['guardian_contact_no']) ? htmlspecialchars($student['guardian_contact_no']) : '' ?>" placeholder="+63 000 000 0000">

                </div>
            </div>


            <h1>COUNCELING SESSION</h1>
            
<div class="appoint-input-group">
    <label for="counseling_concern">Counseling Concern</label>
    <select id="counseling_concern" name="counseling_concern" class="form-select">
        <option value="" disabled selected>Please Select</option>
        <option value="Career" <?= isset($_POST['counseling_concern']) && $_POST['counseling_concern'] === 'Career' ? 'selected' : '' ?>>Career</option>
        <option value="Academic" <?= isset($_POST['counseling_concern']) && $_POST['counseling_concern'] === 'Academic' ? 'selected' : '' ?>>Academic</option>
        <option value="Personal" <?= isset($_POST['counseling_concern']) && $_POST['counseling_concern'] === 'Personal' ? 'selected' : '' ?>>Personal</option>
    </select>
</div>

<div class="appoint-input-group">
    <label for="concern">Brief Information About Your Concern</label>
    <input type="text" id="brief-info" name="add_concern_info" placeholder="Your Answer">
</div>

<div class="appoint-input-group">
    <label for="preferred_time">Preferred Time</label>
    <select id="preferred_time" name="preferred_time" class="form-select">
        <option value="" disabled selected>Please Select</option>
        <option value="9:00 AM to 10:00 AM" <?= isset($_POST['preferred_time']) && $_POST['preferred_time'] === '9:00 AM to 10:00 AM' ? 'selected' : '' ?>>9:00 AM - 10:00 AM</option>
        <option value="11:00 AM to 12:00 NN" <?= isset($_POST['preferred_time']) && $_POST['preferred_time'] === '11:00 AM to 12:00 NN' ? 'selected' : '' ?>>11:00 AM - 12:00 NN</option>
        <option value="1:00 PM to 2:00 PM" <?= isset($_POST['preferred_time']) && $_POST['preferred_time'] === '1:00 PM to 2:00 PM' ? 'selected' : '' ?>>1:00 PM - 2:00 PM</option>
        <option value="3:00 PM to 4:00 PM" <?= isset($_POST['preferred_time']) && $_POST['preferred_time'] === '3:00 PM to 4:00 PM' ? 'selected' : '' ?>>3:00 PM - 4:00 PM</option>
    </select>
</div>

<div class="appoint-input-group">
    <label for="preferred_day">Preferred Day</label>
    <select id="preferred_day" name="preferred_day" class="form-select">
        <option value="" disabled selected>Please Select</option>
        <option value="Monday" <?= isset($_POST['preferred_day']) && $_POST['preferred_day'] === 'Monday' ? 'selected' : '' ?>>Monday</option>
        <option value="Tuesday" <?= isset($_POST['preferred_day']) && $_POST['preferred_day'] === 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
        <option value="Wednesday" <?= isset($_POST['preferred_day']) && $_POST['preferred_day'] === 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
        <option value="Thursday" <?= isset($_POST['preferred_day']) && $_POST['preferred_day'] === 'Thursday' ? 'selected' : '' ?>>Thursday</option>
        <option value="Friday" <?= isset($_POST['preferred_day']) && $_POST['preferred_day'] === 'Friday' ? 'selected' : '' ?>>Friday</option>
    </select>
</div>
 
<br>

<button type="button" class="btn appoint-btn-primary" id="appointbookBtn">
  Book Appointment
</button>


<div id="appointtermsModal" class="appoint-modal">
  <div class="appoint-modal-content">
    <div class="appoint-modal-header">
      <h3>Terms of Conditions and Agreement</h3>
    </div>
    <div class="appoint-modal-body">
    <p>
          Counseling is a well-planned, goal-oriented and short-term intervention that aims to help learners manage and overcome issues or concerns that hinder them from attaining success. Its process aids learners to define the problems, their sources, options, and pros and cons, which facilitate them to decide and act appropriately.
        </p>
        <br>
       <h5><b>Privacy and Confidentiality</b></h5>
        <p>
          The information disclosed during the course of the online counseling is confidential. Information about the counselee will only be released with the following exceptions:
          <br>1. If the counselee is becoming an imminent danger to him/herself and/or to others through thoughts of suicide or threats to harm other people.
          <br>2. If there is a reasonable suspicion of emotional and/or physical neglect and/or abuse including sexual abuse of a minor.
          <br>3. In rare cases, if ordered by court.
        </p>
        <br>
        <h5><b>Limitations</b></h5>
        <p>
          It is important to realize that online counseling is intended to provide quality information, practical answers to psychological issues, and online counseling for present problems. You may be referred to other professionals as needed. This service is available during normal business hours. It is not intended to provide in-depth psychotherapy. 
          For emergency, you may call the <b> National Center for Mental Health crisis hotline: 0917899-USAP (8727) or 989-USAP (8727).</b>
        </p>
    </div>
    <div class="appoint-modal-footer">
      <button type="button" class="btn appoint-btn-primary" id="appointreadBtn">I have read and understood the terms</button>
    </div>
  </div>
</div>


<div id="appointreviewModal" class="appoint-modal">
  <div class="appoint-modal-content">
    <div class="appoint-modal-header">
      <h3>Review Your Answers Before Submitting</h3>
    </div>
    <br>
    <div class="appoint-modal-body">
    <p>
          Please take a moment to review your responses. Once submitted, changes may not be possible.
          Make sure all information is correct and complete. If you're ready, click 'Submit' to finalize.
        </p>
    </div>
    <div class="appoint-modal-footer">
      <button type="button" class="btn appoint-btn-cancel" id="appointcancelBtn">Cancel</button>
      <button type="button" class="btn appoint-btn-submit" id="appointsubmitBtn">Submit</button>
    </div>
  </div>
</div>


<div id="appointsuccessModal" class="appoint-modal">
  <div class="appoint-modal-content">
    <div class="appoint-modal-header">
      <h3>Appointment Successfully Scheduled!</h3>
      <br>
    </div>
    <div class="appoint-modal-body">
    Thank you! Your appointment has been recorded. You will receive a confirmation 
          and any further details via your registered email or dashboard. If you need to reschedule or cancel, 
          please visit the 'Schedule Appointments' section.
    </div>
    <div class="appoint-modal-footer">
      <button type="submit" class="btn appoint-btn-secondary" id="appointcloseSuccessModal">Okay</button>
    </div>
</form> <!-- Moved the closing form tag here -->
  </div>
</div>
            


<footer class="appoint-footer">
    <p>&copy; 2025 QCU Guidance and Counseling. All Rights Reserved.</p>
</footer>





<script>
document.getElementById('appointbookBtn').addEventListener('click', function() {
  document.getElementById('appointtermsModal').style.display = 'flex';
});

document.getElementById('appointreadBtn').addEventListener('click', function() {
  document.getElementById('appointreviewModal').style.display = 'flex';
  document.getElementById('appointtermsModal').style.display = 'none';
});

document.getElementById('appointcancelBtn').addEventListener('click', function() {
  document.getElementById('appointtermsModal').style.display = 'none';
  document.getElementById('appointreviewModal').style.display = 'none';
  
});


document.getElementById('appointsubmitBtn').addEventListener('click', function() {
  document.getElementById('appointreviewModal').style.display = 'none';
  document.getElementById('appointsuccessModal').style.display = 'flex';
});


document.getElementById('appointcloseSuccessModal').addEventListener('click', function() {
  document.getElementById('appointsuccessModal').style.display = 'none';
});

appointtermsModal.addEventListener('click', function (event) {
      if (event.target === appointtermsModal)
       {
        appointtermsModal.style.display = 'none';
      }
    });
    

    
    


document.getElementById('course').addEventListener('change', function() {
    const yearDropdown = document.getElementById('year');
    const selectedCourse = this.value;

    // Clear existing options in the year dropdown
    yearDropdown.innerHTML = '<option value="" disabled selected>Please Select Year</option>';

    // Populate year options based on the selected course
    if (selectedCourse === '4') { // Bachelor of Science in Entrepreneurship (BSENT)
        yearDropdown.innerHTML += '<option value="1st">1st Year</option>';
        yearDropdown.innerHTML += '<option value="2nd">2nd Year</option>';
        yearDropdown.innerHTML += '<option value="3rd">3rd Year</option>';
    } else {
        yearDropdown.innerHTML += '<option value="1st">1st Year</option>';
        yearDropdown.innerHTML += '<option value="2nd">2nd Year</option>';
        yearDropdown.innerHTML += '<option value="3rd">3rd Year</option>';
        yearDropdown.innerHTML += '<option value="4th">4th Year</option>';
    }


    });

</script>

<script>
  const dropdowns = document.querySelectorAll('.appoint-dropdown');

  dropdowns.forEach(dropdown => {
    const button = dropdown.querySelector('.appoint-dropdown-btn');
    const items = dropdown.querySelectorAll('.appoint-dropdown-item');

    items.forEach(item => {
      item.addEventListener('click', function(e) {
        e.preventDefault(); 
        button.textContent = this.textContent;
      });
    });
  });
</script>
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>