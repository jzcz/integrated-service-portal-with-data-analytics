<?php
    session_start();

        // check session first exists first
    if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
        header("location: ../public/counselor-admin-login-page.php");
        exit();
    }
    
    include(__DIR__ . "/../../config/utils.php");
    $db_conn = require(__DIR__ . "/../../db/db_conn.php");

    // Modified SQL query to exclude archived announcements
    $sql = "SELECT * FROM announcements WHERE is_archived = FALSE ORDER BY created_at DESC";
    $result = $db_conn->query($sql);

    if (!$result) {
        error_log("Error executing announcement query: " . $db_conn->error);
        // Optionally display an error message to the user if needed
    }

    $success = "";
    $error = "";

    if (isset($_GET['success'])) {
        $success = $_GET['success'];
    }

    if (isset($_GET['error'])) {
        $error = $_GET['error'];
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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

    <?php if (!empty($success)): ?>
        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title w-100 text-center" id="addAnnouncementModalLabel">Create Announcement</h5>
                </div>
                <div class="modal-body">
                    <form action="../../queries/process-announcements.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="announcementTitle" class="form-label"><strong>Title</strong></label>
                            <input type="text" class="form-control" id="announcementTitle" name="title" placeholder="Enter title" required>
                        </div>
                        <div class="mb-3">
                            <label for="announcementDescription" class="form-label"><strong>Description</strong></label>
                            <textarea class="form-control" id="announcementDescription" name="description" rows="3" placeholder="Enter description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="announcementImage" class="form-label"><strong>Cover Image</strong>
                                <span class="span-text text-muted">(Choose or drag an image to upload)</span>
                            </label>
                            <div class="border border-secondary border-dashed rounded p-4 text-center bg-light" style="cursor: pointer;" onclick="document.getElementById('announcementImage').click()">
                                <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                                <p class="mb-0 text-muted">Click or drag an image to upload</p>
                            </div>
                            <input type="file" class="form-control d-none" id="announcementImage" name="image">
                            <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 100%; height: auto;">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Save Announcement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card-container pt-3">
        <?php
            // Database connection (ensure you have included your db_conn.php)
            $db_conn = require(__DIR__ . "/../../db/db_conn.php");

            // SQL query to fetch all announcements, excluding archived ones, ordered by creation date (newest first)
            $sql = "SELECT * FROM announcements WHERE is_archived = FALSE ORDER BY created_at DESC";
            $result = $db_conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card announcement-card shadow-sm" data-announcement-id="' . htmlspecialchars($row['announcement_id']) . '" data-title="' . htmlspecialchars($row['title']) . '" data-description="' . htmlspecialchars($row['description']) . '" data-image="' . htmlspecialchars($row['img_url']) . '">';
                    echo '  <div class="row g-0">';
                    echo '    <div class="col-md-4">';
                    echo '      <img src="' . htmlspecialchars($row['img_url']) . '" class="card-img announcement-image rounded" alt="' . htmlspecialchars($row['title']) . '">';
                    echo '    </div>';
                    echo '    <div class="col-md-8">';
                    echo '      <div class="card-body text-center">';
                    echo '        <div class="card-icons d-flex justify-content-end pb-3">';
                    echo '          <img src="../../static/image.png" alt="Delete" class="icon delete-icon" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">';
                    echo '          <img src="../../static/image 11.png" alt="Edit" class="icon edit-icon" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal">';
                    echo '        </div>';
                    echo '        <h5 class="card-title text-small">';
                    echo '          <u>' . htmlspecialchars($row['title']) . '</u>';
                    echo '        </h5>';
                    echo '        <h6 class="card-subtitle mb-2 text-muted small">' . htmlspecialchars($row['created_at']) . '</h6>';
                    echo '        <p class="card-text text-muted small">' . htmlspecialchars(substr($row['description'], 0, 150)) . (strlen($row['description']) > 150 ? '...' : '') . '</p>';
                    echo '        <a href="#" class="btn readmore-btn btn-primary btn-sm">READ MORE</a>';
                    echo '      </div>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No announcements available.</p>';
            }

            // Close the database connection
            if (isset($db_conn)) {
                $db_conn->close();
            }
        ?>
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
                    <button type="button" class="btn btn-danger" id="deleteConfirmButton">Delete</button>
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
                    <form id="editAnnouncementForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="editAnnouncementTitle" class="form-label"><strong>Title</strong></label>
                            <input type="text" class="form-control text-muted" id="editAnnouncementTitle" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="editAnnouncementDescription" class="form-label"><strong>Description</strong></label>
                            <textarea class="form-control text-muted" id="editAnnouncementDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
    <label for="editAnnouncementImage" class="form-label"><strong>Cover Image</strong>
        <span class="span-text text-muted">(Choose or drag an image to update)</span>
    </label>
    <div class="border rounded px-3 py-2 d-flex align-items-center gap-2" style="height: 45px; cursor: pointer;" id="editImageUploadArea">
        <i class="bi bi-image text-muted" style="font-size: 1.2rem;"></i>
        <span id="currentEditFileName">No file selected</span>
    </div>
    <input type="file" class="form-control mt-2 d-none" id="editAnnouncementImage" name="image">
</div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveChangesButton">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
  </div>
</main>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('announcementImage');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const fileUploadArea = document.querySelector('.border-dashed.bg-light[onclick]');
        const addAnnouncementForm = document.querySelector('#addAnnouncementModal form');
        const editModal = document.getElementById('editAnnouncementModal');
        const editTitleInput = document.getElementById('editAnnouncementTitle');
        const editDescriptionTextarea = document.getElementById('editAnnouncementDescription');
        const editImageInput = document.getElementById('editAnnouncementImage');
        const currentEditFileNameSpan = document.getElementById('currentEditFileName');
        const saveChangesButton = document.getElementById('saveChangesButton');
        const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        const deleteConfirmButton = document.getElementById('deleteConfirmButton');
        const editImageUploadArea = document.getElementById('editImageUploadArea'); // Get the styled div for edit image

        let currentAnnouncementId = null;
        let announcementToDelete = null;

        const editIcons = document.querySelectorAll('.edit-icon');
        const deleteIcons = document.querySelectorAll('.delete-icon');

        editIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                const card = icon.closest('.announcement-card');
                currentAnnouncementId = card.dataset.announcementId;
                editTitleInput.value = card.dataset.title;
                editDescriptionTextarea.value = card.dataset.description;
                $(editModal).modal('show');
            });
        });

        deleteIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                const card = icon.closest('.announcement-card');
                announcementToDelete = card.dataset.announcementId;
                deleteConfirmationModal.show();
            });
        });

        saveChangesButton.addEventListener('click', () => {
            if (currentAnnouncementId) {
                const updatedTitle = editTitleInput.value;
                const updatedDescription = editDescriptionTextarea.value;
                const formData = new FormData(document.getElementById('editAnnouncementForm'));
                formData.append('action', 'edit');
                formData.append('announcement_id', currentAnnouncementId);

                fetch('../../queries/process-edit-delete-announ.php', { // Corrected URL
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        const card = document.querySelector(`.announcement-card[data-announcement-id="${currentAnnouncementId}"]`);
                        if (card) {
                            card.dataset.title = updatedTitle;
                            card.dataset.description = updatedDescription;
                            card.dataset.image = data.new_image_url ? data.new_image_url : card.dataset.image;
                            card.querySelector('.card-title u').textContent = updatedTitle;
                            card.querySelector('.card-text').textContent = updatedDescription;
                            if (data.new_image_url) {
                                card.querySelector('.announcement-image').src = data.new_image_url;
                            }
                        }
                        $(editModal).modal('hide');
                        window.location.reload(true);
                        return false;
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred while saving changes.');
                });

                currentAnnouncementId = null;
            } else {
                console.warn("No announcement ID to save.");
            }
        });

        if (deleteConfirmButton) {
            deleteConfirmButton.addEventListener('click', () => {
                if (announcementToDelete) {
                    fetch('../../queries/process-edit-delete-announ.php', { // Corrected URL
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete&announcement_id=${announcementToDelete}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            const cardToRemove = document.querySelector(`.announcement-card[data-announcement-id="${announcementToDelete}"]`);
                            if (cardToRemove) {
                                cardToRemove.remove();
                            }
                            deleteConfirmationModal.hide();
                            window.location.reload(true);
                            return false;
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An unexpected error occurred while deleting the announcement.');
                    });
                    announcementToDelete = null;
                }
            });
        }

        imageInput.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                    fileUploadArea.style.display = 'none';
                }

                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '#';
                imagePreviewContainer.style.display = 'none';
                fileUploadArea.style.display = 'flex';
                fileUploadArea.style.flexDirection = 'column';
                fileUploadArea.style.justifyContent = 'center';
                fileUploadArea.style.alignItems = 'center';
            }
        });

        const addAnnouncementModalElement = document.getElementById('addAnnouncementModal');
        addAnnouncementModalElement.addEventListener('hidden.bs.modal', function () {
            imagePreview.src = '#';
            imagePreviewContainer.style.display = 'none';
            fileUploadArea.style.display = 'flex';
            fileUploadArea.style.flexDirection = 'column';
            fileUploadArea.style.justifyContent = 'center';
            fileUploadArea.style.alignItems = 'center';
            imageInput.value = '';
        });

        addAnnouncementForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('../../queries/process-announcements.php', { // CORRECTED URL FOR ADD
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    addAnnouncementForm.reset();
                    const addAnnouncementModal = bootstrap.Modal.getInstance(document.getElementById('addAnnouncementModal'));
                    addAnnouncementModal.hide();
                    window.location.reload();
                } else if (data.status === 'error') {
                    alert(data.message);
                } else if (data.status === 'warning') {
                    alert(data.message);
                    addAnnouncementForm.reset();
                    const addAnnouncementModal = bootstrap.Modal.getInstance(document.getElementById('addAnnouncementModal'));
                    addAnnouncementModal.hide();
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Please try again.');
            });
        });

        // JavaScript to trigger the hidden file input for EDIT and display filename
        if (editImageUploadArea && editImageInput && currentEditFileNameSpan) {
            editImageUploadArea.addEventListener('click', function() {
                editImageInput.click(); // Trigger the file input when the styled div is clicked
            });

            editImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    currentEditFileNameSpan.textContent = this.files[0].name;
                } else {
                    currentEditFileNameSpan.textContent = 'No file selected';
                }
            });
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>