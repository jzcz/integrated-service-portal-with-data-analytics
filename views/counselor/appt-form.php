<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../../assets/css/global.css">
        <link rel="stylesheet" href="../../assets/css/counselor.css">
        
        <style>
        .appt-header-bar {
            width: 100%;
            background-color:rgb(222, 237, 251);
            border-bottom: 2px #9DCEFF solid;
        }
        .appt-header-bar h5{
            color: var(--primary-color);
        }

        .btn-primary {
          background-color: #004085;
          color: white;
          border: none;
          width: 200px;
        }

        .btn-primary:hover {
          background-color: #002752;
        }
        </style>
    </head>
    <body>
    <?php    
        include(__DIR__ . '/../components/counselor/sidebar.php');
    ?>

    <main>
        <div class="appt-header-bar px-4 py-3 d-flex align-items-center mb-4">
        <h5 class="mb-0 fw-bold">Appointments</h5>
        </div>

        <div class="px-4">

  <form action="" method="POST">
    <h6 class="fw-bold mb-3">Appointment's Detail</h6>

    <!-- First Row -->
    <div class="row mb-3">
      <div class="col">
        <label for="firstName" class="form-label fw-bold">First Name</label>
        <input type="text" id="firstName" name="firstName" class="form-control" value="Mark" />
      </div>

      <div class="col">
        <label for="lastName" class="form-label fw-bold">Last Name</label>
        <input type="text" id="lastName" name="lastName" class="form-control" value="Fahardo" />
      </div>

      <div class="col">
        <label for="middleName" class="form-label fw-bold">Middle Name</label>
        <input type="text" id="middleName" name="middleName" class="form-control" value="N/A" />
      </div>

      <div class="col">
        <label for="suffix" class="form-label fw-bold">Suffix</label>
        <select name="suffix" id="suffix" class="form-select">
          <option value="">N/A</option>
          <option value="Sr">Sr</option>
          <option value="Jr">Jr</option>
          <option value="III">III</option>
          <option value="IV">IV</option>
        </select>
      </div>

      <div class="col">
        <label for="studentNum" class="form-label fw-bold">Student Number</label>
        <input type="text" id="studentNum" name="studentNum" class="form-control" value="00-0000" />
      </div>
    </div>

    <!-- Second Row -->
    <div class="row mb-3">
      <div class="col">
        <label for="program" class="form-label fw-bold">Program</label>
        <select name="program" id="program" class="form-select">
          <option value="BSIT">Bachelor of Science in Information Technology</option>
          <option value="BSCS">Bachelor of Science in Computer Science</option>
        </select>
      </div>

      <div class="col">
        <label for="year-level" class="form-label fw-bold">Year Level</label>
        <select name="year-level" id="year-level" class="form-select">
          <option value="1st">1st Year</option>
          <option value="2nd">2nd Year</option>
          <option value="3rd">3rd Year</option>
          <option value="4th">4th Year</option>
        </select>
      </div>

      <div class="col">
        <label for="contactNum" class="form-label fw-bold">Personal Contact Number</label>
        <input type="text" id="contactNum" name="contactNum" class="form-control" value="+63 91023456789" />
      </div>
    </div>

    <!-- Email -->
    <div class="mb-3">
      <label for="email" class="form-label fw-bold">QCU Email Address</label>
      <input type="email" id="email" name="email" class="form-control" value="fahardo.mark@gmail.com" />
    </div>

    <!-- Guardian Info -->
    <div class="row mb-3">
      <div class="col">
        <label for="guardianName" class="form-label fw-bold">Guardian's Name</label>
        <input type="text" id="guardianName" name="guardianName" class="form-control" value="Cynica Santos" />
      </div>

      <div class="col">
        <label for="guardianContact" class="form-label fw-bold">Guardian's Contact Number</label>
        <input type="text" id="guardianContact" name="guardianContact" class="form-control" value="+63 9784435632" />
      </div>
    </div>

    <!-- Counseling Schedule -->
    <div class="row mb-3">
  <div class="col">
    <label for="counselConcern" class="form-label fw-bold">Counseling Concern</label>
    <select name="counselConcern" id="counselConcern" class="form-select">
      <option value="Academic">Academic</option>
      <option value="Career">Career</option>
      <option value="Personal">Personal</option>
    </select>
  </div>

  <div class="col">
    <label for="startTime" class="form-label fw-bold">Start Time</label>
    <input type="text" id="startTime" name="startTime" class="form-control" value="9:00 AM" />
  </div>

  <div class="col">
    <label for="endTime" class="form-label fw-bold">End Time</label>
    <input type="text" id="endTime" name="endTime" class="form-control" value="4:00 PM" />
  </div>

  <div class="col">
    <label for="counselingDate" class="form-label fw-bold">Preferred Date</label>
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
      <input type="date" id="counselingDate" name="counselingDate" class="form-control" />
    </div>
  </div>
</div>
</div>

    <!-- Submit -->
    <div class="text-center mt-4">
      <button type="submit" value="submit" class="btn btn-primary px-4">Save</button>
    </div>
  </form>
</div>
