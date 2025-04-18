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
    <link rel="stylesheet" href="../../assets/css/service-portal.css">
</head>
<body>
    <?php 
        include(__DIR__ . '/../components/service-portal/navbar.php');
    ?>
    <main>
      <!--- BG IMAGE --->
      <div class="assessment-banner">
      <!--- ASSESSMENTS --->
    <div class="assessmenttitle-text">
      Student Assessment
    </div>
  </div>
      <!--- ASSESSMENTS CARDS --->
      <div class="container my-5">
  <div class="row justify-content-center g-4 mt-4">
    
  <div class="col-md-4 assessmentcol col-sm-6 d-flex justify-content-center h-100">
  <div class="card-assessment text-center h-100 d-flex flex-column p-3">
    <div class="assessmentcard-icon mb-3"> <img src="../../static/inprogress.png"> </div>
    <p class="flex-grow-1 assessmentp" >Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut...</p>
    
    <div class="d-flex justify-content-center mt-auto">
      <a href="#" class="btn assessmentbtn btn-primary">Start</a>
    </div>
  </div>
</div>

  <div class="col-md-4 assessmentcol col-sm-6 d-flex justify-content-center h-100">
  <div class="card-assessment text-center h-100 d-flex flex-column p-3">
      <div class="assessmentcard-icon mb-3"> <img src="../../static/done.png"> </div>
      <p class="flex-grow-1" >Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut...</p>

      <div class="d-flex justify-content-center mt-auto">
        <a href="#" class="btn assessmentbtn btn-primary">View Response</a>
    </div>
  </div>
</div>

  <div class="col-md-4 assessmentcol col-sm-6 d-flex justify-content-center h-100">
  <div class="card-assessment text-center h-100 d-flex flex-column p-3">
      <div class="assessmentcard-icon mb-3"> <img src="../../static/inprogress.png"> </div>
      <p class="flex-grow-1" >Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut...</p>

      <div class="d-flex justify-content-center mt-auto">
        <a href="#" class="btn assessmentbtn btn-primary">Start</a>
    </div>
  </div>
</div>
</div>
</div>

    
    </main>
    <!--- ACTIVE PAGE HIGHLIGHT --->
    <script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebarNavItems = document.querySelectorAll('.nav-item.sidebar-nav-item');
  sidebarNavItems.forEach(item => {
    const link = item.querySelector('a');
    if (link && link.textContent.trim() === 'Assessments') {
      item.classList.add('active');
    }
  });
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>