<?php
session_start();

include(__DIR__ . "/../../config/utils.php");

// Check session first exists
if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
    header("location: ../public/counselor-admin-login-page.php");
    exit();
}

// Database connection
$db_conn = require(__DIR__ . "/../../db/db_conn.php");

// Test database connection
if ($db_conn->connect_error) {
    die("Database connection failed: " . $db_conn->connect_error);
}

// Fetch assessments and their respondent counts
$assessments = [];
$sql = "
    SELECT 
        a.assessment_id, 
        a.assessment_title, 
        a.assessment_desc,
        a.created_at, 
        COUNT(ar.assessment_response_id) AS respondent_count
    FROM assessments a
    LEFT JOIN assessment_responses ar ON a.assessment_id = ar.assessment_id
    WHERE a.is_deleted = 0
    GROUP BY a.assessment_id
";
$result = $db_conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assessments[] = $row;
    }
} else {
    $error = "No assessments found or query failed: " . $db_conn->error;
}

// Handle AJAX request for getting assessment details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_assessment_details') {
    $assessmentId = $db_conn->real_escape_string($_POST['assessment_id']);

    // Fetch assessment details
    $sql = "SELECT assessment_title, assessment_desc FROM assessments WHERE assessment_id = '$assessmentId' AND is_deleted = 0";
    $result = $db_conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $assessment = $result->fetch_assoc();

        // Fetch questions
        $questions = [];
        $sql = "SELECT assessment_question_id AS question_id, question_title, question_text FROM assessment_questions WHERE assessment_id = '$assessmentId'";
        $questionResult = $db_conn->query($sql);

        if ($questionResult && $questionResult->num_rows > 0) {
            while ($questionRow = $questionResult->fetch_assoc()) {
                $questionId = $questionRow['question_id'];

                // Fetch options for this question
                $options = [];
                $optionSql = "SELECT option_text FROM assessment_options WHERE assessment_question_id = '$questionId'";
                $optionResult = $db_conn->query($optionSql);

                if ($optionResult && $optionResult->num_rows > 0) {
                    while ($optionRow = $optionResult->fetch_assoc()) {
                        $options[] = [
                            'text' => $optionRow['option_text']
                        ];
                    }
                }

                $questions[] = [
                    'id' => $questionId,
                    'title' => $questionRow['question_title'],
                    'text' => $questionRow['question_text'],
                    'options' => $options
                ];
            }
        }

        echo json_encode([
            'success' => true,
            'title' => $assessment['assessment_title'],
            'description' => $assessment['assessment_desc'],
            'questions' => $questions
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Assessment not found.']);
    }
    exit();
}

// Handle form submission for deleting an assessment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_assessment') {
    $assessmentId = $db_conn->real_escape_string($_POST['assessment_id']);
    $sql = "UPDATE assessments SET is_deleted = 1 WHERE assessment_id = '$assessmentId'";
    if ($db_conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $db_conn->error]);
    }
    exit();
}

// Handle form submission for adding a new assessment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_assessment') {
    $title = $db_conn->real_escape_string($_POST['title']);
    $description = $db_conn->real_escape_string($_POST['description']);
    $questionsJson = $_POST['questions'];
    $questions = json_decode($questionsJson, true);

    // Ensure the counselorId exists in the session
    if (!isset($_SESSION['counselorId'])) {
        die("Error: Counselor ID is missing from the session.");
    }
    $counselorId = $_SESSION['counselorId'];

    // Start transaction
    $db_conn->begin_transaction();

    try {
        // Insert assessment
        $sql = "INSERT INTO assessments (assessment_title, assessment_desc, created_at, is_archived, created_by) 
                VALUES ('$title', '$description', NOW(), 0, '$counselorId')";
        if (!$db_conn->query($sql)) {
            throw new Exception("Error inserting assessment: " . $db_conn->error);
        }
        $assessmentId = $db_conn->insert_id;

        // Insert questions and options
        if (!empty($questions)) {
            foreach ($questions as $question) {
                $questionTitle = $db_conn->real_escape_string($question['title']);
                $questionText = $db_conn->real_escape_string($question['text']);

                $sql = "INSERT INTO assessment_questions (assessment_id, question_title, question_text) 
                        VALUES ('$assessmentId', '$questionTitle', '$questionText')";
                if (!$db_conn->query($sql)) {
                    throw new Exception("Error inserting question: " . $db_conn->error);
                }
                $questionId = $db_conn->insert_id; // Get the inserted question ID

                // Insert options
                if (!empty($question['options'])) {
                    foreach ($question['options'] as $option) {
                        $optionText = $db_conn->real_escape_string($option['text']);

                        $sql = "INSERT INTO assessment_options (assessment_id, assessment_question_id, option_text) 
                                VALUES ('$assessmentId', '$questionId', '$optionText')"; // Include `assessment_id`
                        if (!$db_conn->query($sql)) {
                            throw new Exception("Error inserting option: " . $db_conn->error);
                        }
                    }
                }
            }
        }

        

        // Commit transaction
        $db_conn->commit();

        // Redirect to avoid form resubmission
        header("Location: assessments.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $db_conn->rollback();
        die("Error adding assessment: " . $e->getMessage());
    }
}

// Handle form submission for fetching responses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_responses') {
    $assessmentId = $db_conn->real_escape_string($_POST['assessment_id']);

    // Fetch responses for the given assessment ID
    $sql = "
        SELECT 
            assessment_response_id, 
            assessment_id, 
            student_id, 
            created_at AS date_submitted
        FROM assessment_responses
        WHERE assessment_id = '$assessmentId'
    ";
    $result = $db_conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $responses = [];
        while ($row = $result->fetch_assoc()) {
            $responses[] = $row;
        }
        echo json_encode($responses);
    } else {
        echo json_encode([]);
    }
    exit();
}

// Handle form submission for fetching response details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_response_details') {
    $responseId = $db_conn->real_escape_string($_POST['response_id']);

    $sql = "
        SELECT 
            ar.assessment_response_id,
            a.assessment_title,
            a.assessment_desc,
            q.assessment_question_id,
            q.question_title,
            q.question_text,
            o.option_text AS selected_option
        FROM assessment_responses ar
        JOIN assessments a ON ar.assessment_id = a.assessment_id
        JOIN assessment_answers ans ON ar.assessment_response_id = ans.assessment_response_id
        JOIN assessment_questions q ON ans.assessment_question_id = q.assessment_question_id
        LEFT JOIN assessment_options o ON ans.assessment_option_id = o.assessment_option_id -- Corrected column name
        WHERE ar.assessment_response_id = '$responseId'
    ";
    $result = $db_conn->query($sql);

    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Query failed: ' . $db_conn->error]);
        exit();
    }

    if ($result->num_rows > 0) {
        $responseDetails = [];
        while ($row = $result->fetch_assoc()) {
            $responseDetails[] = [
                'assessment_title' => $row['assessment_title'],
                'assessment_desc' => $row['assessment_desc'],
                'question_title' => $row['question_title'],
                'question_text' => $row['question_text'],
                'selected_option' => $row['selected_option']
            ];
        }
        echo json_encode(['success' => true, 'response_details' => $responseDetails]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No data found for response ID: ' . $responseId]);
    }
    exit();
}

// Close the database connection
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
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/counselor.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Ensure Bootstrap Icons are included -->
</head>
<body>
    <?php
        include(__DIR__ . '/../components/counselor/sidebar.php');
    ?>
    <main>
<!-- BANNER -->
<div class="border-bottom py-3 px-3 banner-surveys_c">
      <h6 class="fw-bold mb-0 btext-surveys_c">Assessments</h6>
      </div>
<!-- TITLE -->
<div class="container pt-2 mt-5">
  <h2 class="text-center fw-bold title-surveys_c">Student Assessment</h2>
      <!-- Button Row -->
<div class="d-flex justify-content-end gap-2 mt-2 mb-0">
  <button class="btn btn-surveys_c btn-primary" data-bs-toggle="modal" data-bs-target="#addNewModal">
    <i class="bi bi-plus-circle"></i> Add New
  </button>
</div>
<!-- TITLE -->
<!-- Student Assessment Table Section -->
<div class="container mt-4">
  <div class="table-pagination-wrapper">
    <div class="table-responsive table-surveys_c">
      <table class="table align-middle mb-0 table-hover">
        <thead class="table-light thead-surveys_c">
          <tr>
            <th class="sticky-top bg-secondary-subtle">Assessment Title</th>
            <th class="sticky-top bg-secondary-subtle">Date of Release</th>
            <th class="sticky-top bg-secondary-subtle">Respondents</th>
            <th class="sticky-top bg-secondary-subtle">Actions</th> 
          </tr>
        </thead>
        <tbody id="surveyTableBody">
  <?php if (!empty($assessments)): ?>
    <?php foreach ($assessments as $assessment): ?>
    <tr>
      <td><?= htmlspecialchars($assessment['assessment_title']) ?></td>
      <td><?= htmlspecialchars(date("F d, Y", strtotime($assessment['created_at']))) ?></td>
      <td><?= htmlspecialchars($assessment['respondent_count']) ?></td>
      <td class="action-icons">
        <i class="bi bi-eye text-primary fs-5 me-2" data-bs-toggle="modal" data-bs-target="#viewModal" data-assessment-id="<?= $assessment['assessment_id'] ?>" title="View"></i>
        <i class="bi bi-trash text-danger fs-5 me-2" data-bs-toggle="modal" data-bs-target="#deleteModal" data-assessment-id="<?= $assessment['assessment_id'] ?>" title="Delete"></i>
        <button class="btn btn-viewsurvey_c btn-viewassess_c btn-sm" data-bs-toggle="modal" data-bs-target="#viewResponsesModal" data-assessment-id="<?= $assessment['assessment_id'] ?>">View Responses</button>
        <button class="btn btn-viewsurvey_c btn-sm archive-btn" data-archived="false">Archive</button>
      </td>
    </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="4" class="text-center text-muted">No assessments available.</td>
    </tr>
  <?php endif; ?>
</tbody>
      </table>
    </div>
  </div>
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
<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header modal-header_ac text-white">
        <h5 class="modal-title title_ac fw-bold" id="viewModalLabel">Assessment Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-body_ac">
        <!-- Title Section -->
        <div class="mb-4">
          <h6 class="fw-bold text-primary">Assessment Title:</h6>
          <p id="viewAssessmentTitle" class="text-secondary"></p>
        </div>
        <!-- Description Section -->
        <div class="mb-4">
          <h6 class="fw-bold text-primary">Description:</h6>
          <p id="viewAssessmentDescription" class="text-secondary"></p>
        </div>
        <!-- Questions Section -->
        <div class="mb-4">
          <h6 class="fw-bold text-primary">Questions:</h6>
          <div id="viewQuestionsList" class="mt-3">
            <!-- Questions will be dynamically inserted here -->
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-viewsurvey_c btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this assessment?
      </div>
      <div class="modal-footer">
        <button class="btn btn-viewsurvey_c btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" id="confirmDeleteRowBtn">Delete</button>
      </div>
    </div>
  </div>
</div>
<!-- View Responses Modal -->
<div class="modal fade" id="viewResponsesModal" tabindex="-1" aria-labelledby="viewResponsesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="viewResponsesModalLabel">Responses for: [Assessment Title]</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Response ID</th>
                <th>Assessment ID</th>
                <th>Student ID</th>
                <th>Date Submitted</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="responseTableBody">
              <!-- Responses will be dynamically loaded here -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-viewsurvey_c btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold" id="archiveModalLabel">Confirm Archive</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-center">Are you sure you want to archive this assessment?</p>
      </div>
      <div class="modal-footer archive_ac">
        <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger w-50" id="confirmArchiveBtn">Archive</button>
      </div>
    </div>
  </div>
</div>
<!-- Add New Modal -->
<div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">
      <form method="POST" action="assessments.php">
        <input type="hidden" name="action" value="add_assessment">
        <input type="hidden" id="questionsInput" name="questions"> <!-- Hidden input for questions JSON -->
        <div class="modal-header header_ac text-white">
          <h5 class="modal-title newmodal_ac fw-bold" id="addNewModalLabel">Add New Assessment</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body body_ac bg-light">
          <!-- Title Input -->
          <div class="mb-4">
            <label for="assessmentTitle" class="form-label fw-bold">Assessment Title:</label>
            <input type="text" class="form-control form-control-lg" id="assessmentTitle" name="title" placeholder="Enter title" required>
          </div>
          <!-- Description Input -->
          <div class="mb-4">
            <label for="assessmentDescription" class="form-label fw-bold">Description:</label>
            <textarea class="form-control form-control-lg" id="assessmentDescription" name="description" rows="3" placeholder="Enter description" required></textarea>
          </div>
          <!-- Questions Container -->
          <div id="addQuestionsContainer">
            <div class="viewsurvey-questions_c mb-4 position-relative p-3 border rounded bg-white">
              <label class="form-label fw-bold">1. Question Title:</label>
              <input type="text" class="form-control mb-2" placeholder="Enter question title">
              <textarea class="form-control mb-2" rows="2" placeholder="Enter full question"></textarea>
              <div class="multiple-choice-options">
                <div class="d-flex align-items-center mb-2">
                  <input type="text" class="form-control me-2" placeholder="Option 1">
                  <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addOption(this)">Add Option</button>
              </div>
              <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="confirmDeleteQuestion(this)">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <!-- Add Question Button -->
          <button type="button" class="btn btn-outline-primary btncolor_ac w-100 mb-3" onclick="addQuestionField()">+ Add Question</button>
        </div>
        <div class="modal-footer footer_ac bg-light">
          <button type="button" id="saveAssessmentBtn" class="btn btn-primary btn-viewsurvey_c w-100">Save Assessment</button>
          <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- View Response Modal -->
<div class="modal fade" id="viewResponseModal" tabindex="-1" aria-labelledby="viewResponseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header text-center d-flex flex-column" style="background-color: #0E58A3; color: #fff;">
        <h5 class="modal-title" id="viewResponseModalLabel">Response Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="fw-bold text-primary">Assessment Title:</h6>
        <p id="responseAssessmentTitle" class="text-secondary"></p>
        <h6 class="fw-bold text-primary">Description:</h6>
        <p id="responseAssessmentDescription" class="text-secondary"></p>
        <h6 class="fw-bold text-primary">Questions and Answers:</h6>
        <div id="responseQuestionsList" class="mt-3">
          <!-- Questions and answers will be dynamically inserted here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    </main>
    <script>
document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.getElementById("surveyTableBody");
  const viewModalElement = document.getElementById("viewModal");
  const viewModal = new bootstrap.Modal(viewModalElement);

  // Open view modal and populate data
  tableBody.addEventListener("click", function (event) {
    if (event.target.classList.contains("bi-eye")) {
      const assessmentId = event.target.getAttribute("data-assessment-id");

      // Fetch assessment details via AJAX
      fetch("assessments.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=fetch_assessment_details&assessment_id=${assessmentId}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Populate modal fields
          document.getElementById("viewAssessmentTitle").textContent = data.title;
          document.getElementById("viewAssessmentDescription").textContent = data.description;

          // Populate questions and options
          const questionListContainer = document.getElementById("viewQuestionsList");
          questionListContainer.innerHTML = ""; // Clear previous content

          data.questions.forEach((question, index) => {
            const questionHTML = `
              <div class="mb-3 border-bottom pb-2">
                <h6 class="text-dark fw-semibold">Q${index + 1}: ${question.title || 'Untitled'}</h6>
                <p class="text-secondary mb-1">${question.text || 'No full question provided'}</p>
                ${question.options && question.options.length > 0 ? `
                  <ul class="list-unstyled ps-3">
                    ${question.options.map(option => `<li>• ${option.text}</li>`).join('')}
                  </ul>
                ` : ''}
              </div>
            `;
            questionListContainer.insertAdjacentHTML('beforeend', questionHTML);
          });

          viewModal.show();
        } else {
          alert("Failed to fetch assessment details.");
        }
      })
      .catch(error => console.error("Error fetching assessment details:", error));
    }
  });

  // Ensure modal is properly hidden and overlay is removed
  viewModalElement.addEventListener("hidden.bs.modal", function () {
    document.body.classList.remove("modal-open");
    const modalBackdrop = document.querySelector(".modal-backdrop");
    if (modalBackdrop) {
      modalBackdrop.remove();
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.getElementById("surveyTableBody");
  const deleteModalInstance = new bootstrap.Modal(document.getElementById("deleteModal"));
  let assessmentToDelete = null;
  // Open delete modal and store the assessment ID
  tableBody.addEventListener("click", function (event) {
    if (event.target.classList.contains("bi-trash")) {
      assessmentToDelete = event.target.closest("tr");
      const assessmentId = event.target.getAttribute("data-assessment-id");
      document.getElementById("confirmDeleteRowBtn").setAttribute("data-assessment-id", assessmentId);
      deleteModalInstance.show();
    }
  });

  // Confirm delete action
  document.getElementById("confirmDeleteRowBtn").addEventListener("click", function () {
    const assessmentId = this.getAttribute("data-assessment-id");
    // Send AJAX request to delete the assessment
    fetch("assessments.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `action=delete_assessment&assessment_id=${assessmentId}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Remove the row from the table
        if (assessmentToDelete) {
          assessmentToDelete.remove();
        }
        deleteModalInstance.hide();
      } else {
        alert("Failed to delete assessment: " + (data.error || "Unknown error"));
      }
    })
    .catch(error => console.error("Error deleting assessment:", error));
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // Add new assessment when save button is clicked
  document.getElementById('saveAssessmentBtn').addEventListener('click', function () {
    const assessmentTitle = document.getElementById("assessmentTitle").value;
    const assessmentDescription = document.getElementById("assessmentDescription").value;
    // Validate inputs
    if (!assessmentTitle || !assessmentDescription) {
      alert("Please fill out all required fields.");
      return;
    }
    // Collect questions and options
    const questions = [];
    const questionElements = document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c");
    
    if (questionElements.length === 0) {
      alert("Please add at least one question.");
      return;
    }
    questionElements.forEach((questionElement) => {
      const questionTitle = questionElement.querySelector("input[type='text']").value;
      const questionText = questionElement.querySelector("textarea").value;
      if (!questionTitle || !questionText) {
        alert("Please fill out all question fields.");
        return;
      }
      
      const options = [];
      const optionElements = questionElement.querySelectorAll(".multiple-choice-options .d-flex");
      optionElements.forEach((optionElement) => {
        const optionText = optionElement.querySelector("input[type='text']").value;
        if (optionText) {
          options.push({ text: optionText });
        }
      });
      
      if (options.length === 0) {
        alert("Please add at least one option for each question.");
        return;
      }
      
      questions.push({ title: questionTitle, text: questionText, options });
    });
    
    // Set the questions JSON to the hidden input
    document.getElementById("questionsInput").value = JSON.stringify(questions);
    
    // Submit the form
    document.querySelector("#addNewModal form").submit();
  });
  
  window.addQuestionField = function () {
    const questionCount = document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c").length + 1;
    const questionDiv = document.createElement("div");
    questionDiv.classList.add("viewsurvey-questions_c", "mb-4", "position-relative", "p-3", "border", "rounded", "bg-white");
    questionDiv.innerHTML = `
      <label class="form-label fw-bold">${questionCount}. Question Title:</label>
      <input type="text" class="form-control mb-2" placeholder="Enter question title">
      <textarea class="form-control mb-2" rows="2" placeholder="Enter full question"></textarea>
      <div class="multiple-choice-options">
        <div class="d-flex align-items-center mb-2">
          <input type="text" class="form-control me-2" placeholder="Option 1">
          <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addOption(this)">Add Option</button>
      </div>
      <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="confirmDeleteQuestion(this)">
        <i class="bi bi-x-lg"></i>
      </button>
    `;
    document.getElementById("addQuestionsContainer").appendChild(questionDiv);
  };
  
  window.addOption = function (button) {
    const optionsContainer = button.closest(".multiple-choice-options");
    const optionDiv = document.createElement("div");
    optionDiv.classList.add("d-flex", "align-items-center", "mb-2");
    optionDiv.innerHTML = `
      <input type="text" class="form-control me-2" placeholder="Option">
      <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">
        <i class="bi bi-x-lg"></i>
      </button>
    `;
    optionsContainer.insertBefore(optionDiv, button);
  };
  
  window.removeOption = function (button) {
    button.closest(".d-flex").remove();
  };
  
  window.confirmDeleteQuestion = function (button) {
    if (document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c").length > 1) {
      button.closest(".viewsurvey-questions_c").remove();
    }
  };
});

document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.getElementById("surveyTableBody");
  const archiveModalInstance = new bootstrap.Modal(document.getElementById("archiveModal"));
  let archiveButton;
  tableBody.addEventListener("click", function (event) {
    if (event.target.classList.contains("archive-btn")) {
      archiveButton = event.target; // Store the clicked button
      const isArchived = archiveButton.getAttribute("data-archived") === "true";

      // Update the modal text based on the action
      const modalTitle = document.getElementById("archiveModalLabel");
      const modalBody = document.querySelector("#archiveModal .modal-body p");
      const confirmButton = document.getElementById("confirmArchiveBtn");

      if (isArchived) {
        modalTitle.textContent = "Confirm Unarchive";
        modalBody.textContent = "Are you sure you want to unarchive this assessment?";
        confirmButton.textContent = "Unarchive";
        confirmButton.classList.remove("btn-danger");
        confirmButton.classList.add("btn-success");
      } else {
        modalTitle.textContent = "Confirm Archive";
        modalBody.textContent = "Are you sure you want to archive this assessment?";
        confirmButton.textContent = "Archive";
        confirmButton.classList.remove("btn-success");
        confirmButton.classList.add("btn-danger");
      }

      // Show the modal
      archiveModalInstance.show();
    }
  });

  document.getElementById("confirmArchiveBtn").addEventListener("click", function () {
    const isArchived = archiveButton.getAttribute("data-archived") === "true";

    if (isArchived) {
      // Unarchive logic
      archiveButton.textContent = "Archive";
      archiveButton.setAttribute("data-archived", "false");
    } else {
      // Archive logic
      archiveButton.textContent = "Unarchive";
      archiveButton.setAttribute("data-archived", "true");
    }

    archiveModalInstance.hide();
  });
});

function populateResponses(assessmentTitle) {
  const modalTitle = document.getElementById("viewResponsesModalLabel");
  const responseTableBody = document.getElementById("responseTableBody");

  // Update the modal title
  modalTitle.textContent = `Responses for: ${assessmentTitle}`;

  // Clear existing rows in the table
  responseTableBody.innerHTML = '';

  // Example data (replace this with actual data from your backend or logic)
  const exampleResponses = [];

  // Check if there are any responses
  if (exampleResponses.length === 0) {
    const emptyRow = document.createElement("tr");
    emptyRow.innerHTML = `
      <td colspan="5" class="text-center text-muted">No responses yet.</td>
    `;
    responseTableBody.appendChild(emptyRow);
  } else {
    exampleResponses.forEach((response) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${response.respondent}</td>
        <td>${response.studentId}</td>
        <td>${response.program}</td>
        <td>${response.yearLevel}</td>
        <td>${response.dateSubmitted}</td>
      `;
      responseTableBody.appendChild(row);
    });
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const viewResponsesModal = new bootstrap.Modal(document.getElementById("viewResponsesModal"));
  const responseTableBody = document.getElementById("responseTableBody");

  document.querySelectorAll("[data-bs-target='#viewResponsesModal']").forEach(button => {
    button.addEventListener("click", function () {
      const assessmentId = this.getAttribute("data-assessment-id");
      const modalTitle = document.getElementById("viewResponsesModalLabel");

      // Update modal title
      modalTitle.textContent = `Responses for: ${this.closest("tr").querySelector("td").textContent}`;

      // Fetch responses via AJAX
      fetch("assessments.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=fetch_responses&assessment_id=${assessmentId}`
      })
      .then(response => response.json())
      .then(data => {
        responseTableBody.innerHTML = ""; // Clear existing rows
        if (data.length > 0) {
          data.forEach(response => {
            const row = document.createElement("tr");
            row.innerHTML = `
              <td>${response.assessment_response_id || 'N/A'}</td>
              <td>${response.assessment_id || 'N/A'}</td>
              <td>${response.student_id || 'N/A'}</td>
              <td>${response.date_submitted || 'N/A'}</td>
              <td>
                <button class="btn btn-primary btn-sm view-response-btn" data-response-id="${response.assessment_response_id}" data-bs-toggle="modal" data-bs-target="#viewResponseModal">View Response</button>
              </td>
            `;
            responseTableBody.appendChild(row);
          });
        } else {
          responseTableBody.innerHTML = `
            <tr>
              <td colspan="5" class="text-center text-muted">No responses yet.</td>
            </tr>
          `;
        }
      })
      .catch(error => console.error("Error fetching responses:", error));
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const saveAssessmentBtn = document.getElementById("saveAssessmentBtn");
  const questionsInput = document.getElementById("questionsInput");

  saveAssessmentBtn.addEventListener("click", function () {
    const questions = [];
    const questionElements = document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c");

    questionElements.forEach((questionElement) => {
      const questionTitle = questionElement.querySelector("input[type='text']").value;
      const questionText = questionElement.querySelector("textarea").value;

      const options = [];
      const optionElements = questionElement.querySelectorAll(".multiple-choice-options .d-flex");
      optionElements.forEach((optionElement) => {
        const optionText = optionElement.querySelector("input[type='text']").value;
        if (optionText) {
          options.push({ text: optionText });
        }
      });

      questions.push({ title: questionTitle, text: questionText, options });
    });

    if (questions.length === 0) {
      alert("Please add at least one question.");
      return;
    }

    questionsInput.value = JSON.stringify(questions); // Set the JSON string to the hidden input
    questionsInput.closest("form").submit(); // Submit the form
  });
});

function renderAssessmentDetails(assessment) {
  document.getElementById('viewAssessmentTitle').textContent = assessment.title;
  document.getElementById('viewAssessmentDescription').textContent = assessment.description;

  const questionListContainer = document.getElementById('viewQuestionsList');
  questionListContainer.innerHTML = '';

  assessment.questions.forEach((q, index) => {
    const questionHTML = `
      <div class="mb-3 border-bottom pb-2">
        <h6 class="text-dark fw-semibold">Q${index + 1}: ${q.title || 'Untitled'}</h6>
        <p class="text-secondary mb-1">${q.text || 'No question text.'}</p>
        ${q.options.length > 0 ? `
          <ul class="list-unstyled ps-3">
            ${q.options.map(opt => `<li>• ${opt.text}</li>`).join('')}
          </ul>
        ` : ''}
      </div>
    `;
    questionListContainer.insertAdjacentHTML('beforeend', questionHTML);
  });
}

document.querySelectorAll('.view-icon').forEach(icon => {
  icon.addEventListener('click', () => {
    const assessmentId = icon.dataset.id;
    fetch('your_php_file.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({
        action: 'fetch_assessment_details',
        assessment_id: assessmentId
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        renderAssessmentDetails(data);
        new bootstrap.Modal(document.getElementById('viewModal')).show();
      } else {
        alert('Assessment not found!');
      }
    })
    .catch(err => console.error('Fetch error:', err));
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const saveAssessmentBtn = document.getElementById("saveAssessmentBtn");
  const questionsInput = document.getElementById("questionsInput");

  saveAssessmentBtn.addEventListener("click", function () {
    const assessmentTitle = document.getElementById("assessmentTitle").value.trim();
    const assessmentDescription = document.getElementById("assessmentDescription").value.trim();

    // Validate inputs
    if (!assessmentTitle || !assessmentDescription) {
      alert("Please fill out all required fields.");
      return;
    }

    // Collect questions and options
    const questions = [];
    const questionElements = document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c");

    if (questionElements.length === 0) {
      alert("Please add at least one question.");
      return;
    }

    questionElements.forEach((questionElement) => {
      const questionTitle = questionElement.querySelector("input[type='text']").value.trim();
      const questionText = questionElement.querySelector("textarea").value.trim();

      if (!questionTitle || !questionText) {
        alert("Please fill out all question fields.");
        return;
      }

      const options = [];
      const optionElements = questionElement.querySelectorAll(".multiple-choice-options .d-flex");

      optionElements.forEach((optionElement) => {
        const optionText = optionElement.querySelector("input[type='text']").value.trim();
        if (optionText) {
          options.push({ text: optionText });
        }
      });

      if (options.length === 0) {
        alert("Please add at least one option for each question.");
        return;
      }

      questions.push({ title: questionTitle, text: questionText, options });
    });

    // Set the questions JSON to the hidden input
    questionsInput.value = JSON.stringify(questions);

    // Submit the form
    questionsInput.closest("form").submit();
  });

  window.addQuestionField = function () {
    const questionCount = document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c").length + 1;
    const questionDiv = document.createElement("div");
    questionDiv.classList.add("viewsurvey-questions_c", "mb-4", "position-relative", "p-3", "border", "rounded", "bg-white");

    questionDiv.innerHTML = `
      <label class="form-label fw-bold">${questionCount}. Question Title:</label>
      <input type="text" class="form-control mb-2" placeholder="Enter question title">
      <textarea class="form-control mb-2" rows="2" placeholder="Enter full question"></textarea>
      <div class="multiple-choice-options">
        <div class="d-flex align-items-center mb-2">
          <input type="text" class="form-control me-2" placeholder="Option 1">
          <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addOption(this)">Add Option</button>
      </div>
      <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="confirmDeleteQuestion(this)">
        <i class="bi bi-x-lg"></i>
      </button>
    `;

    document.getElementById("addQuestionsContainer").appendChild(questionDiv);
  };

  window.addOption = function (button) {
    const optionsContainer = button.closest(".multiple-choice-options");
    const optionDiv = document.createElement("div");
    optionDiv.classList.add("d-flex", "align-items-center", "mb-2");

    optionDiv.innerHTML = `
      <input type="text" class="form-control me-2" placeholder="Option">
      <button type="button" class="btn btn-sm btn-danger" onclick="removeOption(this)">
        <i class="bi bi-x-lg"></i>
      </button>
    `;

    optionsContainer.insertBefore(optionDiv, button);
  };

  window.removeOption = function (button) {
    button.closest(".d-flex").remove();
  };

  window.confirmDeleteQuestion = function (button) {
    if (document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c").length > 1) {
      button.closest(".viewsurvey-questions_c").remove();
    }
  };
});

document.querySelectorAll("[data-bs-target='#viewResponsesModal']").forEach(button => {
    button.addEventListener("click", function () {
        const assessmentId = this.getAttribute("data-assessment-id");
        const modalTitle = document.getElementById("viewResponsesModalLabel");
        const responseTableBody = document.getElementById("responseTableBody");

        // Update modal title
        modalTitle.textContent = `Responses for: ${this.closest("tr").querySelector("td").textContent}`;

        // Fetch responses via AJAX
        fetch("assessments.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=fetch_responses&assessment_id=${assessmentId}`
        })
        .then(response => response.json())
        .then(data => {
            responseTableBody.innerHTML = ""; // Clear existing rows
            if (data.length > 0) {
                data.forEach(response => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${response.assessment_response_id || 'N/A'}</td>
                        <td>${response.assessment_id || 'N/A'}</td>
                        <td>${response.student_id || 'N/A'}</td>
                        <td>${response.date_submitted || 'N/A'}</td>
                        <td>
                            <button class="btn btn-primary btn-sm view-response-btn" data-response-id="${response.assessment_response_id}" data-bs-toggle="modal" data-bs-target="#viewResponseModal">View Response</button>
                        </td>
                    `;
                    responseTableBody.appendChild(row);
                });
            } else {
                responseTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted">No responses yet.</td>
                    </tr>
                `;
            }
        })
        .catch(error => console.error("Error fetching responses:", error));
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const responseTableBody = document.getElementById("responseTableBody");
    const viewResponseModal = new bootstrap.Modal(document.getElementById("viewResponseModal"));

    responseTableBody.addEventListener("click", function (event) {
        if (event.target.classList.contains("view-response-btn")) {
            const responseId = event.target.getAttribute("data-response-id");

            // Fetch response details via AJAX
            fetch("assessments.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=fetch_response_details&response_id=${responseId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate modal fields
                    const responseDetails = data.response_details;
                    document.getElementById("responseAssessmentTitle").textContent = responseDetails[0].assessment_title;
                    document.getElementById("responseAssessmentDescription").textContent = responseDetails[0].assessment_desc;

                    const questionsList = document.getElementById("responseQuestionsList");
                    questionsList.innerHTML = ""; // Clear previous content

                    responseDetails.forEach((detail, index) => {
                        const questionHTML = `
                            <div class="mb-3 border-bottom pb-2">
                                <h6 class="text-dark fw-semibold">Q${index + 1}: ${detail.question_title || 'Untitled'}</h6>
                                <p class="text-secondary mb-1">${detail.question_text || 'No question text provided'}</p>
                                <p class="text-success"><strong>Answer:</strong> ${detail.selected_option || 'No answer provided'}</p>
                            </div>
                        `;
                        questionsList.insertAdjacentHTML('beforeend', questionHTML);
                    });

                    // Show the modal
                    viewResponseModal.show();
                } else {
                    alert("Failed to fetch response details.");
                }
            })
            .catch(error => console.error("Error fetching response details:", error));
        }
    });
});
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>