<!--- TOP NAVBAR (START) --->
<nav class="navbar fixed-top">
    <div class="container-fluid">
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebar"
        >
            <span class="navbar-toggler-icon" data-bs-target="#sidebar"></span>
        </button>
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="../../static/qcu-logo.jpg" alt="Logo" width="45" height="45" class="d-inline-block align-text-top">
        <span class="navbar-brand mb-0 h1 align-self-center">QCU Guidance and Counseling Unit</span>
        </a>           
    </div>
</nav>
<!--- TOP NAVBAR (END) --->

<!--- SIDEBAR (START) --->
<div class="sidebar" tabindex="-1" id="sidebar" aria-labelledby="offcanvasLabel">
    <div class="sidebar-body">
        <nav class="">
            <ul class="navbar-nav flex-column">
                <li class="fw-bold nav-link-title">General</li>
                <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2 d-flex align-items-center gap-2">
                    <i class="bi bi-megaphone-fill"></i>
                    <a class="nav-link active" aria-current="page" href="../service-portal/announcements.php">Announcements</a>
                </li>
                <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                    <i class="bi bi-book-half"></i>
                    <a class="nav-link active" aria-current="page" href="../service-portal/pub-mats.php">Publication Materials</a>
                </li>
                <li class="my-1"><hr /></li>
                <li class="fw-bold nav-link-title">Services</li>
                <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-fill"></i>
                    <a class="nav-link active" aria-current="page" href="../service-portal/appointments.php">Appointment Form</a>
                </li>
                <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-fill"></i>
                    <a class="nav-link active" aria-current="page" href="../service-portal/assessment.php">Assessments</a>
                </li>
                <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                    <i class="bi bi-ui-radios-grid"></i>
                    <a class="nav-link active" aria-current="page" href="../service-portal/student-survey.php">Surveys</a>
                </li>
                <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-fill"></i>
                    <a class="nav-link active" aria-current="page" href="../service-portal/good-moral-cert-req-form.php">Good Moral Certificate Form</a>
                </li>
                <li class="my-1"><hr /></li>
                <li class="fw-bold nav-link-title">Account</li>
                <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                    <i class="bi bi-person-fill"></i>
                    <a class="nav-link active" aria-current="page" href="../service-portal/student-profile.php">Profile</a>
                </li>
                <li class="py-2 nav-item sidebar-nav-item d-flex align-items-center gap-2">
                    <i class="bi bi-power"></i>
                    <button type="button" class="logout-btn w-100 text-start" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Logout
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!--- SIDEBAR (END) --->

<!--- LOGOUT MODAL START --->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">
            Log Out?
        </h1>
      </div>
      <div class="modal-body border-0">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        <a href=""><button type="button" class="btn btn-primary">Log Out</button></a>
      </div>
    </div>
  </div>
</div>
<!--- LOGIN MODAL END --->