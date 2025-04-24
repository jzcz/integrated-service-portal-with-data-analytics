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
      <h3 class="pt-3">Rachel L. Jungco</h3>
      <p class="text-muted">Officer-in-Charge</p>
    </div>
    <div class="card bg-white counselor-details-card" style="max-width: 500px; min-height: 200px; border-radius: 0; margin-bottom: 16rem;">
     <div class="card-body py-2 px-3">  
      <form>
         <div class="row gx-1 align-items-center">
           <label for="counselorEmployeeID" class="col-md-5 col-form-label fw-bold smaller">EMPLOYEE ID</label>
           <div class="col-md-7 text-end">
             <input type="text" class="form-control-plaintext smaller text-end" id="counselorEmployeeID" value="XXXXX" readonly>
           </div>
         </div>
         <div class="row gx-1 align-items-center">
           <label for="counselorQcuEmail" class="col-md-5 col-form-label fw-bold smaller">QCU Email</label>
           <div class="col-md-7 text-end">
             <input type="email" class="form-control-plaintext smaller text-end" id="counselorQcuEmail" value="example@email.com" readonly>
           </div>
         </div>
         <div class="row gx-1 align-items-center">
           <label for="counselorBirthdate" class="col-md-5 col-form-label fw-bold smaller">Birthdate</label>
           <div class="col-md-7 text-end">
             <input type="text" class="form-control-plaintext smaller text-end" id="counselorBirthdate" value="February 30, 2000" readonly>
           </div>
         </div>
         <div class="row gx-1 align-items-center">
           <label for="counselorGender" class="col-md-5 col-form-label fw-bold smaller">Gender</label>
           <div class="col-md-7 text-end">
             <input type="text" class="form-control-plaintext smaller text-end" id="counselorGender" value="Female" readonly>
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