
<!--- SIDE NAVBAR START --->
<div class="pt-4" id="sidebar">
    <div class="sidebar-body d-flex flex-column gap-2">
        <div class="sidebar-header text-center d-flex flex-column align-items-center gap-1">
            <img src="../../static/qcu-logo-login.png" class="" alt="" width="75px" height="75  px">
            <p class="fw-bold text-wrap text-center">QCU Guidance and Counseling Unit</p>
        </div>
        <div>
            <nav>
                <ul class="navbar-nav flex-column">
                    <li class="fw-bold nav-link-title">General</li>
                    <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2 d-flex align-items-center gap-2">
                        <i class="bi bi-megaphone-fill"></i>
                        <a class="nav-link active" aria-current="page" href="">Announcements</a>
                    </li>
                    <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                        <i class="bi bi-book-half"></i>
                        <a class="nav-link active" aria-current="page" href="#">Publication Materials</a>
                    </li>
                    <li class="my-1"><hr /></li>
                    <li class="fw-bold nav-link-title">Services</li>
                    <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-fill"></i>
                        <a class="nav-link active" aria-current="page" href="#">Appointments</a>
                    </li>
                    <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-fill"></i>
                        <a class="nav-link active" aria-current="page" href="#">Assessments</a>
                    </li>
                    <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                        <i class="bi bi-ui-radios-grid"></i>
                        <a class="nav-link active" aria-current="page" href="#">Surveys</a>
                    </li>
                    <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-fill"></i>
                        <a class="nav-link active" aria-current="page" href="#">Good Moral Certificate Requests</a>
                    </li>
                    <li class="my-1"><hr /></li>
                    <li class="fw-bold nav-link-title">Account</li>
                    <li class="nav-item sidebar-nav-item d-flex align-items-center gap-2">
                        <i class="bi bi-person-fill"></i>
                        <a class="nav-link active" aria-current="page" href="#">Profile</a>
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
</div>    
<!--- SIDE NAVBAR END --->

<!--- LOGIN MODAL START --->
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