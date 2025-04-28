  <?php 
 // Directly start session
session_start();

// Include necessary files
require(__DIR__ . "/../../queries/students.php");
include(__DIR__ . "/../../config/utils.php");

// Include DB connection
$db_conn = include(__DIR__ . "/../../db/db_conn.php");
$conn = $db_conn; // Now $conn is safe to use

  // // Check if student is logged in
  // if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
  //     header("location: ../service-portal/login.php");
  //     exit();
  // }

  $studentId = $_SESSION['studentId'];

// Default values
$student = [
    'first_name' => '',
    'last_name' => '',
    'middle_name' => '',
    'suffix' => '',
    'student_no' => '',
    'program_id' => '',
    'current_year_level' => '',
    'gender' => '',
    'personal_contact_no' => '',
    'student_email' => '',
    'guardian_name' => '',
    'guardian_contact_no' => '',
    'course' => '',
    'year' => '',
    'section' => '',
    'qcu_email' => ''
];

// Fetch student data
$query = "SELECT first_name, last_name, middle_name, suffix, student_no, program_id, current_year_level, gender, personal_contact_no, student_email, guardian_name, guardian_contact_no 
          FROM appt_attendee 
          WHERE student_id = ? LIMIT 1";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $student = array_merge($student, $data);
    }
    $stmt->close();
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
        <form>
            <div class="appoint-input-group">
                <label for="lastname">Full Name (Last Name, First Name, Middle Initial)</label>
                <div class="appoint-seperate-inputs">
                    <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($student['last_name']) ?>" placeholder="Last Name">
                    <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($student['first_name']) ?>" placeholder="First Name">
                    <input type="text" id="mi" name="middleinitial" value="<?= htmlspecialchars($student['middle_name']) ?>" placeholder="Middle Name">
            </div>

  
                <div class="appoint-input-group">
                <label for="course/yr/sec">Course/Year/Section</label>
                <div class="appoint-seperate-inputs">
                    <input type="text" id="course" name="course"value="<?= htmlspecialchars($student['course']) ?>" placeholder="Course">
                    <input type="text" id="year" name="year" value="<?= htmlspecialchars($student['year']) ?>" placeholder="Year">
                    <input type="text" id="section" name="section" value="<?= htmlspecialchars($student['section']) ?>" placeholder="Section">
            </div>

                <div class="appoint-input-group">
                    <label for="personal-contact-no">Personal Contact Number</label>
                    <input type="text" id="personal-contact-no" name="personal-contact-no" value="<?= htmlspecialchars($student['personal_contact_no']) ?>" placeholder="+63 000 000 0000">
                </div>
        

            <div class="appoint-input-group">
                <label for="qcu-email">QCU Email Address</label>
                <input type="text" id="qcu-email" name="qcu-email" value="<?= htmlspecialchars($student['qcu_email']) ?>" placeholder="example@example.com">
            </div>
            <div class="appoint-row-group">
                <div class="appoint-input-group">
                    <label for="guardian's-name">Guardian's Name</label>
                    <div class="multi-input-group">
                    <input type="text" id="guardian's-name" name="guardian's-name" value="<?= htmlspecialchars($student['guardian_name']) ?>" placeholder="Guardian's Full Name">
                    </div>
                </div>

                <div class="appoint-input-group">
                    <label for="guardian-contact-no">Guardian's Contact Number</label>
                    <input type="text" id="guardian-contact-no" name="guardian-contact-no" value="<?= htmlspecialchars($student['guardian_contact_no']) ?>" placeholder="+63 000 000 0000">
                </div>
            </div>


            <h1>COUNCELING SESSION</h1>
            
            <div class="appoint-input-group">
  <div class="appoint-dropdown">
    <label for="coun-concern">Counseling Concern</label>
    <button class="appoint-dropdown-btn" type="button">Please Select</button>
    <ul class="appoint-dropdown-menu">
      <li><a class="appoint-dropdown-item" href="#">Career</a></li>
      <li><a class="appoint-dropdown-item" href="#">Academic</a></li>
      <li><a class="appoint-dropdown-item" href="#">Personal</a></li>
    </ul>
  </div>
</div>

<div class="appoint-input-group">
                <label for="concern">Brief Information About Your Concern</label>
                <input type="text" id="brief-info" name="bried-info" placeholder="Your Answer">

          
 <div class="appoint-input-group">
  <div class="appoint-dropdown">
    <label for="time">Preferred Time of Counseling Schedule</label>
    <button class="appoint-dropdown-btn" type="button">Please Select</button>
    <ul class="appoint-dropdown-menu">
      <li><a class="appoint-dropdown-item" href="#">9:00 AM - 10:00 AM</a></li>
      <li><a class="appoint-dropdown-item" href="#">11:00 AM - 12:00 NN</a></li>
      <li><a class="appoint-dropdown-item" href="#">1:00 PM - 2:00 PM</a></li>
      <li><a class="appoint-dropdown-item" href="#">3:00 PM - 4:00 PM</a></li>
    </ul>
  </div>
</div>

<div class="appoint-input-group">
  <div class="appoint-dropdown">
    <label for="day">Preferred Day of Counseling Schedule</label>
    <button class="appoint-dropdown-btn" type="button">Please Select</button>
    <ul class="appoint-dropdown-menu">
      <li><a class="appoint-dropdown-item" href="#">Monday</a></li>
      <li><a class="appoint-dropdown-item" href="#">Tuesday</a></li>
      <li><a class="appoint-dropdown-item" href="#">Wednesday</a></li>
      <li><a class="appoint-dropdown-item" href="#">Thursday</a></li>
      <li><a class="appoint-dropdown-item" href="#">Friday</a></li>
    </ul>
  </div>
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
      <button type="button" class="btn appoint-btn-secondary" id="appointcloseSuccessModal">Okay</button>
    </div>
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
    appointsuccessModal.addEventListener('click', function (event) {
      if (event.target === appointsuccessModal)
       {
        appointsuccessModal.style.display = 'none';
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