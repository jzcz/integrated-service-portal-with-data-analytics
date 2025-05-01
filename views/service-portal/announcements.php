<?php
    session_start();

    // check session first exists first
    if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
        header("location: ../service-portal/login.php");
        exit();
    }

    require(__DIR__ . "/../../queries/students.php");
    include(__DIR__ . "/../../config/utils.php");

    // Database connection
    $db_conn = require(__DIR__ . "/../../db/db_conn.php");

    // Fetch announcements
    $announcementsQry = "SELECT * FROM announcements;";
    $stmt = $db_conn->prepare($announcementsQry);
    $stmt->execute();
    $result = $stmt->get_result();
    $announcements = $result->fetch_all(MYSQLI_ASSOC);

    $db_conn->close();
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
          <?php foreach ($announcements as $index => $announcement): ?>
              <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="true"></button>
          <?php endforeach; ?>
      </div>

      <div class="carousel-inner">
          <?php foreach (array_chunk($announcements, 3) as $index => $announcementChunk): ?>
              <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <div class="row gx-4 gy-4 justify-content-center">
                      <?php foreach ($announcementChunk as $announcement): ?>
                          <div class="col-md-4 d-flex justify-content-center">
                              <div class="card card-shadow">
                              <img 
                                src="<?= !empty($announcement['img_url']) ? htmlspecialchars('../../' . $announcement['img_url']) : '../../static/default_img.jpg'; ?>" 
                                class="card-img-top" 
                                alt="Announcement Image"
                                onerror="this.onerror=null; this.src='../../static/default_img.jpg';">

                                  <div class="card-body text-center">
                                      <h6 class="card-title"><u><?= htmlspecialchars($announcement['title']) ?></u></h6>
                                      <p class="card-text custom-font-size">
                                          <?= htmlspecialchars($announcement['description']) ?>
                                      </p>
                                      <a href="javascript:void(0);" 
   class="view-announcement-btn btn btn-sm" 
   data-bs-toggle="modal" 
   data-bs-target="#announcementModal" 
   data-img="<?= htmlspecialchars($announcement['img_url']) ?>" 
   data-description="<?= htmlspecialchars($announcement['description']) ?>">
   READ MORE
</a>
                                  </div>
                              </div>
                          </div>
                      <?php endforeach; ?>

                      <?php for ($i = count($announcementChunk); $i < 3; $i++): ?>
                          <div class="col-md-4 d-flex justify-content-center">
                              <div class="card card-shadow" style="visibility: hidden;"></div>
                          </div>
                      <?php endfor; ?>
                  </div>
              </div>
          <?php endforeach; ?>
      </div>

      <!-- Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Announcement Image" class="img-fluid mb-3">
                <p id="modalDescription" class="text-start"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('announcementModal');
    const modalImage = document.getElementById('modalImage');
    const modalDescription = document.getElementById('modalDescription');

    document.querySelectorAll('.view-announcement-btn').forEach(button => {
        button.addEventListener('click', function () {
            let imgSrc = this.getAttribute('data-img');
            const description = this.getAttribute('data-description');

            if (!imgSrc || imgSrc.trim() === "") {
                imgSrc = 'static/default_img.jpg';
            }

            modalImage.src = "../../" + imgSrc;
            modalImage.onerror = function() {
                this.onerror = null;
                this.src = '../../static/default_img.jpg';
            };

            modalDescription.innerHTML = description;
        });
    });

    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.onerror = function () {
            this.onerror = null;
            this.src = '../../static/default_img.jpg';
        };
    });

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
