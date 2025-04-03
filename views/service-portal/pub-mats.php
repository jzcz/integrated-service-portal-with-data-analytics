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
    <!--- MODULES --->
    <h2 class="text-center mb-4 fw-bold custom-blue pt-3">Publication Materials</h2>
<div class="container mt-4">
    <div class="d-flex justify-content-center">
        <div class="row row-cols-1 row-cols-md-3 g-5 justify-content-center">
            <div class="col col-md-4 mx-auto">
                <div class="pb-card-modules card-shadow">
                    <img src="../../static/module1.png" class="pb-img card-img-top img-fluid">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold custom-blue">Module Title</h5>
                        <p class="card-text text-center">Description</p>
                        <div class="text-center">
                            <a href="#" class="module-btn">View Module</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col col-md-4 mx-auto">
                <div class="pb-card-modules card-shadow">
                    <img src="../../static/module2.png" class="pb-img card-img-top img-fluid">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold custom-blue">Module Title</h5>
                        <p class="card-text text-center">Description</p>
                        <div class="text-center">
                            <a href="#" class="module-btn">View Module</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col col-md-4 mx-auto">
                <div class="pb-card-modules card-shadow">
                    <img src="../../static/module3.png" class="pb-img card-img-top img-fluid">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold custom-blue">Module Title</h5>
                        <p class="card-text text-center">Description</p>
                        <div class="text-center">
                            <a href="#" class="module-btn">View Module</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col col-md-4 mx-auto">
                <div class="pb-card-modules card-shadow">
                    <img src="../../static/module4.png" class="pb-img card-img-top img-fluid">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold custom-blue">Module Title</h5>
                        <p class="card-text text-center">Description</p>
                        <div class="text-center">
                            <a href="#" class="module-btn">View Module</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col col-md-4 mx-auto">
                <div class="pb-card-modules card-shadow">
                    <img src="../../static/module5.png" class="pb-img card-img-top img-fluid">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold custom-blue">Module Title</h5>
                        <p class="card-text text-center">Description</p>
                        <div class="text-center">
                            <a href="#" class="module-btn">View Module</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col col-md-4 mx-auto">
                <div class="pb-card-modules card-shadow">
                    <img src="../../static/module6.png" class="pb-img card-img-top img-fluid">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold custom-blue">Module Title</h5>
                        <p class="card-text text-center">Description</p>
                        <div class="text-center">
                            <a href="#" class="module-btn">View Module</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<nav aria-label="Page navigation example" class="mt-4">
    <ul class="pagination justify-content-center">
      <a class="page-link" href="#" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item">
      <a class="page-link" href="#" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>
    </main>
    <!--- ACTIVE PAGE HIGHLIGHT --->
    <script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebarNavItems = document.querySelectorAll('.nav-item.sidebar-nav-item');
  sidebarNavItems.forEach(item => {
    const link = item.querySelector('a');
    if (link && link.textContent.trim() === 'Publication Materials') {
      item.classList.add('active');
    }
  });
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html>