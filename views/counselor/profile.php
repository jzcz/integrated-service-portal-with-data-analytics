<?php 
    session_start();

    include(__DIR__ . "/../../config/utils.php");
    
    // check session first exists first
    if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
      header("location: ../public/counselor-admin-login-page.php");
      exit();
    }

    $db_conn = require( __DIR__ . "/../../db/db_conn.php");

    $counselorId = $_SESSION['counselorId'];

    function getCounselorProfile($db_conn, $counselorId) {
        $sql = "SELECT * FROM counselors JOIN user ON counselors.user_id = user.user_id WHERE counselor_id = ?;";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("i", $counselorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    $counselorProfile = getCounselorProfile($db_conn, $counselorId);
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
  <div class="container  d-flex flex-column align-items-center">
    <h2 class="text-primary-emphasis text-center pt-5 mb-3 counselor-profile-header"><b>COUNSELOR'S PROFILE</b></h2>
    <div class="text-center mb-3">
      <img src="../../static/profile1.png" alt="Profile Icon" width="150" height="150">
      <h3 class="pt-3"><?php echo $counselorProfile['first_name'] . ' ' . $counselorProfile['last_name']; ?></h3>
      <p class="text-muted"><?php echo $counselorProfile['role']; ?></p>
    </div>
    <div class="card bg-white counselor-details-card" style="max-width: 500px; min-height: 200px; border-radius: 0; margin-bottom: 16rem;">
     <div class="card-body py-2 px-3">  
      <form>
         <div class="row gx-1 align-items-center">
           <label for="counselorEmployeeID" class="col-md-5 col-form-label fw-bold smaller">EMPLOYEE ID</label>
           <div class="col-md-7 text-end">
             <input type="text" class="form-control-plaintext smaller text-end" id="counselorEmployeeID" value="<?php echo $counselorProfile['employee_id']; ?>" readonly>
           </div>
         </div>
         <div class="row gx-1 align-items-center">
           <label for="counselorQcuEmail" class="col-md-5 col-form-label fw-bold smaller">QCU Email</label>
           <div class="col-md-7 text-end">
             <input type="email" class="form-control-plaintext smaller text-end" id="counselorQcuEmail" value="<?php echo $counselorProfile['email']; ?>" readonly>
           </div>
         </div>
         <div class="row gx-1 align-items-center">
           <label for="counselorCampus" class="col-md-5 col-form-label fw-bold smaller">Campus</label>
           <div class="col-md-7 text-end">
             <input type="text" class="form-control-plaintext smaller text-end" id="counselorCampus" value="Main Campus - San Bartolome" readonly>
           </div>
         </div>
       </div>  
      </div>
   </div>

   <style>
.counselor-details-card .row {
  position: relative;
  padding: 12px 0;
  border-bottom: 1px solid lightgray;
}

.counselor-details-card .row:last-child {
  border-bottom: none;
}


   </style>
       </form>
       </div>  
      </div>

  
  <footer>
    <div class="form-title custom-footer-color text-white pb-3 pt-3">
      <p class="text-center mb-0">© 2024 QCU Guidance and Counseling. All rights reserved.</p>
    </div>
  </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html> 