<?php 
    session_start();
    
    include(__DIR__ . "/../../config/utils.php");
    
    // check session first exists first
    if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
      header("location: ../public/counselor-admin-login-page.php");
      exit();
    }
    
    $db_conn = require_once(__DIR__ . '/../../db/db_conn.php');

    $page = 1;

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
      if(isset($_GET["page_form"])) {
        $page = $_GET['page'];
      }
    } 

    function getAllSurveyResponses($db_conn, $page) {
      $pageSize = 50;
      $offset = ($page - 1) * $pageSize;

      $sql = "SELECT  r.created_at, q.question_text as question, q.survey_question_id as question_id,o.option_text as answer, r.survey_response_id as response_id FROM survey_responses r 
      JOIN survey_answers a on r.survey_response_id = a.survey_response_id
      JOIN  survey_options o on a.survey_option_id = o.survey_option_id
      JOIN survey_questions q on a.survey_question_id = q.survey_question_id
      WHERE r.survey_id = 1 ORDER BY response_id DESC LIMIT $pageSize OFFSET $offset ";

  
      $stmt = $db_conn->prepare($sql);
      $stmt->execute();
      $results = $stmt->get_result();
      $allResponses = $results->fetch_all(MYSQLI_ASSOC);

      if (empty($allResponses)) {
        return [];
      } else {
        foreach ($allResponses  as $row) {
          $responseId = $row['response_id'];
      
          // If this response_id hasn't been initialized yet
          if (!isset($groupedResponses[$responseId])) {
              $groupedResponses[$responseId] = [
                  "response_id" => $responseId,
                  "created_at" => $row['created_at'],
                  "survey" => []
              ];
          }
      
          // Append the question+answer inside the "survey"
          $groupedResponses[$responseId]['survey'][] = [
              "question" => $row['question'],
              "answer" => $row['answer']
          ];
        } 
        return $groupedResponses;
      }
    }



    $groupedResponses = getAllSurveyResponses($db_conn, $page);



function getSurvey($db_conn) {
    $sql = "SELECT question_text FROM survey_questions WHERE survey_id = 1;";
    $stmt = $db_conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->get_result();
    return $results->fetch_all(MYSQLI_ASSOC);
}

$questions = getSurvey($db_conn);

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
    <link rel="stylesheet" href="../../assets/css/counselor.css">
</head>
<body>
    <?php 
        include(__DIR__ . '/../components/counselor/sidebar.php');
    ?>
    <main>
      <!-- BANNER -->
      <div class="border-bottom py-3 px-3 banner-surveys_c">
      <h6 class="fw-bold mb-0 btext-surveys_c">Evaluation Survey Responses</h6>
      </div>

      <!-- TITLE -->
<div class="container pt-4">

  <!-- Button Row -->
  <div class="d-flex justify-content-end gap-2 mt-2 mb-0">
    <button class="btn btn-surveys_c btn-primary" data-bs-toggle="modal" data-bs-target="#viewSurvey">
      <i class="bi"></i> <span class="fw-bold">View Survey</span>
    </button>
  </div>
</div>

  <div class="modal fade" id="viewSurvey" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="font-size: 15px;">
      <div class="modal-content">
        <div class="modal-header">
          <p class="modal-title text-white" id="exampleModalLabel">Evaluation Survey</p>
        </div>
        <div class="modal-body">
        
        <?php foreach($questions as $q) { ?>
          <div class="pb-4">
            <p class="mb-1"><?php echo $q['question_text'] ?></p>
            <div class="form-check">
              <input class="form-check-input" type="radio" id="radioDefault2" disabled>
              <label class="form-check-label text-body-emphasis" for="radioDefault2">Strongly Agree</label>
            </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" id="radioDefault3" disabled>
            <label class="form-check-label text-body-emphasis" for="radioDefault3">Agree</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" id="radioDefault4" disabled>
            <label class="form-check-label text-body-emphasis" for="radioDefault4">Neutral</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" id="radioDefault5"  disabled>
            <label class="form-check-label text-body-emphasis" for="radioDefault5">Disagree</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" id="radioDefault6"  disabled>
            <label class="form-check-label text-body-emphasis" for="radioDefault6">Strongly Disagree</label>
          </div>
        </div>
        <?php } ?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
  </div>
</div></div>



    <!-- Student Surveys Table Section -->
<div class="container mt-4">
<div class="">
  <div class="">
    <table class="table-bordered rounded table text-center">
      <thead class="table-light thead-surveys_c">
        <tr>
          <th class="sticky-top bg-secondary-subtle text-center">#</th>
          <th class="sticky-top bg-secondary-subtle">Survey Title</th>
          <th class="sticky-top bg-secondary-subtle">Date Submitted</th>
          <th class="sticky-top bg-secondary-subtle text-center">Action</th>
        </tr>
      </thead>
      <tbody id="surveyTableBody">
        <?php if($groupedResponses !== null) {
                  foreach ($groupedResponses as $index => $response): ?>
                    <tr>
                      <td class="text-truncate column-surveys_c text-center align-middle"><?php echo $response["response_id"]  ?></td>
                      <td class="align-middle">QCU Guidance and Counseling Office Services Survey</td>
                      <td class="align-middle"> <?php echo date("M j, Y, D", strtotime($response['created_at'])); ?></td>
                      <td class="d-flex justify-content-center align-items-center">
                        <button class="btn btn-viewsurvey_c btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#response-modal-<?php echo $index; ?>">
                          View Response
                        </button>
                      </td>
                    </tr>
                    <div class="modal fade" id  ="response-modal-<?php echo $index; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-scrollable" style="font-size: 15px;">
                        <div class="modal-content">
                          <div class="modal-header">
                            <p class="modal-title text-white" id="exampleModalLabel">Survey Response</p>
                          </div>
                          <div class="modal-body">
                            <?php foreach($response['survey'] as $responseItem) { ?>
                            <div class="pb-4">
                            <p class="mb-1"><?php echo $responseItem['question']; ?></p>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" id="radioDefault2" 
                              <?php if($responseItem['answer'] == "Strongly Agree") { echo "checked" ;} else { echo " disabled"; } ?>>
                              <label class="form-check-label text-body-emphasis" for="radioDefault2">Strongly Agree</label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" id="radioDefault3" 
                              <?php if($responseItem['answer'] == "Agree") { echo "checked" ;} else { echo " disabled"; } ?>>
                              <label class="form-check-label text-body-emphasis" for="radioDefault3">
                                Agree
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" id="radioDefault4" 
                              <?php if($responseItem['answer'] == "Neutral") { echo "checked" ;} else { echo " disabled"; } ?>>
                              <label class="form-check-label text-body-emphasis" for="radioDefault4">
                                Neutral
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" id="radioDefault5" 
                              <?php if($responseItem['answer'] == "Disagree") { echo "checked" ;} else { echo " disabled"; } ?>>
                              <label class="form-check-label text-body-emphasis" for="radioDefault5">
                                Disagree
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" id="radioDefault6" 
                              <?php if($responseItem['answer'] == "Strongly Disagree") { echo "checked" ;} else { echo " disabled";  } ?>>
                              <label class="form-check-label text-body-emphasis" for="radioDefault6">
                                Strongly Disagree
                              </label>
                              </div>
                            </div>
                            <?php } ?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; 
        } else { ?>

<?php  } ?>

      </tbody>
    </table>
  </div>

<!-- Pagination -->
<div class="appt-page-nav-wrapper mt-4">
            <form action="" method="get">
              <input type="hidden" name="page_form" value="true" hidden>
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item">
                            <button type="submit" name="page" value="<?php echo $page - 1; ?>" class="page-link page-nav-link <?php echo ($page == 1) ? ' disabled' : ''; ?>" <?php echo ($page == 1) ? ' disabled' : ''; ?>>Previous</button>
                        </li>
                        <li class="page-item"><button class="page-link text-body-emphasis"><?php echo $page ?></button></li>
                        <li class="page-item">
                            <button type="submit" name="page" value=<?php echo $page + 1?> class="page-link page-nav-link">Next</button>
                        </li>
                    </ul>
                </nav>
            </form>
        </div>


<!-- View Modal -->



    </main>

    <script>


</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>