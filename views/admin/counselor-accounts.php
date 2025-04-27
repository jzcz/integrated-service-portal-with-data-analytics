<?php 
    session_start();

    include(__DIR__ . "/../../config/utils.php");
    
    // check session first exists first
    if (!isset($_SESSION['adminId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Admin') {
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,14..32,100..900;1,14..32,100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <?php
        include(__DIR__ . '/../components/admin/sidebar.php');
    ?>

<main class="p-4">
    <h2 class="fw-bold text-center mb-4" style="font-family: 'Rubik', sans-serif; color: #004085;">Counselor Accounts</h2>

    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex gap-2">
            <div>
                <button type="button" class="btn" style="background-color: #004085; color: white; font-weight: bold;">
                    Add Account
                </button>
            </div>
        </div>
        <div class="d-flex align-items-end">
            <input type="text" class="form-control form-control-sm me-2" placeholder="Search account">
            <button class="btn btn-sm" style="width: 120px; background-color: #004085; color: white;">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <!-- Example row -->
                <tr>
                    <td>Cruz, Jazelle L.</td>
                    <td>cruz.jazelle@gmail.com</td>
                    <td>2025-01-01 14:45</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" style="gap: 5px;">       
                            <a href="#" 
                               class="btn btn-view" 
                               style="background-color: #3879b1; color: white; border-radius: 8px;"
                               data-fullname="Cruz, Jazelle L."
                               data-email="cruz.jazelle@gmail.com"
                               data-datecreated="2025-01-01 14:45">
                               View
                            </a>
                            <a href="#" class="btn" style="background-color: #d6534f; color: white; border-radius: 8px;">Disable</a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Narsico, Rhealyn A.</td>
                    <td>narsico.rhealyn@gmail.com</td>
                    <td>2025-01-02 01:45</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" style="gap: 5px;">
                            <a href="#" 
                               class="btn btn-view" 
                               style="background-color: #3879b1; color: white; border-radius: 8px;"
                               data-fullname="Narsico, Rhealyn A."
                               data-email="narsico.rhealyn@gmail.com"
                               data-datecreated="2025-01-02 01:45">
                               View
                            </a>
                            <a href="#" class="btn" style="background-color: #d6534f; color: white; border-radius: 8px;">Disable</a>
                        </div>
                    </td>
                </tr>

                <!-- Add the same for other rows -->
                <!-- Make sure to update data-fullname, data-email, data-datecreated correctly -->

                <tr>
                    <td>Domanico, Chassy Dhayne</td>
                    <td>domanico.chassy@gmail.com</td>
                    <td>2025-01-03 02:45</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" style="gap: 5px;">
                            <a href="#" 
                               class="btn btn-view" 
                               style="background-color: #3879b1; color: white; border-radius: 8px;"
                               data-fullname="Domanico, Chassy Dhayne"
                               data-email="domanico.chassy@gmail.com"
                               data-datecreated="2025-01-03 02:45">
                               View
                            </a>
                            <a href="#" class="btn" style="background-color: #d6534f; color: white; border-radius: 8px;">Disable</a>
                        </div>
                    </td>
                </tr>

                <!-- (Continue for the rest of the rows) -->

            </tbody>
        </table>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #004085; color: white;">
        <h5 class="modal-title" id="viewModalLabel">Counselor Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Date Created:</strong> <span id="modalDateCreated"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" style="background-color: #004085; color: white;" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var viewButtons = document.querySelectorAll('.btn-view');

        viewButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                var fullname = this.getAttribute('data-fullname');
                var email = this.getAttribute('data-email');
                var datecreated = this.getAttribute('data-datecreated');

                document.getElementById('modalFullName').textContent = fullname;
                document.getElementById('modalEmail').textContent = email;
                document.getElementById('modalDateCreated').textContent = datecreated;

                var viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
                viewModal.show();
            });
        });
    });
</script>

</body>
</html>
