<?php
  session_start();

  $db_conn = require(__DIR__ . "/../../db/db_conn.php");

  require(__DIR__ . "/../../queries/students.php");
  require(__DIR__ . "/../../queries/accounts.php");
  include(__DIR__ . "/../../config/utils.php");

  $err = "";
  $success = "";

  if(isset($_GET["err"])) {
    $err = $_GET["err"];
  }

  if(isset($_GET["success"])) {
    $success = $_GET["success"];
  }

  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeData($db_conn, $_POST['email']);
    $password = sanitizeData($db_conn, $_POST['password']);

    $user = getAccountByEmail($db_conn, $email, 'Student');
    
    if(empty($user)) {
      header("location: " . $_SERVER["PHP_SELF"] . "?err=Account does not exist! Please sign up instead.");
      exit();
    }

    if(!password_verify($password, $user['password'])) {
      header("location: " . $_SERVER["PHP_SELF"] . "?err=Invalid email or password! Please enter your correct credentials.");
      exit();
    } 

    if(!$user['is_disabled']) {
      header("location: " . $_SERVER["PHP_SELF"] . "?err=Your account is disabled. Please contact the QCU Guidance and Counseling Office for support.");
      exit();
    } 

    // get student profile if passed all authentication
    $student = getStudentByUserId($db_conn, $user['user_id']);

    $_SESSION['studentId'] = $student['student_id'];
    $_SESSION['userId'] = $user['user_id'];
    $_SESSION['userRole'] = 'Student';

    // redirect to announcements page if passed authentication
    header('location: announcements.php');
  } 
  
  $db_conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>

      :root {
          --primary-color: #08437F;
      }

      body{
          max-height: 100vh;
          max-width: 100vw;
          color: black;
      }

      .signup-form-box {
          width: 500px
      }

      .signup-form-title {
        background-color: var(--primary-color);
        color: white;
        padding: 3px 0;
      }

      .signup-form-title p {
        margin: auto;
      }

      .sliding-form label {
        font-size: 14px;
        color:rgb(75, 75, 75)
      }

      .signup-input, option {
        font-size: 14px;
      }  

      .signup-form-wrapper {
        background-color: white;
      }

      .sign-up-bg-img-container {
        position: absolute;
        height:100%;
        width: 100%;
        z-index: -99;
        overflow: hidden;
      }

      .sign-up-bg-img-container img {
        object-fit: cover;
        height: 100%;
        width: 100%;
      }

      .form-control {
        font-size: 14px;
      }

      #signup-link {
        color:  rgb(56, 114, 190);
        cursor: pointer;
        text-decoration: underline;
      }

      #login-btn {
        background-color: var(--primary-color);
        border: none;
      }

      label {
        font-size: 14px
      }

      #togglePassword {
        cursor: pointer;
      }
    </style>
</head>
<body>
    <!-- ERROR MODAL START -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger">
            <h5 class="modal-title text-white fs-6" id="exampleModalLabel">
            <i class="bi bi-exclamation-triangle"></i>
              Error
            </h5>
          </div>
          <div class="modal-body" id="errMsg">
            <?php echo $err; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light border-secondary-subtle" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ERROR MODAL END -->

    <!-- SUCCESS MODAL START -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-success">
            <h5 class="modal-title text-white fs-6" id="exampleModalLabel">
            <i class="bi bi-check-circle"></i>
              Success
            </h5>
          </div>
          <div class="modal-body">
            <?php echo $success; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light border-secondary-subtle" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- SUCCESS MODAL END -->
    <div class="sign-up-bg-img-container">
        <img src="../../static/qcu-bg-img.jpg"" alt="">
    </div>
    <div class="">
        <div class="signup-form-box mx-auto pt-5 rounded-2">
            <div class="signup-form-title d-flex rounded-top-2 ">
                <p class="fw-bold">STUDENT LOGIN</p>
            </div>
            <div class="signup-form-wrapper">
                <div class="mb-2 pt-3 flex-column gap-2 form-header d-flex align-items-center justify-content-center">
                    <img src="http://localhost/integrated-service-portal-with-data-analytics/static/qcu-logo.jpg" width="60" height="60" alt="">
                    <p class="fw-bold">Guidance and Counseling Unit</p>
                </div>
                <form method="POST" action="" id="loginForm" class="d-flex flex-column gap-4 container-fluid px-3 pb-4" >
                    <div>
                        <span class="form-text fw-bold">Login Form</span>
                        <div class="form-text">Please fill out the form with the <span class="fw-bold">necessary</span> details</div>
                    </div>     
                    <div class="d-flex flex-column gap-3">
                      <div>
                        <label for="emailInput" class="form-label fw-normal text-body-secondary">Email Address</label>
                        <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email">
                      </div>
                      <div>
                      <label for="password" class="form-label fw-normal text-body-secondary">Password</label>
                        <div class="input-group">
                          <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password">
                          <span class="input-group-text" id="togglePassword" data-target="passwordInput"><i class="bi bi-eye-slash"></i></span>
                        </div> 
                      </div>
                      <button type="button" class="btn btn-primary w-100 mt-1" id="login-btn">Login</button>
                      <div id="passwordHelpBlock" class="form-text fst-italic align-self-center">
                          Don't have a student account? <a id="signup-link" >Sign Up</a>
                      </div> 
                    </div>         
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        const errMsg = document.getElementById("errMsg");
        const passwordInput = document.getElementById("passwordInput");
        const emailInput = document.getElementById("emailInput");
        const passwordToggleBtn = document.getElementById("togglePassword");
        const loginBtn = document.getElementById("login-btn");
        const loginForm = document.getElementById("loginForm");

        <?php if ($err) { echo 'errorModal.show();'; } ?>
        <?php if ($success) { echo 'successModal.show();'; } ?>

        const signupLink = document.getElementById('signup-link');
        
        signupLink.addEventListener('click', () => {
          window.location.href ='./../public/sign-up.php';
        })

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

        loginBtn.addEventListener('click', () => {
          if(emailInput.value.length === 0 ||
            passwordInput.value.length === 0 
          ) {
            errMsg.innerText = "Please fill up all the required fields!";
            errorModal.show();
            return;
          }
          loginForm.submit();
        })
         
    </script>
</body>
</html>