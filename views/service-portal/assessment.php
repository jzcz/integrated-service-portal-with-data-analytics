<?php
// Database connection
$db_conn = require(__DIR__ . "/../../db/db_conn.php");

if (!$db_conn) {
    error_log("Database connection failed.");
    die("Database connection failed.");
}

// Simulate user role (replace this with actual session or authentication logic)
$user_role = 'counselor'; // Example: 'student' or 'counselor'

// Handle form submission for assessment responses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assessment_id'], $_POST['student_id'], $_POST['answer'])) {
    if ($user_role !== 'student') {
        echo json_encode(['success' => false, 'message' => 'Only students can submit responses.']);
        exit;
    }

    $assessment_id = intval($_POST['assessment_id']);
    $student_id = intval($_POST['student_id']);
    $answer = htmlspecialchars($_POST['answer']);
    $created_at = date('Y-m-d H:i:s');

    // Insert the response into the database
    $sql = "INSERT INTO assessment_responses (assessment_id, student_id, answer, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $db_conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('iiss', $assessment_id, $student_id, $answer, $created_at);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save response.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    }
    $db_conn->close();
    exit;
}

// Fetch active assessments with their questions
$assessments = [];
$sql = "
    SELECT 
        a.assessment_id, 
        a.assessment_title, 
        a.assessment_desc, 
        q.question_title, 
        q.question_text 
    FROM 
        assessments a
    LEFT JOIN 
        assessment_questions q 
    ON 
        a.assessment_id = q.assessment_id
    WHERE 
        a.is_archived = 0 AND 
        a.is_deleted = 0
";
$result = $db_conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $assessments[] = $row;
        }
    } else {
        error_log("No assessments found matching the criteria.");
    }
} else {
    error_log("Query failed: " . $db_conn->error);
}

// Debugging: Log the assessments array
error_log("Assessments: " . print_r($assessments, true));
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
    <link rel="stylesheet" href="../../assets/css/service-portal.css">
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
            <p class="flex-grow-1 assessmentp"><?= htmlspecialchars($assessment['assessment_desc']) ?></p>
            <div class="d-flex justify-content-center mt-auto">
              <button 
                class="btn assessmentbtn btn-primary" 
                data-bs-toggle="modal" 
                data-bs-target="#assessmentModal" 
                data-id="<?= $assessment['assessment_id'] ?>" 
                data-title="<?= htmlspecialchars($assessment['assessment_title']) ?>" 
                data-desc="<?= htmlspecialchars($assessment['assessment_desc']) ?>" 
                data-question-title="<?= htmlspecialchars($assessment['question_title']) ?>" 
                data-question-text="<?= htmlspecialchars($assessment['question_text']) ?>">
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
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-center align-items-center">
        <h5 class="modal-title" id="assessmentModalLabel">Assessment</h5>
        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 id="modalAssessmentTitle"></h6>
        <p id="modalAssessmentDesc"></p>
        <h6 id="modalQuestionTitle" class="mt-3"></h6>
        <p id="modalQuestionText"></p>
        <form id="assessmentForm">
        <div class="mb-3">
        <label class="form-label d-block">Your Answer</label>
        <div class="d-flex justify-content-between text-center">
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="answer" id="answerSD" value="SD" required>
            <label class="form-check-label" for="answerSD">Strongly Disagree</label>
          </div>
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="answer" id="answerD" value="D">
            <label class="form-check-label" for="answerD">Disagree</label>
          </div>
          <div class="form-check me-2">
            <input class="form-check-input" type="radio" name="answer" id="answerA" value="A">
            <label class="form-check-label" for="answerA">Agree</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="answer" id="answerSA" value="SA">
            <label class="form-check-label" for="answerSA">Strongly Agree</label>
          </div>
        </div>
      </div>

          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const assessmentModal = document.getElementById('assessmentModal');
    const modalTitle = document.getElementById('modalAssessmentTitle');
    const modalDesc = document.getElementById('modalAssessmentDesc');
    const modalQuestionTitle = document.getElementById('modalQuestionTitle');
    const modalQuestionText = document.getElementById('modalQuestionText');
    const assessmentForm = document.getElementById('assessmentForm');

    assessmentModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const title = button.getAttribute('data-title');
      const desc = button.getAttribute('data-desc');
      const questionTitle = button.getAttribute('data-question-title');
      const questionText = button.getAttribute('data-question-text');
      const id = button.getAttribute('data-id');

      modalTitle.textContent = title;
      modalDesc.textContent = desc;
      modalQuestionTitle.textContent = `Question Title: ${questionTitle}`;
      modalQuestionText.textContent = `Full Question: ${questionText}`;

      assessmentForm.onsubmit = function (e) {
        e.preventDefault();
        const answer = document.querySelector('input[name="answer"]:checked').value;
        const studentId = 1; // Replace with the actual logged-in student's ID

        // Submit the answer via AJAX
        fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            assessment_id: id,
            student_id: studentId,
            answer: answer
          })
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Assessment submitted successfully!');
              const modalInstance = bootstrap.Modal.getInstance(assessmentModal);
              modalInstance.hide();
            } else {
              alert(data.message || 'Failed to submit assessment.');
            }
          })
          .catch(error => console.error('Error:', error));
      };
    });
  });
</script>

    
    </main>
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