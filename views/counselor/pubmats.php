<?php 
    session_start();

    include(__DIR__ . "/../../config/utils.php");
    require __DIR__ . "/../../db/media_store.php";

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

    // Pagination setup
    $limit = 6; 
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch publication materials with pagination
    $sql = "SELECT pub_mat_id, file_title, file_desc, file_url, cover_img_url, type 
            FROM publication_materials 
            ORDER BY pub_mat_id DESC 
            LIMIT $limit OFFSET $offset";
    $result = $db_conn->query($sql);

    $pubMats = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pubMats[] = $row;
        }
    }

    // Get total number of publication materials
    $totalResult = $db_conn->query("SELECT COUNT(*) as total FROM publication_materials");
    $totalRow = $totalResult->fetch_assoc();
    $totalPubMats = $totalRow['total'];
    $totalPages = ceil($totalPubMats / $limit);

    $mediaStore = getMediaStore();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                // Add new publication material
                $title = $_POST['title'];
                $description = $_POST['description'];
                $type = $_POST['publication_type'];

                // Validate the type field
                $validTypes = ['Module', 'Infographics'];
                if (!in_array($type, $validTypes)) {
                    die("Error: Invalid publication type.");
                }

                // Debugging: Ensure $type is not empty
                if (empty($type)) {
                    die("Error: Publication type is required.");
                }

                // Debugging: Check if files are uploaded
                if (!isset($_FILES['publication_file']) || !isset($_FILES['cover_image'])) {
                    die("Error: Files not uploaded.");
                }

                $publicationFile = $_FILES['publication_file'];
                $coverImage = $_FILES['cover_image'];

                if ($publicationFile['error'] === UPLOAD_ERR_OK && $coverImage['error'] === UPLOAD_ERR_OK) {

                    // Upload files to media store
                    try {
                        $uploadedPublication = $mediaStore->uploadApi()->upload($publicationFile['tmp_name']);
                        $uploadedCover = $mediaStore->uploadApi()->upload($coverImage['tmp_name']);
                    } catch (Exception $e) {
                        die("Error uploading files to media store: " . $e->getMessage());
                    }

                    $fileUrl = $uploadedPublication['secure_url'];
                    $coverUrl = $uploadedCover['secure_url'];

                    // Insert into database
                    $stmt = $db_conn->prepare("INSERT INTO publication_materials (file_title, file_desc, file_url, cover_img_url, type) VALUES (?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        die("Error preparing statement: " . $db_conn->error);
                    }
                    $stmt->bind_param("sssss", $title, $description, $fileUrl, $coverUrl, $type);
                    if (!$stmt->execute()) {
                        die("Error executing statement: " . $stmt->error);
                    }
                    $stmt->close();

                    header("Location: pubmats.php");
                    exit();
                } else {
                    die("Error uploading files.");
                }
            } elseif ($_POST['action'] === 'edit') {
                // Edit publication material
                $id = $_POST['id'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $type = $_POST['publication_type']; // Ensure this is captured correctly

                // Validate the type field
                $validTypes = ['Module', 'Infographics'];
                if (!in_array($type, $validTypes)) {
                    die("Error: Invalid publication type.");
                }

                $fileUrl = $_POST['current_file_url'];
                $coverUrl = $_POST['current_cover_url'];

                // Handle optional file uploads
                if (!empty($_FILES['publication_file']['tmp_name'])) {
                    $uploadedPublication = $mediaStore->uploadApi()->upload($_FILES['publication_file']['tmp_name']);
                    $fileUrl = $uploadedPublication['secure_url'];
                }
                if (!empty($_FILES['cover_image']['tmp_name'])) {
                    $uploadedCover = $mediaStore->uploadApi()->upload($_FILES['cover_image']['tmp_name']);
                    $coverUrl = $uploadedCover['secure_url'];
                }

                // Update database
                $stmt = $db_conn->prepare("UPDATE publication_materials SET file_title = ?, file_desc = ?, file_url = ?, cover_img_url = ?, type = ? WHERE pub_mat_id = ?");
                if (!$stmt) {
                    die("Error preparing statement: " . $db_conn->error);
                }
                $stmt->bind_param("sssssi", $title, $description, $fileUrl, $coverUrl, $type, $id);
                if (!$stmt->execute()) {
                    die("Error executing statement: " . $stmt->error);
                }
                $stmt->close();

                header("Location: pubmats.php");
                exit();
            } elseif ($_POST['action'] === 'delete') {
                // Delete publication material
                $id = $_POST['id'];

                // Optionally delete files from media store (not implemented here)
                $stmt = $db_conn->prepare("DELETE FROM publication_materials WHERE pub_mat_id = ?");
                if (!$stmt) {
                    die("Error preparing statement: " . $db_conn->error);
                }
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    die("Error executing statement: " . $stmt->error);
                }
                $stmt->close();

                header("Location: pubmats.php");
                exit();
            }
        }
    }

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
    <link rel="stylesheet" href="../../assets/css/counselor.css">
</head>
<body>
    <?php include(__DIR__ . '/../components/counselor/sidebar.php'); ?>
    <main>
      <!-- BG IMAGE -->
      <div class="pubmats_c-banner"></div>

      <!-- TITLE CENTERED, BUTTON RIGHT -->
      <div class="container pt-4 position-relative">
        <h2 class="text-center pubmats_c-title-text">Publication Materials</h2>
        <button class="btn pubmats_c-btn btn-primary position-absolute end-0 top-0" data-bs-toggle="modal" data-bs-target="#addNewModal">
          <i class="bi bi-plus-lg"></i> <span class="fw-bold">Add New</span>
        </button>
      </div>

      <!-- CARDS SECTION -->
      <div class="container mt-5 container-pubmats_c">
        <div class="row g-5 row-pubmats_c">
          <?php foreach ($pubMats as $pubMat): ?>
            <div class="col-md-6 col-lg-6 col-pubmats_c-materials">
              <div class="card shadow-sm h-100 card-pubmats_c-materials" id="card-<?php echo $pubMat['pub_mat_id']; ?>">
                <div class="row g-0">
                  <div class="col-4 d-flex justify-content-center">
                    <img src="<?php echo htmlspecialchars($pubMat['cover_img_url']); ?>" class="pubmats_c-images img-fluid rounded-start" alt="Cover Image" />
                  </div>
                  <div class="col-8 col-pubmats_c">
                    <div class="card-body d-flex flex-column justify-content-between h-100 body-pubmats_c">
                      <h5 class="card-title"><?php echo htmlspecialchars(strip_tags($pubMat['file_title'])); ?></h5>
                      <div class="card-text small text-muted no-scroll">
                        <?php echo htmlspecialchars(strip_tags(mb_strimwidth($pubMat['file_desc'], 0, 170, '...'))); ?>
                      </div>
                      <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                        <span class="badge mb-1 me-2 badge-pubmats_c">
                            <?php echo htmlspecialchars(strip_tags($pubMat['type'])); ?>
                        </span>
                        <div class="d-flex gap-2">
                          <button
                            class="btn btn-primary btn-sm edit-pubmats_c-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-id="<?php echo $pubMat['pub_mat_id']; ?>"
                            data-title="<?php echo htmlspecialchars($pubMat['file_title']); ?>"
                            data-description="<?php echo htmlspecialchars($pubMat['file_desc']); ?>"
                            data-type="<?php echo htmlspecialchars($pubMat['type']); ?>"
                            data-file-url="<?php echo htmlspecialchars($pubMat['file_url']); ?>"
                            data-cover-url="<?php echo htmlspecialchars($pubMat['cover_img_url']); ?>"
                          >
                            Edit
                          </button>
                          <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $pubMat['pub_mat_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- PAGINATION -->
      <div class="pagination-container pagination-pubmats_c">
        <nav class="d-flex justify-content-center mt-4">
          <ul class="pagination">
            <!-- Previous Button -->
            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
              <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                <i class="bi bi-arrow-left"></i>
              </a>
            </li>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>

            <!-- Next Button -->
            <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
              <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                <i class="bi bi-arrow-right"></i>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </main>

    <!-- ADD NEW MODAL -->
    <div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header text-white bg-primary">
            <h5 class="modal-title w-100 text-center" id="addNewModalLabel">Add New Publication Material</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="addNewForm" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="add">
              <!-- Title -->
              <div class="mb-3">
                <label for="newTitle" class="form-label">Title</label>
                <input type="text" class="form-control" id="newTitle" name="title" required>
              </div>
              <!-- Description -->
              <div class="mb-3">
                <label for="newDescription" class="form-label">Description</label>
                <textarea class="form-control preformatted-text" id="newDescription" name="description" rows="3" required></textarea>
              </div>
              <!-- Publication Type -->
              <div class="mb-3">
                <label for="newType" class="form-label">Publication Type</label>
                <select class="form-select" id="newType" name="publication_type" required>
                  <option value="" disabled selected>Select type</option>
                  <option value="Module">Module</option>
                  <option value="Infographics">Infographics</option>
                </select>
              </div>
              <!-- Publication File -->
              <div class="mb-3">
                <label for="newPublicationFile" class="form-label">Publication (PDF File)</label>
                <input type="file" class="form-control" id="newPublicationFile" name="publication_file" accept="application/pdf" required>
              </div>
              <!-- Cover Image -->
              <div class="mb-3">
                <label for="newCoverImage" class="form-label">Cover Image</label>
                <input type="file" class="form-control" id="newCoverImage" name="cover_image" accept="image/*" required>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-success">Add Material</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header text-white bg-primary">
            <h5 class="modal-title w-100 text-center" id="editModalLabel">Edit Publication Material</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editForm" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" id="editId" name="id">
              <input type="hidden" id="currentFileUrl" name="current_file_url">
              <input type="hidden" id="currentCoverUrl" name="current_cover_url">
              <!-- Title -->
              <div class="mb-3">
                <label for="editTitle" class="form-label">Title</label>
                <input type="text" class="form-control" id="editTitle" name="title" required>
              </div>
              <!-- Description -->
              <div class="mb-3">
                <label for="editDescription" class="form-label">Description</label>
                <textarea class="form-control preformatted-text" id="editDescription" name="description" rows="3" required></textarea>
              </div>
              <!-- Publication Type -->
              <div class="mb-3">
                <label for="editType" class="form-label">Publication Type</label>
                <select class="form-select" id="editType" name="publication_type" required>
                  <option value="" disabled selected>Select type</option>
                  <option value="Module">Module</option>
                  <option value="Infographics">Infographics</option>
                </select>
              </div>
              <!-- Publication File -->
              <div class="mb-3">
                <label for="editPublicationFile" class="form-label">Publication (PDF File)</label>
                <input type="file" class="form-control" id="editPublicationFile" name="publication_file" accept="application/pdf">
                <small class="form-text text-muted">Leave empty if you don't want to replace the file.</small>
              </div>
              <!-- Cover Image -->
              <div class="mb-3">
                <label for="editCoverImage" class="form-label">Cover Image</label>
                <input type="file" class="form-control" id="editCoverImage" name="cover_image" accept="image/*">
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-success">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title w-100 text-center" id="deleteModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this publication material? This action cannot be undone.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const editModal = document.getElementById('editModal');

        // Populate modal fields on open
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            document.getElementById('editId').value = button.getAttribute('data-id');
            document.getElementById('editTitle').value = button.getAttribute('data-title').replace(/<\/?[^>]+(>|$)/g, "");
            document.getElementById('editDescription').value = button.getAttribute('data-description').replace(/<\/?[^>]+(>|$)/g, "");
            document.getElementById('editType').value = button.getAttribute('data-type').replace(/<\/?[^>]+(>|$)/g, "");
            document.getElementById('currentFileUrl').value = button.getAttribute('data-file-url');
            document.getElementById('currentCoverUrl').value = button.getAttribute('data-cover-url');
        });

        // Handle "Add New" form
        document.getElementById('addNewForm').addEventListener('submit', function (e) {
            e.preventDefault();
            this.submit();
        });

        // Handle "Edit" form
        document.getElementById('editForm').addEventListener('submit', function (e) {
            e.preventDefault();
            this.submit();
        });

        // Handle "Delete" confirmation
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            const deleteForm = document.createElement('form');
            deleteForm.method = 'POST';
            deleteForm.action = 'pubmats.php';
            deleteForm.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="${selectedItemId}">
            `;
            document.body.appendChild(deleteForm);
            deleteForm.submit();
        });
    </script>
</body>
</html>