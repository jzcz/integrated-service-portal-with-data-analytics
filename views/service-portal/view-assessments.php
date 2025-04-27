<?php 
    session_start();

    require(__DIR__ . "/../../queries/students.php");
    include(__DIR__ . "/../../config/utils.php");
    
    // check session first exists first
    if (!isset($_SESSION['studentId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Student') {
      header("location: ../service-portal/login.php");
      exit();
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
    <link rel="stylesheet" href="../../assets/css/service-portal.css">
</head>
<body>
    <?php 
        include(__DIR__ . '/../components/service-portal/navbar.php');
    ?>
    <main>
      <!--- VIEW ASSESSMENTS --->
      <div class="container my-5">
  <h2 class="va_title text-center fw-bold mb-3">Student Assessment</h2>
  <p><strong>Instruction:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua...</p>

  <!-- Legend -->
  <div class="d-flex justify-content-center align-items-center mb-4 flex-wrap gap-5">
  <label class="va-legend-label">
    <input type="radio" id="legend_sd" class="va_custom-radio sd" disabled>
    <span>Strongly Disagree</span>
  </label>
  <label class="va-legend-label">
    <input type="radio" id="legend_d" class="va_custom-radio d" disabled>
    <span>Disagree</span>
  </label>
  <label class="va-legend-label">
    <input type="radio" id="legend_n" class="va_custom-radio n" disabled>
    <span>Neutral</span>
  </label>
  <label class="va-legend-label">
    <input type="radio" id="legend_a" class="va_custom-radio a" disabled>
    <span>Agree</span>
  </label>
  <label class="va-legend-label">
    <input type="radio" id="legend_sa" class="va_custom-radio sa" disabled>
    <span>Strongly Agree</span>
  </label>
</div>

  <!-- Form Questions -->
  <form class="view_assessment-box">

    <!-- Question Block -->
    <div class="mb-4">
      <p><strong>1.</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
      <div class="d-flex justify-content-center gap-2">
        <label for="q1_sd"><input type="radio" id="q1_sd" name="q1" class="va_custom-radio sd" required></label>
        <label for="q1_d"><input type="radio" id="q1_d" name="q1" class="va_custom-radio d"></label>
        <label for="q1_n"><input type="radio" id="q1_n" name="q1" class="va_custom-radio n"></label>
        <label for="q1_a"><input type="radio" id="q1_a" name="q1" class="va_custom-radio a"></label>
        <label for="q1_sa"><input type="radio" id="q1_sa" name="q1" class="va_custom-radio sa"></label>
      </div>
    </div>

    <div class="va_question-separator"></div>

    <div class="mb-4">
      <p><strong>2.</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
      <div class="d-flex justify-content-center gap-2">
        <label><input type="radio" name="q2" class="va_custom-radio sd" required></label>
        <label><input type="radio" name="q2" class="va_custom-radio d"></label>
        <label><input type="radio" name="q2" class="va_custom-radio n"></label>
        <label><input type="radio" name="q2" class="va_custom-radio a"></label>
        <label><input type="radio" name="q2" class="va_custom-radio sa"></label>
      </div>
    </div>

    <div class="va_question-separator"></div>

    <div class="mb-4">
      <p><strong>3.</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
      <div class="d-flex justify-content-center gap-2">
        <label><input type="radio" name="q3" class="va_custom-radio sd" required></label>
        <label><input type="radio" name="q3" class="va_custom-radio d"></label>
        <label><input type="radio" name="q3" class="va_custom-radio n"></label>
        <label><input type="radio" name="q3" class="va_custom-radio a"></label>
        <label><input type="radio" name="q3" class="va_custom-radio sa"></label>
      </div>
    </div>

    <div class="va_question-separator"></div>

    <div class="mb-4">
      <p><strong>4.</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
      <div class="d-flex justify-content-center gap-2">
        <label><input type="radio" name="q4" class="va_custom-radio sd" required></label>
        <label><input type="radio" name="q4" class="va_custom-radio d"></label>
        <label><input type="radio" name="q4" class="va_custom-radio n"></label>
        <label><input type="radio" name="q4" class="va_custom-radio a"></label>
        <label><input type="radio" name="q4" class="va_custom-radio sa"></label>
      </div>
    </div>

    <div class="va_question-separator"></div>

    <div class="mb-4">
      <p><strong>5.</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
      <div class="d-flex justify-content-center gap-2">
        <label><input type="radio" name="q5" class="va_custom-radio sd" required></label>
        <label><input type="radio" name="q5" class="va_custom-radio d"></label>
        <label><input type="radio" name="q5" class="va_custom-radio n"></label>
        <label><input type="radio" name="q5" class="va_custom-radio a"></label>
        <label><input type="radio" name="q5" class="va_custom-radio sa"></label>
      </div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn va_submit-btn">SUBMIT</button>
    </div>

  </form>
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