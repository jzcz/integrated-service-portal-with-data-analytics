<?php 
  session_start();

  include(__DIR__ . "/../../config/utils.php");
  include(__DIR__ . "/../../queries/accounts.php");

  if (!isset($_SESSION['adminId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Admin') {
    header("location: ../public/counselor-admin-login-page.php");
    exit();
  }

  $err = null;
  $success  = null;

  $db_conn = require __DIR__ . "/../../db/db_conn.php";

  if(isset($_GET["err"])) {
    $err = $_GET["err"];
  }

  if(isset($_GET["success"])) {
      $success = $_GET["success"];
  }

  if ($db_conn->connect_error) {
      die("Connection failed: " . $db_conn->connect_error);
  }

  if (isset($_POST['toggle'])) {
    $user_id = $_POST['user_id'];

    // Fetch current status
    $stmt = $db_conn->prepare("SELECT is_disabled FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
      $currentStatus = (bool)$row['is_disabled'];
      $newStatus = !$currentStatus;       

        // Update status
        $updateStmt = $db_conn->prepare("UPDATE user SET is_disabled = ? WHERE user_id = ?");
        $updateStmt->bind_param("ii", $newStatus, $user_id);        
        if ($updateStmt->execute()) {
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            echo "Error updating status: " . $db_conn->error;
        }
    }
  }


if($_SERVER['REQUEST_METHOD'] == 'POST' ) {
  $info = [
    'firstName' => $_POST['firstName'],
    'middleName' => isset($_POST['middleName']) ? $_POST['middleName'] : null,
    'lastName' => $_POST['lastName'],
    'suffix' => isset($_POST['suffix']) ? $_POST['suffix'] : null,
    'employeeId' => $_POST['employeeId'],
  ];

  $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $acc = [
    'email' => $_POST['email'],
    'password' => $hashedPassword
  ];
  $res = createAdminAccount($db_conn, $info, $acc);

  if($res) {
    $success = "Account created successfully!";
    header("Location: {$_SERVER['PHP_SELF']}?success=" . urlencode($success));
  } else {
    $err = "Failed to create account. Please try again.";
    header("Location: {$_SERVER['PHP_SELF']}?err=" . urlencode($err));
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
    <!-- MESSAGE MODAL START -->
    <div class="modal fade" id="message-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header 
          <?php echo $err ? "bg-danger"  : "bg-success" ?>
          ">
            <h5 class="modal-title text-white fs-6" id="exampleModalLabel">
            <i class="bi bi-check-circle"></i>
            <?php echo $err ?  'Failed' : '' ?>
            <?php echo $success ? 'Success' : '' ?>
            </h5>
          </div>
          <div class="modal-body">
            <?php echo $err ??  $err ?>
            <?php echo $success ??  $success ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light border-secondary-subtle" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- MESSAGE MODAL END -->
<main class="p-4">
  <h2 class="fw-bold text-center mb-4" style="font-family: 'Rubik', sans-serif; color: #004085;">Admin Accounts</h2>

  <div class="d-flex justify-content-between mb-3">
  <button type="button" class="btn" style="background-color: #004085; color: white; font-weight: bold;" data-bs-toggle="modal" data-bs-target="#addAccount">Add Account</button>

    <form method="GET" class="d-flex align-items-end">
      <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search account" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button type="submit" class="btn btn-sm" style="width: 120px; background-color: #004085; color: white;">
        <i class="bi bi-search"></i> Search
      </button>
    </form>
  </div>

  <div class="modal fade" id="addAccount" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
      <form action="" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Add Admin Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
          <div class="mb-2">
            <label for="exampleFormControlInput1" class="form-label" style="font-size: 14px;">First Name</label>
            <input type="text" name="firstName" class="form-control form-control-sm"  id="exampleFormControlInput1" placeholder="First Name">
          </div>
          <div class="mb-2">
            <label for="exampleFormControlInput2" class="form-label" style="font-size: 14px;">Middle Name <span class="text-danger" style="font-size: 12px;">(leave blank if none)</span></label>
            <input type="text" name="middleName" class="form-control form-control-sm" id="exampleFormControlInput1" placeholder="Middle Name">
          </div>
          <div class="mb-2">
            <label for="exampleFormControlInput3" class="form-label" style="font-size: 14px;">Last Name</label>
            <input type="text" name="lastName" class="form-control form-control-sm" id="exampleFormControlInput1" placeholder="Last Name">
          </div>
          <div class="mb-2">
            <label for="exampleFormControlInput3" name="email" class="form-label" style="font-size: 14px;">Suffix</label>
            <select class="form-select form-select-sm" aria-label="Default select example">
              <option selected  >None</option>
              <option value="Jr.">Jr.</option>
              <option value="Sr.">Sr.</option>
              <option value="III">III</option>
              <option value="IV">IV</option>
            </select>
          </div>
          <div class="mb-2">
            <label for="exampleFormControlInput3" name="email" class="form-label" style="font-size: 14px;">Employee ID</label>
            <input type="text" name="employeeId" class="form-control form-control-sm" id="exampleFormControlInput1" placeholder="Employee ID">
          </div>
          <div class="mb-2">
            <label for="exampleFormControlInput3" class="form-label" style="font-size: 14px;">Email</label>
            <input type="email" name="email" class="form-control form-control-sm" id="exampleFormControlInput1" placeholder="Last Name">
          </div>
          <div class="mb-2">
            <label for="exampleFormControlInput3" class="form-label" style="font-size: 14px;">User Password</label>
  
            <div class="input-group">
              <input type="password" name="password" class="form-control form-control-sm" id="passwordInput" placeholder="Password">
              <span class="input-group-text" id="togglePassword" data-target="passwordInput"><i class="bi bi-eye-slash"></i></span>
            </div> 
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        </form>
      </div>
      
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

$sql = "SELECT s.admin_id, CONCAT(s.last_name, ', ', s.first_name, ' ', s.middle_name) AS full_name, u.email, u.created_at, u.is_disabled, u.user_id, s.employee_id 
FROM admin s JOIN user u ON s.user_id = u.user_id WHERE $whereCondition ORDER BY admin_id DESC LIMIT $limit OFFSET $offset";

if ($stmt = $db_conn->prepare($sql)) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
      $isDisabled = (bool)$row['is_disabled'];
      $buttonText = $isDisabled ? 'Activate' : 'Disable';
      $buttonColor = $isDisabled ? '#198754' : '#d6534f';      

        echo "<tr>
                <td>{$row['full_name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['created_at']}</td>
                <td>
                  <div class='btn-group btn-group-sm' role='group' style='gap: 5px;'>
                    <a href='#' class='btn' style='background-color: #3879b1; color: white; border-radius: 8px;' data-bs-toggle='modal' data-bs-target='#viewAccountModal' data-fullname='{$row['full_name']}' data-studno='{$row['employee_id']}' data-email='{$row['email']}' data-datecreated='{$row['created_at']}'>View</a>
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
        <p><strong>Employee ID:</strong> <span id="modalEmployeeID"></span></p>
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
    const messageModal = new bootstrap.Modal(document.getElementById("message-modal"));
    const passwordToggleBtn = document.getElementById("togglePassword");

  const viewAccountModal = document.getElementById('viewAccountModal');

  viewAccountModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    document.getElementById('modalFullName').textContent = button.getAttribute('data-fullname');
    document.getElementById('modalEmployeeID').textContent = button.getAttribute('data-studno');
    document.getElementById('modalEmail').textContent = button.getAttribute('data-email');
    document.getElementById('modalDateCreated').textContent = button.getAttribute('data-datecreated');
  });

<?php if($err || $success) { echo "messageModal.show();"; }  ?>

    passwordToggleBtn.addEventListener('click', function () {
              const targetInput = document.getElementById(this.dataset.target);
              const icon = this.querySelector('i');

              if (targetInput.type === 'password') {
                  targetInput.type = 'text';
                  icon.classList.remove('bi-eye-slash');
                  icon.classList.add('bi-eye');
              } else {
                  targetInput.type = 'password';
                  icon.classList.remove('bi-eye');
                  icon.classList.add('bi-eye-slash');
              }
        }); 
</script>

</body>
</html>