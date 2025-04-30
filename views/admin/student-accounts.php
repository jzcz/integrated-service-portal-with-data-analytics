<?php
session_start();
include(__DIR__ . "/../../config/utils.php");
$conn = require __DIR__ . "/../../db/db_conn.php"; 

if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
  header("location: ../public/counselor-admin-login-page.php");
  exit();
}

if (isset($_POST['toggle'])) {
  $user_id = $_POST['user_id'];

  $stmt = $conn->prepare("SELECT is_disabled FROM user WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row) {
      $newDisabled = $row['is_disabled'] ? 0 : 1;
      $updateStmt = $conn->prepare("UPDATE user SET is_disabled = ? WHERE user_id = ?");
      $updateStmt->bind_param("ii", $newDisabled, $user_id);
      $updateStmt->execute();
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Accounts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/global.css">
  <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<?php include(__DIR__ . '/../components/admin/sidebar.php'); ?>

<main class="p-4">
  <h2 class="fw-bold text-center mb-4" style="color: #004085;">Student Accounts</h2>

  <div class="d-flex justify-content-between mb-3">
  <button type="button" class="btn" style="background-color: #004085; color: white; font-weight: bold;">Add Account</button>
    <form method="GET" class="d-flex align-items-end">
      <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search account" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button type="submit" class="btn btn-sm" style="width: 120px; background-color: #004085; color: white;">
        <i class="bi bi-search"></i> Search
      </button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover text-center align-middle">
      <thead class="table-light">
        <tr>
          <th>Full Name</th>
          <th>Email</th>
          <th>Date Created</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
<?php
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 8;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = "u.role = 'Student'";
$params = [];
$types = "";

if (!empty($search)) {
    $where .= " AND (CONCAT(s.last_name, ', ', s.first_name, ' ', s.middle_name) LIKE ? OR u.email LIKE ?)";
    $searchParam = '%' . $search . '%';
    $params = [$searchParam, $searchParam];
    $types = "ss";
}

// Count total results
$countSQL = "SELECT COUNT(*) as total FROM students s JOIN user u ON s.user_id = u.user_id WHERE $where";
$countStmt = $conn->prepare($countSQL);
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalPages = ceil($countResult['total'] / $limit);

// Fetch paginated results
$dataSQL = "SELECT s.student_no, CONCAT(s.last_name, ', ', s.first_name, ' ', s.middle_name) AS full_name,
            u.email, u.created_at, u.is_disabled, u.user_id
            FROM students s
            JOIN user u ON s.user_id = u.user_id
            WHERE $where
            LIMIT ? OFFSET ?";
$dataStmt = $conn->prepare($dataSQL);
if ($types) {
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;
    $dataStmt->bind_param($types, ...$params);
} else {
    $dataStmt->bind_param("ii", $limit, $offset);
}
$dataStmt->execute();
$result = $dataStmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $isDisabled = (bool)$row['is_disabled'];
    $buttonText = $isDisabled ? 'Activate' : 'Disable';
    $buttonColor = $isDisabled ? '#198754' : '#d6534f';  

    echo "<tr>
            <td>{$row['full_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['created_at']}</td>
            <td>
              <div class='btn-group btn-group-sm' style='gap: 5px;'>
                <a href='#' class='btn' style='background-color: #3879b1; color: white; border-radius: 8px;'
                   data-bs-toggle='modal' data-bs-target='#viewAccountModal'
                   data-fullname='{$row['full_name']}'
                   data-studno='{$row['student_no']}'
                   data-email='{$row['email']}'
                   data-datecreated='{$row['created_at']}'>
                   View
                </a>
                <form method='POST' action='' style='display:inline-block;'>
                  <input type='hidden' name='user_id' value='{$row['user_id']}'>
                  <button type='submit' name='toggle' class='btn' style='background-color: {$buttonColor}; color: white; border-radius: 8px;'>
                    {$buttonText}
                  </button>
                </form>
              </div>
            </td>
          </tr>";
}
?>
      </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= ($page > 1) ? '?page=' . ($page - 1) . (!empty($search) ? '&search=' . urlencode($search) : '') : '#' ?>" style="color: #004085;">&laquo;</a>
          </li>
          <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= ($page < $totalPages) ? '?page=' . ($page + 1) . (!empty($search) ? '&search=' . urlencode($search) : '') : '#' ?>" style="color: #004085;">&raquo;</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</main>

<!-- Modal -->
<div class="modal fade" id="viewAccountModal" tabindex="-1" aria-labelledby="viewAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #004085; color: white;">
        <h5 class="modal-title" id="viewAccountModalLabel">Account Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
        <p><strong>Student Number:</strong> <span id="modalStudentNo"></span></p>
        <p><strong>Program:</strong> Bachelor of Science in Information Technology</p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Date Created:</strong> <span id="modalDateCreated"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const viewAccountModal = document.getElementById('viewAccountModal');
viewAccountModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  document.getElementById('modalFullName').textContent = button.getAttribute('data-fullname');
  document.getElementById('modalStudentNo').textContent = button.getAttribute('data-studno');
  document.getElementById('modalEmail').textContent = button.getAttribute('data-email');
  document.getElementById('modalDateCreated').textContent = button.getAttribute('data-datecreated');
});
</script>
</body>
</html>
