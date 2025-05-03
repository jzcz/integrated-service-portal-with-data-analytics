<?php 
    session_start();
    $db_conn = require( __DIR__ . "/../../db/db_conn.php");

    $err = null;
    $success = null;
    

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET["err"])) {
            $err = $_GET["err"];
        }

        if(isset($_GET["success"])) {
            $success = $_GET["success"];
        }
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {     
        try {
            $questionOneOptionId = $_POST['question-one-option-id'];
            $questionTwoOptionId = $_POST['question-two-option-id'];    
            $questionThreeOptionId = $_POST['question-three-option-id'];
            $questionFourOptionId = $_POST['question-four-option-id'];
            $questionFiveOptionId = $_POST['question-five-option-id'];
            $db_conn->begin_transaction(); // Start transaction
            // Insert into survey_responses
            $insertSurveyResponse = $db_conn->prepare("INSERT INTO survey_responses (survey_id, created_at) VALUES (?, NOW())");
            $surveyId = 1;
            $insertSurveyResponse->bind_param("i", $surveyId);
            $insertSurveyResponse->execute();

            // Get the last inserted ID
            $responseId = $db_conn->insert_id;

            // Now insert into survey_answers
            $insertSurveyAnswer = $db_conn->prepare("
                INSERT INTO survey_answers 
                    (survey_response_id, survey_option_id, survey_question_id, survey_id) 
                VALUES 
                    (?, ?, ?, ?)
            ");

            // Repeat for each question
            $questionData = [
                [$questionOneOptionId, 1],
                [$questionTwoOptionId, 2],
                [$questionThreeOptionId, 3],
                [$questionFourOptionId, 4],
                [$questionFiveOptionId, 5],
            ];

            foreach ($questionData as [$optionId, $questionId]) {
                echo "Option ID: $optionId, Question ID: $questionId<br>"; 
                $insertSurveyAnswer->bind_param("iiii", $responseId, $optionId, $questionId, $surveyId);
                $insertSurveyAnswer->execute();
            }

            $db_conn->commit(); 
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=Thank you for your feedback! It will help us improve QCU Guidance and Counseling Services."); // Redirect to the same page with success message
        } catch (Exception $e) {
            $db_conn->rollback(); // Something went wrong, roll back
            header("Location: " . $_SERVER['PHP_SELF'] . "?errFailed to submit your survey! Please try again later."); // Redirect to the same page with success message
        }
    
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
    <title>Survey</title>
    <style>
        .submit-btn {
            background-color: #08437F;
            color:white;
        }

        .submit-btn:hover {
            background-color:rgb(42, 115, 188);
            color:white;
        }
    </style>
</head>
<body>
    <?php
        include(__DIR__ . '/../components/service-portal/navbar.php');
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
    <main class="">
        <div class="container w-75">
            <div>
                <h3 class="fw-bold text-center pt-4 pb-3">QCU Guidance and Counseling Unit Services Survey</h3>
                <p style="font-size: 15px;" class="text-center">Help us improve! This survey gathers your feedback 
                    about the services provided by the QCU Guidance and Counseling Office
                </p>
            </div>
            <div>
                <form action="" method="post">
                    <p class="text-center"><span class="fst-italic fw-bold">Instructions:</span> Please indicate your level of agreement with each statement by selecting one of the following options:</p>
                    <p class="text-center"><span class="fw-bold">(1)</span> Strongly Disagree <span class="fw-bold">(2)</span> Disagree <span class="fw-bold">(3)</span> Neutral <span class="fw-bold">(4)</span> Agree <span class="fw-bold">(5)</span> Strongly Agree</p>
                    <div class="mb-4 mt-4">
                        <p style="font-size: 15px;" ><span class="fw-bold" >Accessibility of Service</span>: The counseling office is easily accessible when I need support.</p>
                        <select class="form-select form-select-sm" name="question-one-option-id" id="">
                            <option value="" selected disabled>Choose Option</option>
                            <option value="1">Strongly Disagree</option>
                            <option value="2">Disagree</option>
                            <option value="3">Neutral</option>
                            <option value="4">Agree</option>
                            <option value="5">Strongly Agree</option>
                        </select>
                    </div>
                    <div class="mb-4 mt-2">
                        <p style="font-size: 15px;" ><span class="fw-bold">Professionalism of Staff</span>: The guidance counselors are professional and courteous.</p>
                        <select class="form-select form-select-sm" name="question-two-option-id" id="">
                            <option value="" selected disabled>Choose Option</option>
                            <option value="6">Strongly Disagree</option>
                            <option value="7">Disagree</option>
                            <option value="8">Neutral</option>
                            <option value="9">Agree</option>
                            <option value="10">Strongly Agree</option>
                        </select>
                    </div>
                    <div class="mb-4 mt-2">
                        <p style="font-size: 15px;" ><span class="fw-bold">Timeliness of Response</span>: My appointments and requests were handled in a timely manner.</p>
                        <select class="form-select form-select-sm" name="question-three-option-id" id="">
                            <option value="" selected disabled>Choose Option</option>
                            <option value="11">Strongly Disagree</option>
                            <option value="12">Disagree</option>
                            <option value="13">Neutral</option>
                            <option value="14">Agree</option>
                            <option value="15">Strongly Agree</option>
                        </select>
                    </div>
                    <div class="mb-4 mt-2">
                        <p style="font-size: 15px;" ><span class="fw-bold">Effectiveness of Counseling Sessions</span>: The counseling sessions helped me address my concerns effectively.</p>
                        <select class="form-select form-select-sm" name="question-four-option-id" id="">
                            <option value="" selected disabled>Choose Option</option>
                            <option value="16">Strongly Disagree</option>
                            <option value="17">Disagree</option>
                            <option value="18">Neutral</option>
                            <option value="19">Agree</option>
                            <option value="20">Strongly Agree</option>
                        </select>
                    </div>
                    <div class="mb-4 mt-2">
                        <p style="font-size: 15px;" ><span class="fw-bold">Confidentiality of Services</span>: I feel confident that the counseling office maintains the confidentiality of my information.</p>
                        <select class="form-select form-select-sm" name="question-five-option-id" id="">
                            <option value="" selected disabled>Choose Option</option>
                            <option value="21">Strongly Disagree</option>
                            <option value="22">Disagree</option>
                            <option value="23">Neutral</option>
                            <option value="24">Agree</option>
                            <option value="25">Strongly Agree</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn submit-btn ">Submit Response</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const messageModal = new bootstrap.Modal(document.getElementById("message-modal"));
        <?php if($err || $success) { echo "messageModal.show();"; }  ?>
    </script>
</body>
</html>