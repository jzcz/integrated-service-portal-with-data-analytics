<?php
session_start();
$db_conn = require(__DIR__ . "/../../db/db_conn.php");

require(__DIR__ . "/../../queries/accounts.php");
include(__DIR__ . "/../../config/utils.php");

$err = "";
$success = "";

if (isset($_GET["err"])) {
    $err = $_GET["err"];
}

if (isset($_GET["success"])) {
    $success = $_GET["success"];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeData($db_conn, $_POST['email'] ?? '');
    $password = sanitizeData($db_conn, $_POST['password'] ?? '');
    $loginRole = $_POST['role'] ?? ''; // Get the selected role from the form

    $user = getAccountByEmail($db_conn, $email); // Modified to fetch by email only

    if (empty($user)) {
        header("location: " . $_SERVER["PHP_SELF"] . "?err=Account does not exist! Please sign up instead.");
        exit();
    }

    if (!password_verify($password, $user['password'])) {
        header("location: " . $_SERVER["PHP_SELF"] . "?err=Invalid email or password! Please enter your correct credentials.");
        exit();
    }

    if ($loginRole === 'admin') {
        $admin = getAdminByUserId($db_conn, $user['user_id']);
        if ($admin) {
            $_SESSION['adminId'] = $admin['admin_id'];
            $_SESSION['userId'] = $user['user_id'];
            $_SESSION['userRole'] = 'Admin';
            // Redirect to admin dashboard
            header('location: ../admin/student-accounts.php'); // Adjust path as needed
            exit();
        } else {
            header("location: " . $_SERVER["PHP_SELF"] . "?err=Admin account not found for this user.");
            exit();
        }
    } elseif ($loginRole === 'counselor') {
        $counselor = getCounselorByUserId($db_conn, $user['user_id']);
        if ($counselor) {
            $_SESSION['counselorId'] = $counselor['counselor_id'];
            $_SESSION['userId'] = $user['user_id'];
            $_SESSION['userRole'] = 'Counselor';
            // Redirect to counselor dashboard
            header('location: ../counselor/announcements-page.php'); // Adjust path as needed
            exit();
        } else {
            header("location: " . $_SERVER["PHP_SELF"] . "?err=Counselor account not found for this user.");
            exit();
        }
    } else {
        header("location: " . $_SERVER["PHP_SELF"] . "?err=Please select a valid role.");
        exit();
    }
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
            position: relative;
            min-height: 100vh;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            padding: 0;
        }

        body {
            display: flex;
        }

        .container-fluid {
            height: 100vh;
            display: flex;
            padding: 0;
            padding-left: 0;
        }

        .row {
            display: flex;
            height: 100%;
        }

        .image-container {
            width: 50%;
            height: 100%;
            overflow: hidden;
        }
    </style>
</head>
<body>
<main class="container-fluid h-100 mt-0">
    <div class="row h-100 w-100">
        <div class="col-md-7 image-container mt-0">
            <img src="../../static/QCU-BUILDING2.png" alt="Login Image" class="login-image">
        </div>
        <div class="col-md-4 pt-5 mt-3 login-container">
            <img src="../../static/qcu-logo-login.png" alt="QCU Logo" class="login-logo">
            <h5 class="text-center p-2 mb-2"><b>Guidance & Counseling Unit</b></h5>
            <p class="text-center mb-3">Welcome back! Please enter your credentials to login.</p>

            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="text-secondary form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="text-secondary form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="role" class="text-secondary form-label">Role</label>
                    <select class="text-secondary form-select" id="role" name="role" required>
                        <option selected disabled>Role</option>
                        <option value="admin">Admin</option>
                        <option value="counselor">Counselor</option>
                    </select>
                </div>
                <?php if (!empty($err)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="submit" class="login-btn btn-primary p-1">Login</button>
                </div>
            </form>

            <p class="mt-3 text-center text-muted"><i>*Please contact the admin for assistance with login issues.</i></p>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
<script>
    const passwordInput = document.querySelector('#password');
    const togglePassword = document.querySelector('#togglePassword');
    const icon = document.querySelector('#togglePassword i');

    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        // toggle the icon
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
</script>
</html>
<?php
$db_conn->close();
?>