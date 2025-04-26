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
  <?php include(__DIR__ . '/../components/admin/sidebar.php'); ?>

<main class="p-4">
  <h2 class="fw-bold text-center mb-4" style="font-family: 'Rubik', sans-serif; color: #004085;">Student Accounts</h2>

  <div class="d-flex justify-content-between mb-3">
    <div class="d-flex gap-2">
      <button type="button" class="btn" style="background-color: #004085; color: white; font-weight: bold;">Add Account</button>
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

<!-- Table Rows -->
<?php
$accounts = [
  ["202410001", "Cruz, Jazelle L.", "cruz.jazelle@gmail.com", "2025-01-01 14:45"],
  ["202410002", "Narsico, Rhealyn A.", "narsico.rhealyn@gmail.com", "2025-01-02 01:45"],
  ["202410003", "Domanico, Chassy Dhayne", "domanico.chassy@gmail.com", "2025-01-03 02:45"],
  ["202410004", "Lagmay, Vaughn", "lagmay.vaughn@gmail.com", "2025-01-31 03:45"],
  ["202410005", "Lopez, Diana Paula", "lopez.diana@gmail.com", "2025-01-11 10:45"],
  ["202410006", "Del Mundo, Joshua", "delmundo.joshua@gmail.com", "2025-01-01 14:45"],
  ["202410007", "Cresencio, Princess Jasmine S.", "cresencio.princess@gmail.com", "2025-01-01 14:45"],
  ["202410008", "Labanan, Llysa A.", "labanan.llysa@gmail.com", "2025-01-01 14:45"],
  ["202410009", "Balili, Erica Mea B.", "balili.erica@gmail.com", "2025-01-01 14:45"],
  ["202410010", "Santiago, Juan Z.", "santiago.juan@gmail.com", "2025-01-01 14:45"]
];

foreach ($accounts as $account) {
  echo "<tr>
          <td>{$account[1]}</td>
          <td>{$account[2]}</td>
          <td>{$account[3]}</td>
          <td>
            <div class='btn-group btn-group-sm' role='group' style='gap: 5px;'>
              <a href='#' class='btn' style='background-color: #3879b1; color: white; border-radius: 8px;'
                data-bs-toggle='modal' data-bs-target='#viewAccountModal'
                data-fullname='{$account[1]}'
                data-studno='{$account[0]}'
                data-email='{$account[2]}'
                data-datecreated='{$account[3]}'>
                View
              </a>
              <a href='#' class='btn' style='background-color: #d6534f; color: white; border-radius: 8px;'>Disable</a>
            </div>
          </td>
        </tr>";
}
?>
</tbody>
    </table>
  </div>
</main>

<!-- MODAL -->
<div class="modal fade" id="viewAccountModal" tabindex="-1" aria-labelledby="viewAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #004085; color: white;">
        <h5 class="modal-title" id="viewAccountModalLabel">Account Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
        <p><strong>Student Number:</strong> <span id="modalStudentNumber"></span></p>
        <p><strong>Program:</strong> Bachelor of Science in Information Technology</p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Date Created:</strong> <span id="modalDateCreated"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
const viewAccountModal = document.getElementById('viewAccountModal');

viewAccountModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;

  const fullName = button.getAttribute('data-fullname');
  const studentNumber = button.getAttribute('data-studno');
  const email = button.getAttribute('data-email');
  const dateCreated = button.getAttribute('data-datecreated');

  document.getElementById('modalFullName').textContent = fullName;
  document.getElementById('modalStudentNumber').textContent = studentNumber;
  document.getElementById('modalEmail').textContent = email;
  document.getElementById('modalDateCreated').textContent = dateCreated;
});
</script>

</body>
</html>
