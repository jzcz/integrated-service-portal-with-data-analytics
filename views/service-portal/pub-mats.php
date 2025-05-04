<?php 

    require(__DIR__ . "/../../queries/students.php");
    include(__DIR__ . "/../../config/utils.php");

    // Database connection
    $db_conn = require(__DIR__ . "/../../db/db_conn.php");

    // Pagination setup
    $limit = 6; 
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $sql = "SELECT pub_mat_id, file_title, file_desc, file_url, cover_img_url, type 
            FROM publication_materials 
            ORDER BY pub_mat_id DESC 
            LIMIT $limit OFFSET $offset";
    $result = $db_conn->query($sql);

    $pubMats = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pubMats[] = $row;
        }
    }

    $totalResult = $db_conn->query("SELECT COUNT(*) as total FROM publication_materials");
    $totalRow = $totalResult->fetch_assoc();
    $totalPubMats = $totalRow['total'];
    $totalPages = ceil($totalPubMats / $limit);

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
<?php include(__DIR__ . '/../components/service-portal/navbar.php'); ?>
<main>
    <h2 class="text-center mb-4 fw-bold custom-blue pt-3">Publication Materials</h2>
    <div class="container mt-4">
    <div class="d-flex justify-content-center">
        <div class="row row-cols-1 row-cols-md-3 g-5 justify-content-center">
            <?php if (!empty($pubMats)): ?>
                <?php foreach ($pubMats as $pubMat): ?>
                    <div class="col col-md-4 mx-auto">
                        <div class="pb-card-modules card-shadow">
                            <img src="<?php echo (!empty($pubMat['cover_img_url'])) ? htmlspecialchars($pubMat['cover_img_url']) : '../../static/default_img.jpg'; ?>" 
                            class="pb-img card-img-top img-fluid" 
                            alt="Cover Image">
                            <div class="card-body pb-card-body">
                                <h5 class="card-title text-center fw-bold custom-blue">
                                    <?php echo htmlspecialchars($pubMat['file_title']); ?>
                                </h5>
                                <p class="card-text pb-card-text text-center">
                                    <?php 
                                    $shortDesc = (strlen($pubMat['file_desc']) > 150) ? substr(strip_tags($pubMat['file_desc']), 0, 170) . '...' : strip_tags($pubMat['file_desc']);
                                    echo nl2br(htmlspecialchars($shortDesc)); 
                                    ?>
                                </p>
                                <div class="text-center">
                                    <a href="#" 
                                    class="module-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewPubMatModal"
                                    data-cover-img="<?php echo (!empty($pubMat['cover_img_url'])) ? htmlspecialchars($pubMat['cover_img_url']) : '../../static/default_img.jpg'; ?>"
                                    data-title="<?php echo htmlspecialchars($pubMat['file_title']); ?>"
                                    data-description="<?php echo htmlspecialchars($pubMat['file_desc']); ?>"
                                    data-url="<?php echo htmlspecialchars($pubMat['file_url']); ?>">
                                    View Material
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No Publication Materials Found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewPubMatModal" tabindex="-1" aria-labelledby="viewPubMatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold custom-blue" id="viewPubMatModalLabel">Publication Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="modalCoverImg" src="" alt="Cover Image" class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
                </div>
                <h5 id="modalTitle" class="text-center fw-bold custom-blue"></h5>
                <p id="modalDescription" class="text-center"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <a id="modalViewButton" href="#" target="_blank" class="btn btn-primary">View</a>
            </div>
        </div>
    </div>
</div>

    <!-- Pagination -->
    <nav aria-label="Page navigation example" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous Button -->
            <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page-1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next Button -->
            <li class="page-item <?php if($page >= $totalPages) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page+1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebarNavItems = document.querySelectorAll('.nav-item.sidebar-nav-item');
    sidebarNavItems.forEach(item => {
        const link = item.querySelector('a');
        if (link && link.textContent.trim() === 'Publication Materials') {
            item.classList.add('active');
        }
    });

    const viewPubMatModal = document.getElementById('viewPubMatModal');
    viewPubMatModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const coverImgAttr = button.getAttribute('data-cover-img');
        const coverImg = (coverImgAttr && coverImgAttr.trim() !== '') ? coverImgAttr : '../../static/default_img.jpg';
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');
        const url = button.getAttribute('data-url');

        viewPubMatModal.querySelector('#modalCoverImg').src = coverImg;
        viewPubMatModal.querySelector('#modalTitle').textContent = title;

        viewPubMatModal.querySelector('#modalDescription').innerHTML = description.replace(/<\/?[^>]+(>|$)/g, "").replace(/\n/g, '<br>'); 

        viewPubMatModal.querySelector('#modalViewButton').href = url;
    });

    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.onerror = function () {
            this.src = '../../static/default_img.jpg';
        };
    });
});
</script>
 </body>
</html>

