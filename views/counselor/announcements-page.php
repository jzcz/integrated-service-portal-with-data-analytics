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
    <style>
        body {
    margin: 0;
    padding: 0;
    overflow: auto !important;
    background-color: white;
    overflow-x: hidden;
    min-height: 100vh;
}

html,
body {
    height: 100%;
    overflow: hidden;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

.container-fluid {
    height: 100vh;
    display: flex;
    padding: 0;
    padding-left: 0;
    overflow-x: hidden;
    margin: 0;
}

.row {
    display: flex;
    height: 100%;
    flex-grow: 1;
}

    </style>
</head>
<body>
    <?php
        include(__DIR__ . '/../components/counselor/sidebar.php');
    ?>

<main class="mt-0">
  <div class="container p-0">
    <div class="announcement-header">
      <div class="image-wrapper">
        <img src="../../static/qcu acad.bldg.png" alt="Announcements Header" class="img-fluid">
      </div>
      <h1 class="text-center pt-5" style="color: #0E58A3;">Announcements</h1>
      <button type="button" class="add-new-btn" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
        <img src="../../static/plus-circle.png" alt="Add" class="icon Add-icon">Add New
      </button>
    </div>

    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header text-white">
            <h5 class="modal-title w-100 text-center" id="addAnnouncementModalLabel">Create Announcement</h5>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="announcementTitle" class="form-label"><strong>Title</strong></label>
                <input type="text" class="form-control" id="announcementTitle" placeholder="Enter title">
              </div>
              <div class="mb-3">
                <label for="announcementDescription" class="form-label"><strong>Description</strong></label>
                <textarea class="form-control" id="announcementDescription" rows="3" placeholder="Enter description"></textarea>
              </div>
              <div class="mb-3">
                <label for="announcementImage" class="form-label"><strong>Cover Image</strong>
                  <span class="span-text text-muted">(Choose or drag an image to upload)</span>
                </label>
                <div class="border border-secondary border-dashed rounded p-4 text-center bg-light" style="cursor: pointer;" onclick="document.getElementById('announcementImage').click()">
                  <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                  <p class="mb-0 text-muted">Click or drag an image to upload</p>
                </div>
                <input type="file" class="form-control d-none" id="announcementImage">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success">Save</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card-container pt-3">
      <div class="card announcement-card shadow-sm" data-announcement-id="1" data-title="Quezon City Scholarship Program (QCYDO) Onsite Application Assistance & Renewal Enlistment" data-description="Good news! The Quezon City Youth Development Office will go to the Main Campus of Quezon City University at San Bartolome, Novaliches to conduct onsite assistance to QCU Students. This will be held on April 2-3, 2024, from 8:00AM to 5:00PM at the QCU, San Bartolome Campus, TechVoc GYM. Additionally, please note that..." data-image="../../static/announ1.jpg">
        <div class="row g-0">
          <div class="col-md-4">
            <img src="../../static/announ1.jpg" class="card-img announcement-image rounded" alt="Scholarship">
          </div>
          <div class="col-md-8">
            <div class="card-body text-center">
              <div class="card-icons d-flex justify-content-end pb-3">
                <img src="../../static/image.png" alt="Delete" class="icon delete-icon" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                <img src="../../static/image 11.png" alt="Edit" class="icon edit-icon" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal">
              </div>
              <h5 class="card-title text-small">
                <u>Quezon City Scholarship Program (QCYDO) Onsite Application Assistance & Renewal Enlistment</u>
              </h5>
              <h6 class="card-subtitle mb-2 text-muted small">March 29, 2024</h6>
              <p class="card-text text-muted small">Good news! The Quezon City Youth Development Office will go to the Main Campus of Quezon City University at San Bartolome, Novaliches to conduct onsite assistance to QCU Students. This will be held on <b>April 2-3, 2024,</b> from 8:00AM to 5:00PM at the <b><i>QCU, San Bartolome Campus, TechVoc GYM.</i></b> Additionally, please note that...</p>
              <a href="#" class="btn readmore-btn btn-primary btn-sm">READ MORE</a>
            </div>
          </div>
        </div>
      </div>

      <div class="card announcement-card shadow-sm" data-announcement-id="2" data-title="Mental Health Check-in for QC Students" data-description="FOR: All Year Levels The Quezon City University (QCU) Guidance Unit invites all students to participate in a quick and confidential Mental Health Check-in. This initiative aims to provide psychological support to students experiencing mental health issues or emotional distress..." data-image="../../static/announ2.jpg">
        <div class="row g-0">
          <div class="col-md-4">
            <img src="../../static/announ2.jpg" class="card-img announcement-image rounded" alt="Mental Health">
          </div>
          <div class="col-md-8">
            <div class="card-body text-center">
              <div class="card-icons d-flex justify-content-end pb-3">
                <img src="../../static/image.png" alt="Delete" class="icon delete-icon" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                <img src="../../static/image 11.png" alt="Edit" class="icon edit-icon" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal">
              </div>
              <h5 class="card-title text-small"><u>Mental Health Check-in for QC Students</u></h5>
              <h6 class="card-subtitle mb-2 text-muted small">January 30, 2025</h6>
              <p class="card-text text-muted small">FOR: All Year Levels<br>The Quezon City University (QCU) Guidance Unit invites all students to participate in a quick and confidential Mental Health Check-in. This initiative aims to provide psychological support to students experiencing mental health issues or emotional distress...</p>
              <a href="#" class="btn readmore-btn btn-primary btn-sm">READ MORE</a>
            </div>
          </div>
        </div>
      </div>

      <div class="card announcement-card shadow-sm" data-announcement-id="3" data-title="QCU Guidance Office Launches Online Psychological Testing for 1st and 2nd Year Students (AY 2024-2025) on All Campuses" data-description="FOR: All 1st Year and 2nd Year QCians on all campuses are required to take the psychological test. The Guidance & Counseling Unit of Quezon City University (QCU) is launching the Online Psychological Testing for all 1st and 2nd-year QCians across all campuses, scheduled..." data-image="../../static/announ3.jpg">
        <div class="row g-0">
          <div class="col-md-4">
            <img src="../../static/announ3.jpg" class="card-img announcement-image rounded" alt="Psychological Testing">
          </div>
          <div class="col-md-8">
            <div class="card-body text-center">
              <div class="card-icons d-flex justify-content-end pb-3">
                <img src="../../static/image.png" alt="Delete" class="icon delete-icon" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                <img src="../../static/image 11.png" alt="Edit" class="icon edit-icon" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal">
              </div>
              <h5 class="card-title"><u>QCU Guidance Office Launches Online Psychological Testing for 1st and 2nd Year Students (AY 2024-2025) on All Campuses</u></h5>
              <h6 class="card-subtitle mb-2 text-muted small">December 11, 2024</h6>
              <p class="card-text text-muted small">FOR: All 1st Year and 2nd Year QCians on all campuses are required to take the psychological test.<br>The Guidance & Counseling Unit of Quezon City University (QCU) is launching the Online Psychological Testing for all 1st and 2nd-year QCians across all campuses, scheduled...</p>
              <a href="#" class="btn readmore-btn btn-primary btn-sm">READ MORE</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-white">
            <h5 class="modal-title fw-bold" id="deleteConfirmationModalLabel">Confirm Deletion?</h5>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the announcement?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header text-white">
            <h5 class="modal-title w-100 text-center" id="editAnnouncementModalLabel">Edit Announcement</h5>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="editAnnouncementTitle" class="form-label"><strong>Title</strong></label>
                <input type="text" class="form-control text-muted" id="editAnnouncementTitle">
              </div>
              <div class="mb-3">
                <label for="editAnnouncementDescription" class="form-label"><strong>Description</strong></label>
                <textarea class="form-control text-muted" id="editAnnouncementDescription" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="announcementImage" class="form-label"><strong>Cover Image</strong>
                  <span class="span-text text-muted">(Choose or drag an image to upload)</span>
                </label>
                <div class="border rounded px-3 py-2 d-flex align-items-center gap-2" style="height: 45px;">
                  <i class="bi bi-image text-muted" style="font-size: 1.2rem;"></i>
                  <span id="currentFileName">No file selected</span>
                </div>
                <input type="file" class="form-control mt-2 d-none" id="announcementImage" name="coverImage">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="saveChangesButton">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>


<script>
    const editModal = document.getElementById('editAnnouncementModal');
    const editTitleInput = document.getElementById('editAnnouncementTitle');
    const editDescriptionTextarea = document.getElementById('editAnnouncementDescription');
    const editImageInput = document.getElementById('editAnnouncementImage');
    const currentFileNameSpan = document.getElementById('currentFileName'); 
    const saveChangesButton = document.getElementById('saveChangesButton');

    let currentAnnouncementId = null;

    const editIcons = document.querySelectorAll('.edit-icon');

    editIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            const card = icon.closest('.announcement-card');
            const title = card.dataset.title;
            const description = card.dataset.description;
            const image = card.dataset.image;
            const announcementId = card.dataset.announcementId;

            editTitleInput.value = title;
            editDescriptionTextarea.value = description;

            if (image && currentFileNameSpan) {
                const fileName = image.split('/').pop();
                currentFileNameSpan.textContent = fileName;
            } else if (currentFileNameSpan) {
                currentFileNameSpan.textContent = "No file selected";
            }

            currentAnnouncementId = announcementId;
        });
    });

    saveChangesButton.addEventListener('click', () => {
        if (currentAnnouncementId) {
            const updatedTitle = editTitleInput.value;
            const updatedDescription = editDescriptionTextarea.value;

            console.log("Saving changes for announcement:", currentAnnouncementId);
            console.log("New title:", updatedTitle);
            console.log("New description:", updatedDescription);

            $(editModal).modal('hide');

            updateCardDisplay(currentAnnouncementId, updatedTitle, updatedDescription);

            currentAnnouncementId = null;
        } else {
            console.warn("No announcement ID to save.");
        }
    });

    function updateCardDisplay(announcementId, title, description) {
        const card = document.querySelector(`.announcement-card[data-announcement-id="${announcementId}"]`);
        if (card) {
            card.querySelector('.card-title u').textContent = title;
            card.querySelector('.card-text').textContent = description;
        }
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>