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
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: auto !important;
            background-color: white;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <?php
        include(__DIR__ . '/../components/service-portal/navbar.php');
    ?>
  <main>
    <form>
        <div class="form-header-container">
            <h1 class="p-5"> Good Moral Certificate
               <br>Request Form </h1>

        </div>
        <div class="request-form-text-container">
            <h5 class="text-black text-left p-4 pb-1"><b>Request Form</b></h5>
            <p class="text-black text-left p-5 pt-1 pb-3">Please fill out the form with your information.</p>
        </div>
        
        <div class="p-5 pt-1">
            
                <div class="row">
                    <div class="col-md-3 mb-2">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" placeholder="First Name"
                                style="width: 100%;">
                    </div>
                    <div class="col-md-3 mb-2">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" placeholder="Middle Name"
                                style="width: 100%;">
                    </div>
                    <div class="col-md-3 mb-2">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" placeholder="Last Name"
                                style="width: 100%;">`  
                    </div>
                    <div class="col-md-2 mb-2">
                            <label for="suffix" class="form-label">Suffix</label>
                            <input type="text" class="form-control" id="suffix" placeholder="Suffix" style="width: 100%;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                            <label for="studentNo" class="form-label">Student No.</label>
                            <input type="text" class="form-control" id="studentNo" placeholder="Ex.: 22-0000"
                                style="width: 100%;">
                    </div>
                    <div class="col-md-6 mb-3">
                            <label for="program" class="form-label">Program</label>
                            <input type="text" class="form-control" id="program" placeholder="Program"
                                style="width: 100%;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                            <label for="enrollmentStatus" class="form-label">Enrollment Status</label>
                            <input type="text" class="form-control" id="enrollmentStatus" placeholder="Enrollment Status"
                                style="width: 100%;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                            <label for="startingSchoolYear" class="form-label">Starting School Year</label>
                            <input type="text" class="form-control" id="startingSchoolYear" placeholder="School Year"
                                style="width: 100%;">
                    </div>
                    <div class="col-md-4 mb-3">
                            <label for="lastSchoolYear" class="form-label">Last School Year</label>
                            <input type="text" class="form-control" id="lastSchoolYear" placeholder="School Year"
                                style="width: 100%;">
                    </div>
                    <div class="col-md-4 mb-3">
                            <label for="lastSemesterAttended" class="form-label">Last Semester Attended</label>
                            <input type="text" class="form-control" id="lastSemesterAttended" placeholder="Semester"
                                style="width: 100%;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reasonForRequest" class="form-label">Reason for Request</label>
                    <input type="text" class="form-control" id="reasonForRequest" placeholder="Reason">
                </div>

                <div class="mb-3 specify-reason-row">
                    <label for="specifyReason" class="form-label text-muted">
                        *Specify if reason is not included in the options</label>
                    <textarea class="form-control" id="specifyReason" rows="3" placeholder="Specify reason"></textarea>
                </div>

                <div class="mb-3">
                    <label for="proofOfImage" class="form-label">Proof of Image</label>
                    <input type="file" class="form-control" id="proofOfImage">
                    <p class="text-secondary">Please upload an image that verifies you are a current or former QCU
                        student (Student ID, Registration Form, or any valid ID).
                    </p>

                    <div class="image-upload-area">
                        <div class="upload-icon">
                            <i class="bi bi-cloud-upload"></i>
                        </div>
                        <p class="upload-text">Upload an image</p>
                    </div>

                    <div class="form-check text-secondary mt-3">
                        <input class="form-check-input" type="checkbox" value="" id="consentCheckbox">
                        <label class="form-check-label" for="consentCheckbox">
                            I agree to voluntarily consent to the collection, processing, and use of my personal information as
                            outlined in the
                            <a href="#" target="_blank">Data Privacy Policy</a> by the QCU Guidance & Counseling Unit.
                        </label>
                    </div>
                    <div class="text-left pb-5 mt-4">
                        <button type="submit" class="btn btn-primary custom-submit-button">Submit</button> 
                        
                    </div>
                </div>  
        </div>
    </form>
    <footer>
        <div class="form-title custom-footer-color text-white pb-3 pt-3">
            <p class="text-center mb-0">© 2024 QCU Guidance and Counseling. All rights reserved.</p>
        </div>
    </footer>
        </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 </body>
</html> 