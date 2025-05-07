<?php
// Start output buffering to prevent unexpected output
ob_start();
session_start(); // Ensure session is started

require(__DIR__ . "/../../queries/students.php");
include(__DIR__ . "/../../config/utils.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php-error.log');

// Session check
if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
    header("location: ../service-portal/login.php");
    exit();
}

// Database connection
$db_conn = require(__DIR__ . "/../../db/db_conn.php");

if (!$db_conn) {
    error_log("Database connection failed.");
    die("Database connection failed.");
}

// Get user role
$user_id = $_SESSION['userId'] ?? null;
$user_role = 'guest';

if ($user_id) {
    $query = "SELECT role FROM user WHERE user_id = ?";
    $stmt = $db_conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_role = $user['role'];
    }
    $stmt->close();
}

$_SESSION['user_role'] = $user_role;

// Handle fetching assessment details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_assessment_details') {
    ob_clean();
    $assessment_id = intval($_POST['assessment_id']);

    if (!$assessment_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid assessment ID.']);
        exit;
    }

    $sql = "
        SELECT 
            a.assessment_title, 
            a.assessment_desc, 
            q.assessment_question_id AS question_id,
            COALESCE(q.question_title, 'Untitled') AS question_title, 
            COALESCE(q.question_text, 'No full question provided') AS question_text,
            o.assessment_option_id AS option_id,
            o.option_text
        FROM assessments a
        LEFT JOIN assessment_questions q ON a.assessment_id = q.assessment_id
        LEFT JOIN assessment_options o ON q.assessment_question_id = o.assessment_question_id
        WHERE a.assessment_id = ?
    ";

    $stmt = $db_conn->prepare($sql);
    if (!$stmt) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement.']);
        exit;
    }

    $stmt->bind_param("i", $assessment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to execute query.']);
        $stmt->close();
        exit;
    }

    $questions = [];
    $assessment_title = null;
    $assessment_desc = null;

    while ($row = $result->fetch_assoc()) {
        if ($assessment_title === null && $assessment_desc === null) {
            $assessment_title = $row['assessment_title'];
            $assessment_desc = $row['assessment_desc'];
        }

        $qid = $row['question_id'];

        if (!isset($questions[$qid])) {
            $questions[$qid] = [
                'id' => $qid,
                'title' => $row['question_title'],
                'text' => $row['question_text'],
                'options' => []
            ];
        }

        if ($row['option_id']) {
            $questions[$qid]['options'][] = [
                'id' => $row['option_id'],
                'text' => $row['option_text']
            ];
        }
    }

    $stmt->close();

    ob_clean();
    echo json_encode([
        'success' => !empty($questions),
        'title' => $assessment_title ?? 'No Title',
        'description' => $assessment_desc ?? 'No Description',
        'questions' => array_values($questions)
    ]);
    exit;
}

// Load assessments list
$sql = "SELECT * FROM assessments WHERE is_archived = 0 AND is_deleted = 0";
$result = $db_conn->query($sql);

$assessments = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assessments[] = $row;
    }
}

// Handle submission of assessment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean();
    $postData = json_decode(file_get_contents('php://input'), true);

    if (isset($postData['action']) && $postData['action'] === 'submit_assessment') {
        if ($_SESSION['user_role'] !== 'Student') {
            echo json_encode(['success' => false, 'message' => 'Only students can submit assessments.']);
            exit;
        }

        $assessment_id = intval($postData['assessment_id'] ?? 0);
        $answers = $postData['assessment_answers'] ?? [];
        $student_id = $_SESSION['studentId'];

        if (!$assessment_id || !is_array($answers) || empty($answers)) {
            error_log("Invalid input: assessment_id = $assessment_id, answers = " . json_encode($answers));
            echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
            exit;
        }

        // Insert into assessment_responses
        $stmt = $db_conn->prepare("
            INSERT INTO assessment_responses (assessment_id, student_id, created_at)
            VALUES (?, ?, NOW())
        ");
        if (!$stmt) {
            error_log("SQL Error (prepare response): " . $db_conn->error);
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement for assessment response.']);
            exit;
        }

        $stmt->bind_param("ii", $assessment_id, $student_id);

        if ($stmt->execute()) {
            $response_id = $stmt->insert_id;
            $stmt->close();

            // Insert individual answers
            $stmtAnswers = $db_conn->prepare("
                INSERT INTO assessment_answers (assessment_response_id, assessment_option_id, assessment_question_id, assessment_id)
                VALUES (?, ?, ?, ?)
            ");

            if ($stmtAnswers) {
                foreach ($answers as $key => $value) {
                    $questionId = intval(str_replace('answer_', '', $key));
                    $optionId = intval($value);

                    if (!$questionId || !$optionId) {
                        error_log("Invalid answer data: question_id = $questionId, option_id = $optionId");
                        continue;
                    }

                    $stmtAnswers->bind_param("iiii", $response_id, $optionId, $questionId, $assessment_id);
                    if (!$stmtAnswers->execute()) {
                        error_log("SQL Error (insert answer): " . $db_conn->error);
                    }
                }
                $stmtAnswers->close();
            } else {
                error_log("SQL Error (prepare answers): " . $db_conn->error);
                echo json_encode(['success' => false, 'message' => 'Failed to save answers.']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Assessment submitted successfully.']);
            exit;
        } else {
            error_log("SQL Error (insert response): " . $db_conn->error);
            echo json_encode(['success' => false, 'message' => 'Failed to save assessment response.']);
            exit;
        }
    }
}

ob_end_clean();
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
    <script>
    const userRole = "<?php echo $_SESSION['user_role']; ?>";
    </script>
</head>
<body>
    <?php 
        include(__DIR__ . '/../components/service-portal/navbar.php');
    ?>
    <main>
      <!--- BG IMAGE --->
      <div class="assessment-banner">
      <!--- ASSESSMENTS --->
    <div class="assessmenttitle-text">
      Student Assessment
    </div>
  </div>
      <!--- ASSESSMENTS CARDS --->
      <div class="container my-5">
  <div class="row justify-content-center g-4 mt-4">
    <?php if (!empty($assessments)): ?>
      <?php foreach ($assessments as $assessment): ?>
        <div class="col-md-4 assessmentcol col-sm-6 d-flex justify-content-center h-100">
          <div class="card-assessment text-center h-100 d-flex flex-column p-3">
            <div class="assessmentcard-icon mb-3">
              <img src="../../static/inprogress.png" alt="Assessment Icon">
            </div>
            <p class="flex-grow-1 assessmentp"><?= htmlspecialchars($assessment['assessment_title']) ?></p>
            <div class="d-flex justify-content-center mt-auto">
              <button 
              class="btn assessmentbtn btn-primary" 
              data-bs-toggle="modal" 
              data-bs-target="#assessmentModal" 
              data-id="<?= $assessment['assessment_id'] ?>">
              Start
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-muted">No assessments available at the moment.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="assessmentModal" tabindex="-1" aria-labelledby="assessmentModalLabel" aria-hidden="true">
  <div class="modal-dialog assess_dialog modal-dialog-centered modal-md" style="max-width: 600px; margin: auto"> 
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0E58A3; color: #fff;"> 
        <h5 class="modal-title" id="assessmentModalLabel">Student Assessment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-4">
          <h6 class="custom-blue fw-bold">Assessment Title:</h6>
          <p id="modalAssessmentTitle" class="text-dark fs-5 fw-semibold"></p>
        </div>
        <div class="mb-4">
          <h6 class="custom-blue fw-bold">Description:</h6>
          <p id="modalAssessmentDesc" class="text-dark"></p>
        </div>
        <form id="assessmentForm">
          <input type="hidden" id="hiddenAssessmentId" name="assessment_id" />
          <div id="modalQuestionsContainer" class="p-3 border rounded bg-light"></div>
          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-success px-4">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const userRole = "<?php echo $_SESSION['user_role'] ?? 'guest'; ?>";
  const assessmentModalElement = document.getElementById("assessmentModal");
  const assessmentModal = new bootstrap.Modal(assessmentModalElement);
  const modalTitle = document.getElementById("modalAssessmentTitle");
  const modalDesc = document.getElementById("modalAssessmentDesc");
  const modalQuestionsContainer = document.getElementById("modalQuestionsContainer");
  const assessmentForm = document.getElementById("assessmentForm");
  const hiddenAssessmentId = document.getElementById("hiddenAssessmentId");

  document.addEventListener("click", function (event) {
    if (event.target.classList.contains("assessmentbtn")) {
      if (!["Student", "Counselor", "Admin"].includes(userRole)) {
        alert("You do not have permission to view this assessment.");
        return;
      }

      const assessmentId = event.target.getAttribute("data-id");
      hiddenAssessmentId.value = assessmentId;

      fetch(window.location.href, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=fetch_assessment_details&assessment_id=${assessmentId}`
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            modalTitle.textContent = data.title || "No Title";
            modalDesc.textContent = data.description || "No Description";
            modalQuestionsContainer.innerHTML = "";

            if (userRole === "Student") {
              data.questions.forEach((question, index) => {
                const questionHTML = `
                  <div class="mb-4">
                    <h6 class="text-dark fw-semibold">Q${index + 1}: ${question.title}</h6>
                    <p class="text-secondary mb-2">${question.text}</p>
                    ${question.options && question.options.length > 0 ? `
                      <div>
                        ${question.options.map(option => `
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="answer_${question.id}" id="option_${option.id}" value="${option.id}" required>
                            <label class="form-check-label" for="option_${option.id}">${option.text}</label>
                          </div>
                        `).join('')}
                      </div>
                    ` : '<p class="text-muted">No options available.</p>'}
                  </div>
                `;
                modalQuestionsContainer.insertAdjacentHTML("beforeend", questionHTML);
              });
            } else if (userRole === "Counselor") {
              modalQuestionsContainer.innerHTML = `<p class="text-muted">Counselors can view assessment details but cannot submit responses.</p>`;
            } else if (userRole === "Admin") {
              modalQuestionsContainer.innerHTML = `<p class="text-muted">Admins can manage assessments but cannot submit responses.</p>`;
            }

            assessmentModal.show();
          } else {
            alert(data.message || "Failed to fetch assessment details.");
          }
        })
        .catch(error => console.error("Error fetching assessment details:", error));
    }
  });

  // Student submission logic
  if (userRole === "Student") {
    assessmentForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(assessmentForm);
      const assessmentId = hiddenAssessmentId.value;
      const answers = {};

      for (const [key, value] of formData.entries()) {
        if (key.startsWith("answer_")) {
          answers[key] = parseInt(value);
        }
      }

      fetch(window.location.href, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "submit_assessment",
          assessment_id: parseInt(assessmentId),
          assessment_answers: answers
        }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message || "Assessment submitted successfully.");
          assessmentModal.hide(); 
        } else {
          alert(data.message || "Failed to submit assessment.");
        }
      })
      .catch(error => {
        console.error("Error submitting assessment:", error);
        alert("An error occurred while submitting the assessment.");
      });
    });
  }
});
</script>

<!--- ACTIVE PAGE HIGHLIGHT --->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebarNavItems = document.querySelectorAll('.nav-item.sidebar-nav-item');
  sidebarNavItems.forEach(item => {
    const link = item.querySelector('a');
    if (link && link.textContent.trim() === 'Assessments') {
      item.classList.add('active');
    }
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>