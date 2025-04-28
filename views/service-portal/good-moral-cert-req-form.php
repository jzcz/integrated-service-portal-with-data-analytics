<?php 
    session_start();

    require(__DIR__ . "/../../queries/students.php");
    include(__DIR__ . "/../../config/utils.php");
    $db_conn = require(__DIR__."/../../db/db_conn.php");
    
    // check session first exists first
    if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
        header("location: ../service-portal/login.php");
        exit();
    }

    $programs = getAllPrograms($db_conn);

    // All school year options from 1994-2024
$schoolYears = [];
for ($y = 1994; $y <= 2024; $y++) {
    $schoolYears[] = "$y-" . ($y + 1);
}

$enrollmentStatus = [
    "I am a BACHELOR Degree Student of this university",
    "I was a SENIOR HIGH SCHOOL Graduate/Undergraduate of this university.",
    "I was a TECHNICAL-VOCATIONAL Course Graduate/Undergraduate of this university.",
];

$reasons = [
    "Scholarship or Financial Assistance",
    "Enrollment or Transfer to other school",
    "Work/Employment",
    "Masteral/Graduate Studies",
    "PNP Application",
    "On-the-Job application or Internship (OJT)",
    "Application for 2nd course (For graduates only)"
];

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
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: auto !important;
            background-color: white;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }
    </style>
</head>
<body>
<?php
        include(__DIR__ . '/../components/service-portal/navbar.php');
    ?>
  <main>
  <?php
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    if (isset($_GET['err'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['err']) . '</div>';
    }
    if (isset($_GET['error_noimage'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error_noimage']) . '</div>';
    }
    if (isset($_GET['error_uploading'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error_uploading']) . '</div>';
    }
    ?>
  <form id="gmcRequestForm" action="../../queries/process-gmcr.php" method="POST">
        <div class="p-5 pt-1">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name"
                        style="width: 100%;">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middleName" name="middleName" placeholder="Middle Name"
                        style="width: 100%;">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name"
                        style="width: 100%;">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="suffix" class="form-label">Suffix</label>
                    <input type="text" class="form-control" id="suffix" name="suffix" placeholder="Suffix" style="width: 100%;">
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="studentNo" class="form-label">Student No.</label>
                    <input type="text" class="form-control" id="studentNo" name="studentNo" placeholder="Ex: 22-0000"
                        style="width: 100%;">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                        style="width: 100%;">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="contact_no" class="form-label">Contact No.</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact No."
                        style="width: 100%;">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="program" class="form-label">Program</label>
                    <select name="program" class="required form-select text-muted" id="program" value="">
                        <option selected disabled>Program</option>
                        <?php foreach ($programs as $program) { ?>
                        <option value="<?php echo htmlspecialchars($program['program_id']); ?>">
                            <?php echo htmlspecialchars($program['program_name']); ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="enrollmentStatus" class="form-label">Enrollment Status</label>
                    <select name="enrollmentStatus" class="required form-select text-muted" id="enrollmentStatus" value="" style="width: 100%;">
                        <option selected disabled>Enrollment Status</option>
                        <?php foreach($enrollmentStatus as $e) { ?>
                        <option value="<?php echo htmlspecialchars($e); ?>"><?php echo htmlspecialchars($e); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="startingSchoolYear" class="form-label">Starting School Year</label>
                    <select name="startSchoolYear" class="required form-select text-muted" id="startSchoolYear" value="" style="width: 100%;">
                        <option selected disabled>Start School Year</option>
                        <?php foreach($schoolYears as $s) { ?>
                        <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="lastSchoolYear" class="form-label">Last School Year</label>
                    <select name="endSchoolYear" class="required form-select text-muted" id="endSchoolYear" value="" style="width: 100%;">
                        <option selected disabled>End School Year</option>
                        <?php foreach($schoolYears as $s) { ?>
                        <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="lastSemesterAttended" class="form-label">Last Semester Attended</label>
                    <select name="lastSemester" class="required form-select text-muted" id="lastSemester" value="" style="width: 100%;">
                        <option selected disabled>Last Semester</option>
                        <option value="1st">1st</option>
                        <option value="2nd">2nd</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="reasonForRequest" class="form-label">Reason for Request</label>
                <select name="reason" class="required form-select text-muted" id="reason" value="">
                    <option selected disabled>Select Reason</option>
                    <?php foreach($reasons as $r) { ?>
                    <option value="<?php echo htmlspecialchars($r); ?>"><?php echo htmlspecialchars($r); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3 specify-reason-row">
                <label for="specifyReason" class="form-label text-muted">
                    *Specify if reason is not included in the options</label>
                <textarea class="form-control" id="specifyReason" name="specifyReason" rows="3" placeholder="Specify reason"></textarea>
            </div>

            <div class="mb-3">
                <label for="proofOfImage" class="form-label">Proof of Image</label>
                <input type="file" class="form-control" id="proofOfImage" name="proofOfImage" accept="image/*">
                <p class="text-secondary">Please upload an image that verifies you are a current or former QCU
                    student (Student ID, Registration Form, or any valid ID).
                </p>

                <div class="image-upload-area">
                    <div class="upload-icon">
                        <i class="bi bi-cloud-upload"></i>
                    </div>
                    <p class="upload-text">Upload an image</p>
                    <div id="uploadedImageName" class="mt-2 text-muted" style="display: none;"></div>
                    <div id="imagePreviewContainer" class="mt-3" style="display: none;">
                        <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>

            <div class="form-check text-secondary mt-3">
                <input class="form-check-input" type="checkbox" value="" id="consentCheckbox">
                <label class="form-check-label" for="consentCheckbox">
                    I agree to voluntarily consent to the collection, processing, and use of my personal information as
                    outlined in the
                    <a href="#" target="_blank">Data Privacy Policy</a> by the QCU Guidance & Counseling Unit.
                </label>
            </div>
            <div class="text-left pb-5 mt-4">
                <button type="submit" class="btn btn-primary custom-submit-button">Submit</button>
            </div>
        </div>
    </form>
    <footer>
        <div class="form-title custom-footer-color text-white pb-3 pt-3">
            <p class="text-center mb-0">© 2024 QCU Guidance and Counseling. All rights reserved.</p>
        </div>
    </footer>
    </main>

    <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel">Request Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('gmcRequestForm');
            const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
            const modalBody = document.getElementById('modalBody');
            const responseModalLabel = document.getElementById('responseModalLabel');

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    modalBody.textContent = data.message;
                    if (data.status === 'success') {
                        responseModalLabel.textContent = 'Success!';
                        // Optionally, you can change the modal's appearance for success
                    } else if (data.status === 'error') {
                        responseModalLabel.textContent = 'Error!';
                        // Optionally, you can change the modal's appearance for error
                    }
                    responseModal.show();
                    if (data.status === 'success') {
                        form.reset(); // Optionally clear the form after success
                        // You might also want to redirect after a short delay if preferred
                        // setTimeout(() => { window.location.href = 'some_success_page.php'; }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    modalBody.textContent = 'An unexpected error occurred. Please try again.';
                    responseModalLabel.textContent = 'Error!';
                    responseModal.show();
                });
            });

            const fileInput = document.getElementById('proofOfImage');
            const uploadArea = document.querySelector('.image-upload-area');
            const uploadText = document.querySelector('.upload-text');
            const uploadedImageName = document.getElementById('uploadedImageName');
            const uploadIcon = document.querySelector('.upload-icon');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imagePreview = document.getElementById('imagePreview');

            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileName = file.name;

                    uploadedImageName.textContent = `Selected file: ${fileName}`;
                    uploadedImageName.style.display = 'block';
                    uploadText.style.display = 'none';
                    uploadIcon.style.display = 'none';
                    uploadArea.style.borderColor = '#28a745';

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    uploadedImageName.textContent = '';
                    uploadedImageName.style.display = 'none';
                    imagePreview.src = '#';
                    imagePreviewContainer.style.display = 'none';
                    uploadText.style.display = 'block';
                    uploadIcon.style.display = 'block';
                    uploadArea.style.borderColor = '#ccc';
                }
            });

            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html> 