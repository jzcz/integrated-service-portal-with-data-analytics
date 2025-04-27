<?php 
    session_start();

    require(__DIR__ . "/../../queries/students.php");
    include(__DIR__ . "/../../config/utils.php");
    
    // check session first exists first
    if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
      header("location: ../service-portal/login.php");
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
    <link rel="stylesheet" href="../../assets/css/student-survey.css">
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
                <div class="studsurv-text-content">
                    <h1>Student Survey </h1>
                </div>
        </header>


    


<div class="studsurv-info-text-container">
    <p class="studsurv-info-text" >
       
    <b>Instruction:</b> Please indicate your level of agreement with each statement by selecting on of the following options:
    </p>
<div></div>


  <div class="studsurv-circle-row">
    <div class="studsurv-circle-with-label">
      <div class="studsurv-circle-indicator" style="background-color: #29477B;"></div>
      <span class="studsurv-circle-label"><b>Strongly Disagree</b>
    <br>
  You completely disagree with the statement.</span>
    </div>
    
    <div class="studsurv-circle-with-label">
      <div class="studsurv-circle-indicator" style="background-color: #4472C4;"></div>
      <span class="studsurv-circle-label"><b>Disagree</b>
    <br>
  You generally disagree with the statement.</span>
    </div>

    <div class="studsurv-circle-with-label">
      <div class="studsurv-circle-indicator studsurv-neutral-circle" style="background-color: #FFFFFF;"></div>
      <span class="studsurv-circle-label"><b>Neutral</b>
    <br>
  You neither agree nor; you feel indifferent.</span>
    </div>

    <div class="studsurv-circle-with-label">
      <div class="studsurv-circle-indicator" style="background-color: #8197D0;"></div>
      <span class="studsurv-circle-label"><b>Agree</b>
    <br>

  You generally agree with the statement.</span>
    </div>

    <div class="studsurv-circle-with-label">
      <div class="studsurv-circle-indicator" style="background-color: #C7D5FC;"></div>
      <span class="studsurv-circle-label"><b>Strongly Agree</b>
    <br>
  You completely agree with the statement.</span>
    </div>
  </div>
</div>

<div class="studsurv-form-container">

  <div class="studsurv-label-container">
    <studsurv-label for="feedback"><b>1. Accessibility of Services:</b> The counseling office is easily accessible when I need support.</studsurv-label>
    <div class="studsurv-question-block">
      <div class="studsurv-radio-buttons">
        <input type="radio" id="stronglyDisagree1" name="question1" class="studsurv-radio-button">
        <label for="stronglyDisagree1" class="studsurv-radio-label1 studsurv-position-radi"></label>

        <input type="radio" id="disagree1" name="question1" class="studsurv-radio-button">
        <label for="disagree1" class="studsurv-radio-label2 studsurv-position-radi"></label>

        <input type="radio" id="neutral1" name="question1" class="studsurv-radio-button">
        <label for="neutral1" class="studsurv-radio-label3 studsurv-neutral-circle studsurv-position-radi"></label>

        <input type="radio" id="agree1" name="question1" class="studsurv-radio-button">
        <label for="agree1" class="studsurv-radio-label4 studsurv-position-radi"></label>

        <input type="radio" id="stronglyAgree1" name="question1" class="studsurv-radio-button">
        <label for="stronglyAgree1" class="studsurv-radio-label5 studsurv-position-radi"></label>
      </div>

      <div class="studsurv-label-border"></div>

      <label for="stronglyDisagree2"><b>2. Professionalism of Staff:</b> The guidance counselors are professional and courteous.</label>

      <div class="studsurv-radio-buttons">
        <input type="radio" id="stronglyDisagree2" name="question2" class="studsurv-radio-button">
        <label for="stronglyDisagree2" class="studsurv-radio-label1 studsurv-position-radi"></label>

        <input type="radio" id="disagree2" name="question2" class="studsurv-radio-button">
        <label for="disagree2" class="studsurv-radio-label2 studsurv-position-radi"></label>

        <input type="radio" id="neutral2" name="question2" class="studsurv-radio-button">
        <label for="neutral2" class="studsurv-radio-label3 studsurv-neutral-circle studsurv-position-radi"></label>

        <input type="radio" id="agree2" name="question2" class="studsurv-radio-button">
        <label for="agree2" class="studsurv-radio-label4 studsurv-position-radi"></label>

        <input type="radio" id="stronglyAgree2" name="question2" class="studsurv-radio-button">
        <label for="stronglyAgree2" class="studsurv-radio-label5 studsurv-position-radi"></label>
      </div>

      <div class="studsurv-label-border"></div>

      <label for="stronglyDisagree3"><b>3. Timeliness of Response:</b> My appointments and request were handled in a timely manner.</label>

      <div class="studsurv-radio-buttons">
        <input type="radio" id="stronglyDisagree3" name="question3" class="studsurv-radio-button">
        <label for="stronglyDisagree3" class="studsurv-radio-label1 studsurv-position-radi"></label>

        <input type="radio" id="disagree3" name="question3" class="studsurv-radio-button">
        <label for="disagree3" class="studsurv-radio-label2 studsurv-position-radi"></label>

        <input type="radio" id="neutral3" name="question3" class="studsurv-radio-button">
        <label for="neutral3" class="studsurv-radio-label3 studsurv-neutral-circle studsurv-position-radi"></label>

        <input type="radio" id="agree3" name="question3" class="studsurv-radio-button">
        <label for="agree3" class="studsurv-radio-label4 studsurv-position-radi"></label>

        <input type="radio" id="stronglyAgree3" name="question3" class="studsurv-radio-button">
        <label for="stronglyAgree3" class="studsurv-radio-label5 studsurv-position-radi"></label>
      </div>

      <div class="studsurv-label-border"></div>

      <label for="stronglyDisagree4"><b>4. Effectiveness of Counseling Sessions:</b> The counseling sessions helped me address my concerns effectively.</label>

      <div class="studsurv-radio-buttons">
        <input type="radio" id="stronglyDisagree4" name="question4" class="studsurv-radio-button">
        <label for="stronglyDisagree4" class="studsurv-radio-label1 studsurv-position-radi"></label>

        <input type="radio" id="disagree4" name="question4" class="studsurv-radio-button">
        <label for="disagree4" class="studsurv-radio-label2 studsurv-position-radi"></label>

        <input type="radio" id="neutral4" name="question4" class="studsurv-radio-button">
        <label for="neutral4" class="studsurv-radio-label3 studsurv-neutral-circle studsurv-position-radi"></label>

        <input type="radio" id="agree4" name="question4" class="studsurv-radio-button">
        <label for="agree4" class="studsurv-radio-label4 studsurv-position-radi"></label>

        <input type="radio" id="stronglyAgree4" name="question4" class="studsurv-radio-button">
        <label for="stronglyAgree4" class="studsurv-radio-label5 studsurv-position-radi"></label>
      </div>

      <div class="studsurv-label-border"></div>

      <label for="stronglyDisagree5"><b>5. Confidentiality of Services:</b> I feel confident that the counseling office maintains the confidentiality of my information.</label>

      <div class="studsurv-radio-buttons">
        <input type="radio" id="stronglyDisagree5" name="question5" class="studsurv-radio-button">
        <label for="stronglyDisagree5" class="studsurv-radio-label1 studsurv-position-radi"></label>

        <input type="radio" id="disagree5" name="question5" class="studsurv-radio-button">
        <label for="disagree5" class="studsurv-radio-label2 studsurv-position-radi"></label>

        <input type="radio" id="neutral5" name="question5" class="studsurv-radio-button">
        <label for="neutral5" class="studsurv-radio-label3 studsurv-neutral-circle studsurv-position-radi"></label>

        <input type="radio" id="agree5" name="question5" class="studsurv-radio-button">
        <label for="agree5" class="studsurv-radio-label4 studsurv-position-radi"></label>

        <input type="radio" id="stronglyAgree5" name="question5" class="studsurv-radio-button">
        <label for="stronglyAgree5" class="studsurv-radio-label5 studsurv-position-radi"></label>
      </div>
    

   

      <button type="button" class="btn studsurv-btn-primary" id="studsurvtsubmitBtn">
  <b>SUBMIT</b>
</button>


<div id="studentsurvopeningModal" class="studsurv-modal studsurvmodal-fade">
  <div class="studsurv-modal-content">
    <div class="studsurv-modal-header">
      <h3>Share Your Feedback</h3>
    </div>

    <img src="../../static/studdent-survey-opening.png" alt="Student Survey Opening" width="470" height="200" class="studsurv-opening-img" position="fixed">
    
    <div class="studsurv-modal-body">
      <p>
        We appreciate your feedback! Please take a moment to complete this survey regarding your experience with our Guidance and Counseling Services.
        Your insights are valuable in helping us improve our offerings and better meet the needs of all students. All responses will remain confidential.
      </p>

      <div class="studsurv-modal-footer">
        <button type="button" class="btn studsurv-btn-secondary" id="studsurvgetBtn">Get Started!</button>
      </div>
    </div>
  </div>
</div>


<div id="studsurvclosingModal" class="studsurv-modal studsurvmodal-fade">
  <div class="studsurv-closingmodal-content">
    <div class="studsurv-modal-header">
      <h3>Thank You For Sharing Your Feedback!</h3>
    </div>
    <div class="studsurv-modal-body">
      <p>
        
        Thank you for taking the time to complete our survey. Your feedback is valuable in helping us improve our services and better support students like you.
        If you have any additional comments or need further assistance, feel free to reach out to our Guidance and Counseling team.
      </p>

      <div class="studsurv-modal-footer">
        <button type="button" class="btn studsurv-btn-close" id="studsurvcloseBtn">Okay</button>
      </div>
    </div>
  </div>
</div>


<footer class="studsurv-footer">
  <p>&copy; 2025 QCU Guidance and Counseling. All Rights Reserved.</p>
</footer>


<script>

window.onload = function () {
  document.getElementById("studentsurvopeningModal").style.display = "flex";
};


document.getElementById("studsurvgetBtn").addEventListener("click", function () {
  document.getElementById("studentsurvopeningModal").style.display = "none";
});


document.getElementById("studsurvtsubmitBtn").addEventListener("click", function (e) {
  e.preventDefault(); 
  document.getElementById("studsurvclosingModal").style.display = "flex";


});

document.getElementById("studsurvcloseBtn").addEventListener("click", function () {
  document.getElementById("studsurvclosingModal").style.display = "none";
});
</script>
       
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>     