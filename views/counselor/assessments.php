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
        <h5 class="modal-title title_ac fw-bold" id="viewModalLabel">Student Assessment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-body_ac">
        <!-- Description Section -->
        <div class="mb-4">
          <h6 class="text-muted fw-bold">Description:</h6>
          <p class="descrip_ac text-secondary" id="viewInstruction"></p>
        </div>

        <!-- Questions Section -->
        <div id="viewQuestionsContainer" class="viewsurvey-questions_c">
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
                <th>Respondent</th>
                <th>Student ID</th>
                <th>Program</th>
                <th>Current Year Level</th>
                <th>Date Submitted</th>
              </tr>
            </thead>
            <tbody id="responseTableBody">
              <tr>
                <td>John Doe</td>
                <td>2021-00123</td>
                <td>BSIT</td>
                <td>3rd Year</td>
                <td>April 25, 2025</td>
              </tr>
              
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
      <!-- Modal Header -->
      <div class="modal-header header_ac text-white">
        <h5 class="modal-title newmodal_ac fw-bold" id="addNewModalLabel">Add New Assessment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body body_ac bg-light">
        <!-- Title Input -->
        <div class="mb-4">
          <label for="assessmentTitle" class="form-label fw-bold">Assessment Title:</label>
          <input type="text" class="form-control form-control-lg" id="assessmentTitle" placeholder="Enter title">
        </div>

        <!-- Description Input -->
        <div class="mb-4">
          <label for="assessmentDescription" class="form-label fw-bold">Description:</label>
          <textarea class="form-control form-control-lg" id="assessmentDescription" rows="3" placeholder="Enter description"></textarea>
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

      <!-- Modal Footer -->
      <div class="modal-footer footer_ac bg-light">
        <button id="saveAssessmentBtn" class="btn btn-primary btn-viewsurvey_c w-100">Save Assessment</button>
        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

    </main>
    <script>
document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.getElementById("surveyTableBody");
  const deleteModalInstance = new bootstrap.Modal(document.getElementById("deleteModal"));

  tableBody.addEventListener("click", function (event) {
    if (event.target.classList.contains("bi-trash")) {
      const rowToDelete = event.target.closest("tr");
      confirmRowDelete(rowToDelete);
    }
  });

  function confirmRowDelete(rowToDelete) {
    deleteModalInstance.show();

    document.getElementById("confirmDeleteRowBtn").onclick = function () {
      rowToDelete.remove();
      deleteModalInstance.hide();
    };
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.getElementById("surveyTableBody");
  const viewModal = new bootstrap.Modal(document.getElementById("viewModal"));

  // Add new assessment when save button is clicked
  document.getElementById('saveAssessmentBtn').addEventListener('click', saveAssessment);

  function saveAssessment() {
  const assessmentTitle = document.getElementById("assessmentTitle").value;
  const assessmentDescription = document.getElementById("assessmentDescription").value;
  const date = new Date().toLocaleDateString();
  const respondents = 0; 

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
        options.push(optionText);
      }
    });

    questions.push({ title: questionTitle, text: questionText, options });
  });

  if (assessmentTitle && assessmentDescription && questions.length > 0) {
    const newRow = document.createElement("tr");
    newRow.innerHTML = `
      <td>${assessmentTitle}</td>
      <td>${date}</td>
      <td>${respondents}</td> <!-- Set respondents to 0 -->
      <td class="action-icons">
        <i class="bi bi-eye text-secondary fs-5 view-icon" 
          data-title="${assessmentTitle}" 
          data-description="${assessmentDescription}" 
          data-questions='${JSON.stringify(questions)}'></i>
        <i class="bi bi-trash bold-icon text-secondary fs-5" 
          onclick="confirmRowDelete(this)" data-bs-toggle="modal" data-bs-target="#deleteModal"></i>
        <button class="btn btn-viewsurvey_c btn-viewassess_c btn-sm" 
          data-bs-toggle="modal" 
          data-bs-target="#viewResponsesModal" 
          onclick="populateResponses('${assessmentTitle}')">View Responses</button>
        <button class="btn btn-viewsurvey_c btn-sm archive-btn" data-archived="false">Archive</button>
      </td>
    `;

    tableBody.appendChild(newRow);

    // Close modal after saving
    const modal = bootstrap.Modal.getInstance(document.getElementById('addNewModal'));
    modal.hide();

    // Clear input fields
    document.getElementById("assessmentTitle").value = '';
    document.getElementById("assessmentDescription").value = '';
    document.getElementById("addQuestionsContainer").innerHTML = `
      <div class="viewsurvey-questions_c mb-4 position-relative">
        <label class="form-label"><strong>1. Question Title:</strong></label>
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
    `;
  } else {
    alert("Please fill out all fields.");
  }
}

  tableBody.addEventListener("click", function (event) {
    if (event.target.classList.contains("view-icon")) {
      const title = event.target.getAttribute("data-title");
      const description = event.target.getAttribute("data-description");
      const questions = JSON.parse(event.target.getAttribute("data-questions"));

      viewAssessment(title, description, questions);
    }
  });

  // Function to view assessment
  function viewAssessment(title, description, questions) {
  document.getElementById("viewModalLabel").textContent = title;

  const descriptionElement = document.getElementById("viewInstruction");
  descriptionElement.textContent = description; 

  // Clear existing questions
  const questionsContainer = document.getElementById("viewQuestionsContainer");
  questionsContainer.innerHTML = '';

  questions.forEach((question, index) => {
    const questionRow = document.createElement("div");
    questionRow.classList.add("row", "mb-4");

    const questionContent = `
      <div class="col-md-6">
        <div><strong>${index + 1}. ${question.title}:</strong></div>
        <div>${question.text}</div>
      </div>
    `;

    let optionsHTML = `<div class="col-md-4">`;
    question.options.forEach((option, optionIndex) => {
      optionsHTML += `
        <div class="form-check">
          <input class="form-check-input" type="radio" name="question-${index}" id="question-${index}-option-${optionIndex}">
          <label class="form-check-label" for="question-${index}-option-${optionIndex}">
            ${option}
          </label>
        </div>
      `;
    });
    optionsHTML += `</div>`;

    questionRow.innerHTML = questionContent + optionsHTML;

    questionsContainer.appendChild(questionRow);
  });

  const viewModal = new bootstrap.Modal(document.getElementById("viewModal"));
  viewModal.show();
}

  window.addQuestionField = function () {
    const questionCount = document.querySelectorAll("#addQuestionsContainer .viewsurvey-questions_c").length + 1;
    const questionDiv = document.createElement("div");
    questionDiv.classList.add("viewsurvey-questions_c", "mb-4", "position-relative");

    questionDiv.innerHTML = `
      <label class="form-label"><strong>${questionCount}. Question Title:</strong></label>
      <input type="text" class="form-control mb-2" placeholder="Enter question title">
      <textarea type="text" class="form-control mb-2" rows="2" placeholder="Enter full question"></textarea>
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
    button.closest(".viewsurvey-questions_c").remove();
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

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>