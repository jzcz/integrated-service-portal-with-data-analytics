<?php 
  session_start();

  include(__DIR__ . "/../../config/utils.php");
  $db_conn = require __DIR__ . "/../../db/db_conn.php";

  if (!isset($_SESSION['adminId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Admin') {
    header("location: ../public/counselor-admin-login-page.php");
    exit();
  }

  if ($db_conn->connect_error) {
      die("Connection failed: " . $db_conn->connect_error);
  }

  if (isset($_POST['toggle'])) {
    $user_id = $_POST['user_id'];

    // Fetch current status
    $stmt = $db_conn->prepare("SELECT status FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $currentStatus = $row['status'];
        $newStatus = ($currentStatus == 'Active') ? 'Disabled' : 'Active';

        // Update status
        $updateStmt = $db_conn->prepare("UPDATE user SET status = ? WHERE user_id = ?");
        $updateStmt->bind_param("si", $newStatus, $user_id);
        if ($updateStmt->execute()) {
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            echo "Error updating status: " . $db_conn->error;
        }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rubik&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/global.css">
  <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
  <?php include(__DIR__ . '/../components/admin/sidebar.php'); ?>

<main class="p-4">
  <h2 class="fw-bold text-center mb-4" style="font-family: 'Rubik', sans-serif; color: #004085;">Admin Accounts</h2>

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
<?php
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 8;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$whereCondition = "u.role = 'Admin'";
$params = [];
$types = "";

if (!empty($search)) {
    $whereCondition .= " AND (CONCAT(s.last_name, ', ', s.first_name, ' ', s.middle_name) LIKE ? OR u.email LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam];
    $types = "ss";
}

$totalQuery = "SELECT COUNT(*) as total FROM admin s JOIN user u ON s.user_id = u.user_id WHERE $whereCondition";
if ($stmt = $db_conn->prepare($totalQuery)) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $totalRow = $result->fetch_assoc();
    $totalAccounts = $totalRow['total'];
    $stmt->close();
} else {
    $totalAccounts = 0;
}

$totalPages = ceil($totalAccounts / $limit);

$sql = "SELECT s.admin_id, CONCAT(s.last_name, ', ', s.first_name, ' ', s.middle_name) AS full_name, u.email, u.created_at, u.status, u.user_id FROM admin s JOIN user u ON s.user_id = u.user_id WHERE $whereCondition LIMIT $limit OFFSET $offset";

if ($stmt = $db_conn->prepare($sql)) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $status = $row['status'];
        $buttonText = $status == 'Active' ? 'Disable' : 'Activate';
        $buttonColor = $status == 'Active' ? '#d6534f' : '#198754';

        echo "<tr>
                <td>{$row['full_name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['created_at']}</td>
                <td>
                  <div class='btn-group btn-group-sm' role='group' style='gap: 5px;'>
                    <a href='#' class='btn' style='background-color: #3879b1; color: white; border-radius: 8px;' data-bs-toggle='modal' data-bs-target='#viewAccountModal' data-fullname='{$row['full_name']}' data-studno='{$row['admin_id']}' data-email='{$row['email']}' data-datecreated='{$row['created_at']}'>View</a>
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
    $stmt->close();
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
        <p><strong>Admin Number:</strong> <span id="modalAdminNumber"></span></p>
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

<?php $db_conn->close(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const viewAccountModal = document.getElementById('viewAccountModal');
viewAccountModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  document.getElementById('modalFullName').textContent = button.getAttribute('data-fullname');
  document.getElementById('modalAdminNumber').textContent = button.getAttribute('data-studno');
  document.getElementById('modalEmail').textContent = button.getAttribute('data-email');
  document.getElementById('modalDateCreated').textContent = button.getAttribute('data-datecreated');
});
</script>

</body>
</html>