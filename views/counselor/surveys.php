<?php 
    session_start();
    
    include(__DIR__ . "/../../config/utils.php");
    
    // check session first exists first
    if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
      header("location: ../public/counselor-admin-login-page.php");
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
    <link rel="stylesheet" href="../../assets/css/counselor.css">
</head>
<body>
    <?php 
        include(__DIR__ . '/../components/counselor/sidebar.php');
    ?>
    <main>
      <!-- BANNER -->
      <div class="border-bottom py-3 px-3 banner-surveys_c">
      <h6 class="fw-bold mb-0 btext-surveys_c">Surveys</h6>
      </div>

      <!-- TITLE -->
<div class="container pt-2 mt-5">
  <h2 class="text-center fw-bold title-surveys_c">Student Surveys</h2>

  <!-- Button Row -->
  <div class="d-flex justify-content-end gap-2 mt-2 mb-0">
    <button class="btn btn-surveys_c btn-primary" data-bs-toggle="modal" data-bs-target="#addNewModal">
      <i class="bi"></i> <span class="fw-bold">View Survey</span>
    </button>
  </div>
</div>

    <!-- Student Surveys Table Section -->
<div class="container mt-4">
<div class="table-pagination-wrapper">
  <div class="table-responsive table-surveys_c">
    <table class="table align-middle mb-0 table-hover">
      <thead class="table-light thead-surveys_c">
        <tr>
          <th class="sticky-top bg-secondary-subtle text-center">#</th>
          <th class="sticky-top bg-secondary-subtle">Survey Title</th>
          <th class="sticky-top bg-secondary-subtle">Date Submitted</th>
          <th class="sticky-top bg-secondary-subtle text-center">Action</th>
        </tr>
      </thead>
      <tbody id="surveyTableBody">
        <!-- <tr>
          <td class="text-truncate column-surveys_c text-center align-middle">1.</td>
          <td class="align-middle">Lorem ipsum dolor sit amet, conse...</td>
          <td class="align-middle">Dec 9, 2024</td>
          <td class="text-center align-middle">
            <button class="btn btn-viewsurvey_c btn-sm" data-bs-toggle="modal" data-bs-target="#surveyModal">
              View Response
            </button>
          </td>
        </tr> -->
      </tbody>
    </table>
  </div>

<!-- Pagination -->
<div class="d-flex justify-content-end mt-3">
      <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
          <li class="page-item">
            <a class="page-link pagination-surveys_c text-white" href="#">Previous</a>
          </li>
          <li class="page-item active">
            <a class="page-link pagination-surveys_c bg-white border text-black" href="#">1</a>
          </li>
          <li class="page-item">
            <a class="page-link pagination-surveys_c text-white" href="#">Next</a>
          </li>
        </ul>
      </nav>
    </div>

  </div>
</div>


<!-- View Modal -->
<div class="modal fade" id="surveyModal" tabindex="-1" role="dialog" aria-labelledby="surveyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered viewmodal_c" role="document">
    <div class="modal-content survey-viewmodal-content">
      <div class="modal-body vs-modalbody_c">
        <h2 class="survey-title title-viewsurvey_c">Student Survey</h2>
        <p class="survey-instruction instruction-viewsurvey_c"><strong>Instruction:</strong> Please indicate your level of agreement with each statement by selecting one of the following options:</p>
        
        <div class="likert-legend d-flex justify-content-between mb-4 likertlegend-viewsurvey_c">
          <div>
            <span class="likert-circle likertcircle-viewsurvey_c legend-strongly-disagree-viewsurvey_c"></span>
            <span><strong>Strongly Disagree:</strong> You completely disagree with the statement.</span>
          </div>
          <div>
            <span class="likert-circle likertcircle-viewsurvey_c legend-disagree-viewsurvey_c"></span>
            <span><strong>Disagree:</strong> You generally disagree with the statement.</span>
          </div>
          <div>
            <span class="likert-circle likertcircle-viewsurvey_c legend-neutral-viewsurvey_c"></span>
            <span><strong>Neutral:</strong> You neither agree nor disagree; you feel indifferent.</span>
          </div>
          <div>
            <span class="likert-circle likertcircle-viewsurvey_c legend-agree-viewsurvey_c"></span>
            <span><strong>Agree:</strong> You generally agree with the statement.</span>
          </div>
          <div>
            <span class="likert-circle likertcircle-viewsurvey_c legend-strongly-agree-viewsurvey_c"></span>
            <span><strong>Strongly Agree:</strong> You completely agree with the statement.</span>
          </div>
        </div>

        <form id="studentSurveyForm">
          <div class="viewsurvey-questions_c">
            <!-- Question 1 -->
            <div class="viewsurvey-questions_c">
              <div><strong>1. Accessibility of Services:</strong> The counseling office is easily accessible when I need support.</div>
              <div class="likert-row-viewsurvey_c">
                <label>
                  <input type="radio" name="q1" value="1" class="vs_custom-radio sd" checked disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q1" value="2" class="vs_custom-radio d" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q1" value="3" class="vs_custom-radio n" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q1" value="4" class="vs_custom-radio a" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q1" value="5" class="vs_custom-radio sa" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
              </div>
            </div>

            <!-- Question 2 -->
            <div class="viewsurvey-questions_c">
              <div><strong>2. Availability of Counselors:</strong> There are sufficient counselors available for students who need assistance.</div>
              <div class="likert-row-viewsurvey_c">
                <label>
                  <input type="radio" name="q2" value="1" class="vs_custom-radio sd" checked disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q2" value="2" class="vs_custom-radio d" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q2" value="3" class="vs_custom-radio n" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q2" value="4" class="vs_custom-radio a" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q2" value="5" class="vs_custom-radio sa" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
              </div>
            </div>

            <!-- Question 3 -->
            <div class="viewsurvey-questions_c">
              <div><strong>3. Satisfaction with Counseling Sessions:</strong> I am satisfied with the counseling sessions I have attended.</div>
              <div class="likert-row-viewsurvey_c">
                <label>
                  <input type="radio" name="q3" value="1" class="vs_custom-radio sd" checked disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q3" value="2" class="vs_custom-radio d" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q3" value="3" class="vs_custom-radio n" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q3" value="4" class="vs_custom-radio a" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q3" value="5" class="vs_custom-radio sa" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
              </div>
            </div>

            <!-- Question 4 -->
            <div class="viewsurvey-questions_c">
              <div><strong>4. Counselor's Professionalism:</strong> The counselor was professional in their approach and attitude.</div>
              <div class="likert-row-viewsurvey_c">
                <label>
                  <input type="radio" name="q4" value="1" class="vs_custom-radio sd" checked disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q4" value="2" class="vs_custom-radio d" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q4" value="3" class="vs_custom-radio n" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q4" value="4" class="vs_custom-radio a" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q4" value="5" class="vs_custom-radio sa" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
              </div>
            </div>

            <!-- Question 5 -->
            <div class="viewsurvey-questions_c">
              <div><strong>5. General Experience:</strong> Overall, I am satisfied with my experience at the counseling office.</div>
              <div class="likert-row-viewsurvey_c">
                <label>
                  <input type="radio" name="q5" value="1" class="vs_custom-radio sd" checked disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q5" value="2" class="vs_custom-radio d" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q5" value="3" class="vs_custom-radio n" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q5" value="4" class="vs_custom-radio a" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
                <label>
                  <input type="radio" name="q5" value="5" class="vs_custom-radio sa" disabled>
                  <span class="vscustom-circle_c"></span>
                </label>
              </div>
            </div>

          </div>
        </form>

        <!-- Close Button -->
        <button type="button" class="btn viewsurvey-close-btn_c mt-4" data-bs-dismiss="modal" aria-label="Close">Close</button>
      </div>
    </div>
  </div>
</div>


    </main>

    <script>
  const tbody = document.getElementById('surveyTableBody');

  for (let i = 0; i < 1; i++) {
    const row = `
      <tr>
          <td class="text-truncate column-surveys_c text-center align-middle">${i + 1}</td>
          <td class="align-middle">QCU Guidance and Counseling Office Services Survey</td>
          <td class="align-middle"> <?php echo date("M j, Y"); ?></td>
          <td class="d-flex justify-content-center align-items-center">
          <button class="btn btn-viewsurvey_c btn-sm" data-bs-toggle="modal" data-bs-target="#surveyModal">
          View Response
          </button>
          </td>
          </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', row);
  }

  // Example pre-selected survey data (this should come from your database)
const surveyData = {
  q1: '4', // Example value for question 1 (1: Strongly Disagree, 2: Disagree, 3: Neutral, 4: Agree, 5: Strongly Agree)
  q2: '2',
  q3: '3',
  q4: '1',
  q5: '5'
};

// Function to pre-select the radio buttons based on the survey data
function preselectSurveyData() {
  // Loop through each question and set the checked radio button
  for (let question in surveyData) {
    let selectedValue = surveyData[question];
    let radios = document.getElementsByName(question);

    // Loop through the radio buttons for this question
    for (let i = 0; i < radios.length; i++) {
      if (radios[i].value === selectedValue) {
        radios[i].checked = true;
      }
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const surveyModal = document.getElementById('surveyModal');
  
  surveyModal.addEventListener('show.bs.modal', function () {
    preselectSurveyData();
  });
});

</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>