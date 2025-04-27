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
    <!-- CARD 1 -->
    <div class="col-md-6 col-lg-6 col-pubmats_c-materials">
  <div class="card shadow-sm h-100 card-pubmats_c-materials" id="card-1">
    <div class="row g-0 ">
      <div class="col-4 d-flex justify-content-center">
        <img src="../../static/Materials (1).png" class="pubmats_c-images img-fluid rounded-start" alt="Cover Image" id="card-image-1" />
      </div>
      <div class="col-8 col-pubmats_c">
        <div class="card-body d-flex flex-column justify-content-between h-100 body-pubmats_c">
          <h5 class="card-title" id="card-title-1">Card Title 1</h5>
          <p class="card-text small text-muted" id="card-desc-1">Short description for card 1 goes here.</p>
          <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <span class="badge mb-1 me-2 badge-pubmats_c" id="card-type-1">Article</span>

            <div class="d-flex gap-2">
              <button
                class="btn btn-primary btn-sm edit-pubmats_c-btn"
                data-bs-toggle="modal"
                data-bs-target="#editModal"
                data-title="Card Title 1"
                data-description="Short description for card 1 goes here."
                data-type="Article"
                data-archived="false"
                data-publication-src="file1.pdf"
                data-cover-src="cover1.jpg"
                data-card-id="1"
              >
                Edit
              </button>
              <button class="btn btn-danger btn-sm delete-pubmats_c-btn" onclick="openDeleteModal('Card Title 1')">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

    <!-- CARD 2 -->
    <div class="col-md-6 col-lg-6 col-pubmats_c-materials">
  <div class="card shadow-sm h-100 card-pubmats_c-materials" id="card-2">
    <div class="row g-0">
      <div class="col-4 d-flex justify-content-center">
        <img src="../../static/Materials (2).png" class="pubmats_c-images img-fluid rounded-start" alt="Cover Image" id="card-image-2" />
      </div>
      <div class="col-8 col-8 col-pubmats_c">
        <div class="card-body d-flex flex-column justify-content-between h-100 body-pubmats_c">
          <h5 class="card-title" id="card-title-2">Card Title 2</h5>
          <p class="card-text small text-muted" id="card-desc-2">Short description for card 2 goes here.</p>
          <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
          <span class="badge mb-1 me-2 badge-pubmats_c" id="card-type-2">Article</span>

          <div class="d-flex gap-2">
            <button
              class="btn edit-pubmats_c-btn btn-sm me-2 btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#editModal"
              data-title="Card Title 2"
              data-description="Short description for card 2 goes here."
              data-type="Article"
              data-archived="false"
              data-publication-src="file2.pdf"
              data-cover-src="cover2.jpg"
              data-card-id="2"
            >
              Edit
            </button>
            <button class="btn delete-pubmats_c-btn btn-sm btn-danger" onclick="openDeleteModal('Card Title 2')">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

    <!-- CARD 3 -->
    <div class="col-md-6 col-lg-6 col-pubmats_c-materials">
      <div class="card shadow-sm h-100 card-pubmats_c-materials" id="card-3">
        <div class="row g-0">
          <div class="col-4 d-flex justify-content-center">
            <img src="../../static/Materials (3).png" class="pubmats_c-images img-fluid rounded-start" alt="Cover Image" id="card-image-3" />
          </div>
          <div class="col-8 col-8 col-pubmats_c">
            <div class="card-body d-flex flex-column justify-content-between h-100 body-pubmats_c">
              <h5 class="card-title" id="card-title-3">Card Title 3</h5>
              <p class="card-text small text-muted" id="card-desc-3">Short description for card 3 goes here.</p>
              <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
              <span class="badge mb-1 me-2 badge-pubmats_c" id="card-type-3">Module</span>

              <div class="d-flex gap-2">
                <button
                  class="btn edit-pubmats_c-btn btn-sm me-2 btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  data-title="Card Title 3"
                  data-description="Short description for card 3"
                  data-type="Module"
                  data-archived="false"
                  data-publication-src="file3.pdf"
                  data-cover-src="your-thumbnail3.png"
                  data-card-id="3"
                >
                  Edit
                </button>
                <button class="btn delete-pubmats_c-btn btn-sm btn-danger" onclick="openDeleteModal('Card Title 3')">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- CARD 4 -->
    <div class="col-md-6 col-lg-6 col-pubmats_c-materials">
      <div class="card shadow-sm h-100 card-pubmats_c-materials" id="card-4">
        <div class="row g-0">
          <div class="col-4 d-flex justify-content-center">
            <img src="../../static/Materials (4).png" class="pubmats_c-images img-fluid rounded-start" alt="Cover Image" id="card-image-4" />
          </div>
          <div class="col-8 col-8 col-pubmats_c">
            <div class="card-body d-flex flex-column justify-content-between h-100 body-pubmats_c">
              <h5 class="card-title" id="card-title-4">Card Title 4</h5>
              <p class="card-text small text-muted" id="card-desc-4">Short description for card 4 goes here.</p>
              <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
              <span class="badge mb-1 me-2 badge-pubmats_c" id="card-type-4">Module</span>

              <div class="d-flex gap-2">
                <button
                  class="btn edit-pubmats_c-btn btn-sm me-2 btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  data-title="Card Title 4"
                  data-description="Short description for card 4"
                  data-type="Module"
                  data-archived="false"
                  data-publication-src="file4.pdf"
                  data-cover-src="your-thumbnail4.png"
                  data-card-id="4"
                >
                  Edit
                </button>
                <button class="btn delete-pubmats_c-btn btn-sm btn-danger" onclick="openDeleteModal('Card Title 4')">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- CARD 5 -->
    <div class="col-md-6 col-lg-6 col-pubmats_c-materials">
      <div class="card shadow-sm h-100 card-pubmats_c-materials" id="card-5">
        <div class="row g-0">
          <div class="col-4 d-flex justify-content-center">
            <img src="../../static/Materials (5).png" class="pubmats_c-images img-fluid rounded-start" alt="Cover Image" id="card-image-5" />
          </div>
          <div class="col-8 col-8 col-pubmats_c">
            <div class="card-body d-flex flex-column justify-content-between h-100 body-pubmats_c">
              <h5 class="card-title" id="card-title-5">Card Title 5</h5>
              <p class="card-text small text-muted" id="card-desc-5">Short description for card 5 goes here.</p>
	            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
              <span class="badge mb-1 me-2 badge-pubmats_c" id="card-type-5">Module</span>

              <div class="d-flex gap-2">
                <button
                  class="btn edit-pubmats_c-btn btn-sm me-2 btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  data-title="Card Title 5"
                  data-description="Short description for card 5"
                  data-type="Module"
                  data-archived="false"
                  data-publication-src="file5.pdf"
                  data-cover-src="your-thumbnail5.png"
                  data-card-id="5"
                >
                  Edit
                </button>
                <button class="btn delete-pubmats_c-btn btn-sm btn-danger" onclick="openDeleteModal('Card Title 5')">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- CARD 6 -->
    <div class="col-md-6 col-lg-6 col-pubmats_c-materials">
      <div class="card shadow-sm h-100 card-pubmats_c-materials" id="card-6">
        <div class="row g-0">
          <div class="col-4 d-flex justify-content-center">
            <img src="../../static/Materials (6).png" class="pubmats_c-images img-fluid rounded-start" alt="Cover Image" id="card-image-6" />
          </div>
          <div class="col-8 col-8 col-pubmats_c">
            <div class="card-body d-flex flex-column justify-content-between h-100 body-pubmats_c">
              <h5 class="card-title" id="card-title-6">Card Title 6</h5>
              <p class="card-text small text-muted" id="card-desc-6">Short description for card 6 goes here.</p>
              <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
              <span class="badge mb-1 me-2 badge-pubmats_c" id="card-type-6">Infographic</span>

              <div class="d-flex gap-2">
                <button
                  class="btn edit-pubmats_c-btn btn-sm me-2 btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  data-title="Card Title 6"
                  data-description="Short description for card 6"
                  data-type="Module"
                  data-archived="false"
                  data-publication-src="file6.pdf"
                  data-cover-src="your-thumbnail6.png"
                  data-card-id="6"
                >
                  Edit
                </button>
                <button class="btn delete-pubmats_c-btn btn-sm btn-danger" onclick="openDeleteModal('Card Title 6')">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- PAGINATION -->
  <div class="pagination-container pagination-pubmats_c">
  <nav class="d-flex justify-content-center mt-4 ">
    <ul class="pagination ">
      <li class="page-item disabled">
        <a class="page-link" href="#"><i class="bi bi-arrow-left"></i></a>
      </li>
      <li class="page-item active">
        <a class="page-link" href="#">1</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="#"><i class="bi bi-arrow-right"></i></a>
      </li>
    </ul>
  </nav>
</div>

<!-- ADD NEW MODAL -->
<div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white header-pubmats_c">
        <h5 class="modal-title w-100 text-center" id="addNewModalLabel">Publish Material</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="addNewForm" enctype="multipart/form-data">
          <!-- Title -->
          <div class="mb-3">
            <label for="newTitle" class="form-label">Title</label>
            <input type="text" class="form-control" id="newTitle" name="title" required>
          </div>

          <!-- Description -->
          <div class="mb-3">
            <label for="newDescription" class="form-label">Description</label>
            <textarea class="form-control" id="newDescription" name="description" rows="3" required></textarea>
          </div>

          <!-- Publication Type -->
          <div class="mb-3">
            <label for="newType" class="form-label">Publication Type</label>
            <select class="form-select" id="newType" name="publication_type" required>
              <option value="" disabled selected>Select type</option>
              <option value="Module">Module</option>
              <option value="Infographic">Infographic</option>
              <option value="Newsletter">Newsletter</option>
              <option value="Poster">Poster</option>
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
      <div class="modal-header text-white header-pubmats_c">
        <h5 class="modal-title w-100 text-center" id="editModalLabel">Edit Material</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="editForm" enctype="multipart/form-data">
          <!-- Title -->
          <div class="mb-3">
            <label for="editTitle" class="form-label">Title</label>
            <input type="text" class="form-control" id="editTitle" name="title" required>
          </div>

          <!-- Description -->
          <div class="mb-3">
            <label for="editDescription" class="form-label">Description</label>
            <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
          </div>

          <!-- Publication Type -->
          <div class="mb-3">
            <label for="editType" class="form-label">Publication Type</label>
            <select class="form-select" id="editType" name="publication_type" required>
              <option value="" disabled selected>Select type</option>
              <option value="Module">Module</option>
              <option value="Infographic">Infographic</option>
              <option value="Newsletter">Newsletter</option>
              <option value="Poster">Poster</option>
            </select>
          </div>

          <!-- Publication File -->
            <div class="mb-3">
             <label for="editPublicationFile" class="form-label">Publication (PDF File)</label>
  
          <!-- Current file link (optional) -->
            <a id="currentPublicationLink" href="#" target="_blank" class="d-block mb-2 text-decoration-underline text-primary">
              View Current Publication
            </a>

          <!-- Upload new PDF file -->
            <input type="file" class="form-control" id="editPublicationFile" name="publication_file" accept="application/pdf">

              <small class="form-text text-muted">Leave empty if you don't want to replace the file.</small>
          </div>

          <!-- Cover Image -->
          <div class="mb-3">
            <label for="editCoverImage" class="form-label">Cover Image</label>
            <input type="file" class="form-control" id="editCoverImage" name="cover_image" accept="image/*">
          </div>

          <!-- Archive Checkbox -->
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="archiveCheckbox" name="is_archived">
            <label class="form-check-label" for="archiveCheckbox">
              Archive Material
            </label>
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

    </main>
    <script>
  let currentCardId = null;

  const editModal = document.getElementById('editModal');

  // Populate modal fields on open
  editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    currentCardId = button.getAttribute('data-card-id');

    document.getElementById('editTitle').value = button.getAttribute('data-title') || "";
    document.getElementById('editDescription').value = button.getAttribute('data-description') || "";
    document.getElementById('editType').value = button.getAttribute('data-type') || "";
    document.getElementById('archiveCheckbox').checked = button.getAttribute('data-archived') === "true";

    // Set current publication link (if exists)
    const publicationSrc = button.getAttribute('data-publication-src');
    const currentLink = document.getElementById('currentPublicationLink'); 

    if (publicationSrc) {
      currentLink.href = publicationSrc;
      currentLink.textContent = "View Current Publication";  
    } else {
      currentLink.href = "#";  
      currentLink.textContent = "No publication available";  
    }
  });

  // Handle form submission ONCE
  document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault();

    if (!currentCardId) return;

    const updatedTitle = document.getElementById('editTitle').value;
    const updatedDesc = document.getElementById('editDescription').value;
    const updatedType = document.getElementById('editType').value;

    // Update the card text content
    document.getElementById(`card-title-${currentCardId}`).textContent = updatedTitle;
    document.getElementById(`card-desc-${currentCardId}`).textContent = updatedDesc;
    document.getElementById(`card-type-${currentCardId}`).textContent = updatedType;

    // Handle cover image update
    const coverInput = document.getElementById('editCoverImage');
    if (coverInput.files && coverInput.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const coverImg = document.getElementById(`card-image-${currentCardId}`);
        if (coverImg) {
          coverImg.src = e.target.result;
        }
      };
      reader.readAsDataURL(coverInput.files[0]);
    }

    // Handle publication file update (if any)
    const publicationFileInput = document.getElementById('editPublicationFile');
    if (publicationFileInput.files && publicationFileInput.files[0]) {
      const publicationFile = publicationFileInput.files[0];
      const newPublicationLink = URL.createObjectURL(publicationFile);
      const currentLink = document.getElementById('currentPublicationLink');
      currentLink.href = newPublicationLink;
      currentLink.textContent = "View Updated Publication"; 
    }

    // Close the modal after all updates
    const modalInstance = bootstrap.Modal.getInstance(editModal);
    if (modalInstance) {
      modalInstance.hide();
    }
  });

    // Handle "Add New" form
  document.getElementById('addNewForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const title = document.getElementById('newTitle').value;
    const description = document.getElementById('newDescription').value;
    const type = document.getElementById('newType').value;
    const coverFile = document.getElementById('newCoverImage').files[0];

    // Optional preview logic here

    // Close modal
    const addModal = bootstrap.Modal.getInstance(document.getElementById('addNewModal'));
    if (addModal) addModal.hide();

    // Reset form
    document.getElementById('addNewForm').reset();
  });

   let selectedItemTitle = "";

  function openDeleteModal(title) {
    selectedItemTitle = title;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
  }

  document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    // You can use the title or ID of the item here to delete
    alert('${selectedItemTitle}" has been deleted!');
    // You can also add an API call here to delete from backend

    // Hide modal after confirming
    const modalElement = document.getElementById('deleteModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    modalInstance.hide();
  });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>