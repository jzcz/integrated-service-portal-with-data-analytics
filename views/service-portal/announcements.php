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
    <img src="../../static/qcu acad.bldg.png" class="bg" alt="...">
    <!--- ANNOUNCEMENT --->
    <div class="container my-3">
    <h2 class="text-center mb-4 fw-bold custom-blue">Announcements</h2>
    <div class="container mt-4">
    <div id="carouselExampleIndicators" class="carousel slide">
        
    <!-- INDICATORS -->
      <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
      </div>

      <div class="carousel-inner"> 
          <!-- BATCH 1 -->
          <div class="carousel-item active">
              <div class="row gx-4 gy-4 justify-content-center"> <div class="col-md-4 col-sm-6 d-flex justify-content-center">
                      <div class="card card-shadow">
                            <img src="../../static/announ1.jpg" class="card-img-top">
                            <div class="card-body text-center">
                                <h9 class="card-title"><u>Quezon City Scholarship Program (QCYDO) Onsite Application Assistance & Renewal Enlistment</u></h9>
                                <p class="card-text custom-font-size">
                                March 29, 2024 <br>
                                Good news! The Quezon City Youth Development Office will go to the Main Campus of Quezon City University at San Bartolome, Novaliches to conduct onsite assistance to QCU Students. This will be held on 𝗔𝗽𝗿𝗶𝗹 𝟮-𝟯, 𝟮𝟬𝟮𝟰, from 8 AM to 5 PM at the QCU, San Bartolome Campus, TechVoc GYM…
                                </p>
                                <a href="#" class="view-announcement-btn btn btn-sm">READ MORE</a>
                            </div>
                       </div>
               </div>

                    <div class="col-md-4 col-sm-6 d-flex justify-content-center">
                        <div class="card card-shadow">
                            <img src="../../static/announ2.jpg" class="card-img-top">
                            <div class="card-body text-center">
                                <h6 class="card-title"><u>Mental Health Check-In for QCU Students</u></h6>
                                <p class="card-text custom-font-size">
                                January 30, 2025 <br>  
                                FOR: All Year Levels <br>     
                                The Quezon City University (QCU) Guidance Unit invites all students to participate in a quick and confidential Mental Health Check-In. This initiative aims to provide psychological support to students experiencing mental health issues or emotional distress...
                                </p>
                                <a href="#" class="view-announcement-btn btn btn-sm">READ MORE</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 d-flex justify-content-center">
                        <div class="card card-shadow">
                            <img src="../../static/announ3.jpg" class="card-img-top">
                            <div class="card-body text-center">
                                <h6 class="card-title"><u>QCU Guidance Office Launches Online Psychological Testing for 1st and 2nd Year Students (AY 2024-2025) on All Campuses</u></h6>
                                <p class="card-text custom-font-size">
                                December 11, 2024 <br>
                                FOR: All 1st and 2nd year QCians on all campuses are required to take the online psychological test.
                                The Guidance & Counseling Unit of Quezon City University (QCU) is launching the Online Psychological Testing for all 1st and 2nd-year QCians across all...
                                </p>
                                <a href="#" class="view-announcement-btn btn btn-sm">READ MORE</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BATCH 2 -->
            <div class="carousel-item">
                <div class="row gx-4 gy-4 justify-content-center">
                    <div class="col-md-4 col-sm-6 d-flex justify-content-center">
                        <div class="card card-shadow">
                            <img src="../../static/announ1.jpg" class="card-img-top">
                            <div class="card-body text-center">
                                <h6 class="card-title"><u>QCU Guidance Office Launches Online Psychological Testing for 1st and 2nd Year Students (AY 2024-2025) on All Campuses</u></h6>
                                <p class="card-text custom-font-size">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                </p>
                                <a href="#" class="view-announcement-btn btn btn-sm">READ MORE</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 d-flex justify-content-center">
                        <div class="card card-shadow">
                            <img src="../../static/announ2.jpg" class="card-img-top">
                            <div class="card-body text-center">
                                <h6 class="card-title"><u>QCU Guidance Office Launches Online Psychological Testing for 1st and 2nd Year Students (AY 2024-2025) on All Campuses</u></h6>
                                <p class="card-text custom-font-size">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                </p>
                                <a href="#" class="view-announcement-btn btn btn-sm">READ MORE</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 d-flex justify-content-center">
                        <div class="card card-shadow">
                            <img src="../../static/announ3.jpg" class="card-img-top">
                            <div class="card-body text-center">
                                <h6 class="card-title"><u>QCU Guidance Office Launches Online Psychological Testing for 1st and 2nd Year Students (AY 2024-2025) on All Campuses</u></h6>
                                <p class="card-text custom-font-size">Description</p>
                                <a href="#" class="view-announcement-btn btn btn-sm">READ MORE</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- NAVIGATION -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>

    </div>
</div>
    </main>
    <!--- ACTIVE PAGE HIGHLIGHT --->
    <script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebarNavItems = document.querySelectorAll('.nav-item.sidebar-nav-item');
  sidebarNavItems.forEach(item => {
    const link = item.querySelector('a');
    if (link && link.textContent.trim() === 'Announcements') {
      item.classList.add('active');
    }
  });
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>
