<?php 
    $db_conn = require(__DIR__ . "/../../db/db_conn.php");
    require(__DIR__ . "/../../queries/students.php");
    include(__DIR__ . "/../../config/utils.php");
    
    $err = "";
    $programs = getAllPrograms($db_conn);

    if(isset($_GET['err'])) { 
        $err = $_GET['err']; 
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check first if student already exists in the database
        $existingstudNo = getStudentByStudNo($db_conn, $_POST['studentNo']);
        $existingStudEmail = getStudentByEmail($db_conn, $_POST['email']);

        if($existingstudNo) {
            // If student exists, reload page with an error message
            header("location: " . $_SERVER["PHP_SELF"] . "?err=Student with Student No of ". $_POST['studentNo'] . " already exists. Please log in instead.");
        } else if ($existingStudEmail) {
            header("location: " . $_SERVER["PHP_SELF"] . "?err=Student with an email of ". $_POST['email'] . " already exists. Please log in instead.");
        } else {
            $password = sanitizeData($db_conn, $_POST['password']);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // else save student on database
            $studentInfo = array(
                'studentNo' => sanitizeData($db_conn, $_POST['studentNo']),
                'firstName' => sanitizeData($db_conn, $_POST['firstName']),
                'lastName' => sanitizeData($db_conn, $_POST['lastName']),
                'gender' => sanitizeData($db_conn, $_POST['gender']),
                'birthDate' => sanitizeData($db_conn, $_POST['birthDate']),
                'programId' => sanitizeData($db_conn, $_POST['program']),
                'yearLevel' => sanitizeData($db_conn, $_POST['yearLevel']),
                'campus' => sanitizeData($db_conn, $_POST['campus']),
                'email' => sanitizeData($db_conn, $_POST['email']),
                'agreedToTermsConditions' => $_POST['termsAndConditions'],
                'password' => $hashedPassword,
            );

            if(array_key_exists("middleName", $_POST)) {
                $studentInfo['middleName'] = sanitizeData($db_conn, $_POST['middleName']);
            }

            if(array_key_exists("suffix", $_POST)) {
                $studentInfo['suffix'] = sanitizeData($db_conn, $_POST['suffix']);
            }

            $newStud = addNewStudentAcc($db_conn, $studentInfo); 

            if($newStud) {
                header("Location: " .  '../service-portal/login.php?success=Successfully created acccount! Please login to access the service portal.');
            } else {
                header("Location: " . $_SERVER["PHP_SELF"] . "?err=Failed to create an account. Please try again later.");
            }
        }
    }
    $db_conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>
        body{
            max-height: 100vh;
            max-width: 100vw;
            color: black;
        }
                /** Custom Styles for Sign-Up Page */

.signup-form-box {
  width: 700px;
}

.signup-form-title {
  background-color: #08437F;
  color: white;
  padding: 3px 0;
}

.signup-form-title p {
  margin: auto;
}

.sliding-form label {
  font-size: 14px;
  color:rgb(75, 75, 75)
}

.signup-input, option {
  font-size: 14px;
}  

.form-text-header {
  font-size: 18px;
}

.sign-up-form-wrapper{
  width: 100%;
}

.signup-form-wrapper {
  background-color: white;
}

.signup-next-btn {
  padding: 8px 26px
}

.hidden {
  display: none;
}

.sign-up-bg-img-container {
  position: absolute;
  height:100%;
  width: 100%;
  z-index: -99;
  overflow: hidden;
}

.sign-up-bg-img-container img {
  object-fit: cover;
  height: 100%;
  width: 100%;
}

.signup-input {
  height: 32px;
}

.err-modal .modal-header {
  border-bottom: none;
}

.err-modal .modal-content {
  border: solid 2px rgb(233, 110, 110);
}

.input-note-text {
  font-size: 12px;
}

.stmtModalBtn {
  color:rgb(34, 90, 221);
  text-decoration: underline;
  cursor: pointer;
}

.required-text {
  font-size: 12px;
  color: rgb(43, 96, 164);
}

/** End of Custom Styles for Sign-Up Page */

    </style>
</head>
<body>
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title text-white fs-6" id="exampleModalLabel">
        <i class="bi bi-exclamation-triangle"></i>
          Error
        </h5>
      </div>
      <div class="modal-body" id="errMsg">
        <?php echo $err; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    <div class="sign-up-bg-img-container">
        <img src="../../static/qcu-bg-img.jpg" alt="">
    </div>
    <div class="">
        <div class="signup-form-box mx-auto pt-4 rounded-2">
            <div class="signup-form-title d-flex rounded-top-2 ">
                <p class="fw-bold">STUDENT SIGN UP</p>
            </div>
            <div class="signup-form-wrapper">
                <div class="mb-2 pt-3 flex-column gap-2 form-header d-flex align-items-center justify-content-center">
                    <img src="../../static/qcu-logo.jpg" width="60" height="60" alt="">
                    <p class="fw-bold">Guidance and Counseling Unit</p>
                </div>
                <form method="POST" action="" id="signUpForm" class="d-flex flex-column gap-4 container-fluid px-3" >
                    <div>
                        <span class="form-text fw-bold">Sign Up Form</span>
                        <div class="form-text">Please fill out the form with your <span class="fw-bold">necessary</span> details</div>
                    </div>
                    <div class="sliding-form">
                        <div class="form-step" id="step1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="mb-1" for="lastNameInput">First Name <span class="required-text fst-italic">(required)</span></label>
                                    <input type="text" name="firstName" class="required signup-input form-control" id="firstNameInput" placeholder="First Name" >
                                </div>
                                <div class="col-md-6 mb-3 ">
                                    <label  class="mb-1" for="firstNameInput">Last Name <span class="required-text fst-italic">(required)</span></label>
                                    <input type="text" name="lastName" class="required signup-input form-control" id="lastNameInput" placeholder="Last Name" >
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="mb-1" for="middleNameInput">Middle Name</label>
                                    <input type="text" name="middleName" class="signup-input form-control" id="middleNameInput" placeholder="Middle Name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="mb-1" for="suffixInput">Suffix</label>
                                    <select class="form-select signup-input" name="suffix" id="suffixInput" value="">
                                        <option disabled selected>Suffix</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="mb-1" for="birthDateInput">Birthdate <span class="required-text fst-italic">(required)</span></label>
                                    <input name="birthDate" type="date" class="required signup-input form-control" id="birthDateInput" >
                                </div>
                                <div class="col-md-6 mb-3 d-flex gap-2 flex-column">
                                    <label class="mb-1">Gender <span class="required-text fst-italic">(required)</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input genderRadioInput" value="Male" type="radio" name="radioDefault" id="maleRadio">
                                            <label class="form-check-label">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input genderRadioInput" value="Female" type="radio" name="radioDefault" id="femaleRadio">
                                            <label class="form-check-label">Female</label>
                                        </div>
                                        <input type="text" class="required" name="gender" id="genderInput" hidden>
                                    </div>
                                </div>
                            </div>
                            <div class="form-btn-group d-flex gap-2 justify-content-end mb-4">
                                <button type="button" class=" btn btn-primary signup-next-btn">Next</button>
                            </div>
                        </div>
                        <div class="form-step hidden" id="step2">
                            <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label  class="mb-1" for="studentNoInput">Student No <span class="required-text fst-italic">(required)</span></label>
                                        <input name="studentNo" type="text" class="required signup-input form-control" id="studentNoInput" >
                                        <div id="passwordHelpBlock" class="form-text input-note-text fst-italic">
                                            Example: 24-1234
                                        </div>
                                        <div id="invalidStudNoErr" class="form-text input-note-text fst-italic d-none text-danger">
                                            Student No. is not in a valid format.
                                        </div>     
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label class="mb-1" for="programInput">Program <span class="required-text fst-italic">(required)</span></label>
                                        <select name="program" class="required form-select signup-input" id="programInput" value="">
                                            <option selected disabled>Choose your current program</option>
                                            <?php foreach($programs as $p) { ?>
                                                <option value=<?php echo $p["program_id"]?>><?php echo $p["program_name"]?></option> 
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="mb-1" for="campusInput">Campus <span class="required-text fst-italic">(required)</span></label>
                                        <select name="campus" class="required form-select signup-input" id="campusInput" value="" >
                                            <option disabled selected>Choose your current campus</option>
                                            <option value="San Bartolome">San Bartolome</option>
                                            <option value="San Francisco">San Francisco</option>
                                            <option value="Batasan">Batasan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="mb-1">Year Level <span class="required-text fst-italic">(required)</span></label>
                                        <select name="yearLevel" class="required form-select signup-input" id="yearLevelInput" value="" >
                                            <option disabled selected>Choose your current year level</option>
                                            <option value="1st">First Year</option>
                                            <option value="2nd">Second Year</option>
                                            <option value="3rd">Third Year</option>
                                            <option value="4th">Fourth Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-btn-group d-flex justify-content-between gap-2 align-self-end mt-3 mb-4">
                                    <button type="button" class="btn btn-outline-primary prev-btn">Previous</button>    
                                    <button type="button" class="btn btn-primary signup-next-btn">Next</button>
                                </div>
                        </div>
                        <div class="form-step hidden" id="step3">
                            <div class="row">
                                <div class="col mb-3">
                                    <label  class="mb-1" for="lastNameInput">Email <span class="required-text fst-italic">(required)</span></label>
                                    <input name="email" type="email" class="required signup-input form-control" id="emailInput" >
                                    <div id="passwordHelpBlock" class="form-text input-note-text fst-italic">
                                        Please provide your QCU email.
                                    </div>            
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label  class="mb-1" for="passwordInput">Password <span class="required-text fst-italic">(required)</span></label>
                                    <div class="input-group input-group-sm">
                                        <input name="password" type="password" class="required signup-input form-control" id="password" >
                                        <span class="input-group-text togglePassword" data-target="password" id="inputGroup-sizing-sm"><i class="bi bi-eye-slash"></i></span>
                                    </div>
                                    <div id="passwordCriteriaMsg" class="form-text input-note-text fst-italic text-secondary">
                                    Password must include at least one uppercase letter, one lowercase letter, and one special character.
                                    </div>            
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                <label  class="mb-1" for="passwordInput">Confirm Password <span class="required-text fst-italic">(required)</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="required signup-input form-control" id="confirmPassword" >      
                                        <span class="input-group-text inputGroup-sizing-sm togglePassword" data-target="confirmPassword" id="inputGroup-sizing-sm"><i class="bi bi-eye-slash"></i></span>
                                    </div>
                                    <div id="unmatchedPasswordErr" class="form-text input-note-text fst-italic d-none text-danger">
                                        Password does not match.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <div class="form-check">
                                        <input name="termsAndConditions" class="form-check-input" type="checkbox" id="termsCheckbox">
                                        <label class="form-check-label">
                                        I have read the 
                                        <span id="termsConditionsToggleBtn" data-bs-toggle="modal" data-bs-target="#termsConditionsModal" class="stmtModalBtn">Terms and Conditions</span> 
                                        and the 
                                        <span id="dataPrivacyModalToggleBtn" data-bs-toggle="modal" data-bs-target="#dataPrivacyModal" class="stmtModalBtn">Data Privacy Policy</span> 
                                        of QCU Guidance and Counseling Unit.
                                        </label>
                                    </div>
                                </div>
                                <div class="modal fade" id="termsConditionsModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Terms and Conditions for Signing Up in the Guidance Service Portal
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        By signing up and using this portal, you agree to comply with the following terms and conditions. Please read them carefully before proceeding with registration.
                                        1. Acceptance of Terms

                                        By creating an account and accessing the Guidance Service Portal, you acknowledge that you have read, understood, and agreed to be bound by these Terms and Conditions.

                                        2. User Eligibility

                                        This portal is intended for students, faculty, and authorized personnel of [Your Institution's Name] seeking guidance and counseling services. Unauthorized users are strictly prohibited from using this system.

                                        3. User Responsibilities

                                        You must provide accurate and up-to-date personal information during registration.

                                        You are responsible for maintaining the confidentiality of your account credentials.

                                        You must not share or disclose sensitive information related to guidance services received through the portal.

                                        Any misuse of the system, including unauthorized access, false appointments, or sharing confidential data, is strictly prohibited.

                                        4. Privacy and Data Protection

                                        Your personal information will be collected, stored, and processed in accordance with [Your Institution's Data Privacy Policy] and applicable data protection laws.

                                        All records related to your counseling sessions are confidential and accessible only to authorized personnel.

                                        The portal may collect and analyze anonymized data for service improvement purposes.

                                        5. Appointment Booking and Cancellations

                                        Users can schedule guidance sessions through the portal, subject to counselor availability.

                                        Appointments must be canceled at least [Insert Time Frame] in advance if you are unable to attend.

                                        Repeated no-shows or misuse of the booking system may result in temporary suspension of access.

                                        6. Prohibited Conduct

                                        You agree not to:

                                        Use the portal for unauthorized purposes, including spamming or harassment.

                                        Share, sell, or misuse any information obtained from the portal.

                                        Engage in any activity that disrupts the functionality of the portal.

                                        7. System Availability and Modifications

                                        The institution reserves the right to update, modify, or discontinue portal services at any time.

                                        Users will be notified of major updates or changes that may affect their use of the system.

                                        8. Limitation of Liability

                                        The institution is not responsible for technical issues that may prevent access to the portal.

                                        The institution is not liable for any loss, misuse, or unauthorized access to user data beyond its reasonable control.

                                        9. Termination of Access

                                        The institution reserves the right to suspend or terminate user access to the portal if:

                                        The user violates any terms outlined in this document.

                                        There is evidence of misuse or unauthorized access.

                                        The user is no longer affiliated with the institution.

                                        10. Contact Information

                                        For inquiries or concerns regarding the portal, you may contact [Insert Contact Information].

                                        By proceeding with the registration, you acknowledge and agree to these Terms and Conditions.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-success signup-agree-btn" data-bs-dismiss="modal">I Agree</button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="dataPrivacyModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Data Privacy Policy Statement
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        By providing my personal information to the University, I hereby give my consent to the collection, processing, and use of my data by authorized personnel and officials connected to the University. The information I provide will be utilized for legitimate institutional purposes, including, but not limited to, conducting research for institutional development and supporting all guidance and counseling-related functions.

                                        I further acknowledge and consent to the University using and releasing my personal data for these purposes, in full compliance with the provisions of the Data Privacy Act. I understand that the University will ensure that all personal information provided is processed in a secure and confidential manner and will be protected from unauthorized access or misuse.

                                        I affirm that all information I have provided is true, accurate, and complete to the best of my knowledge. I understand that any falsified or misleading information may lead to consequences as determined by the University's policies and applicable laws.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-success signup-agree-btn" data-bs-dismiss="modal">I Agree</button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-btn-group d-flex justify-content-between gap-2 align-self-end mb-4">
                                <button type="button" class="btn btn-outline-primary prev-btn">Previous</button>    
                                <button type="button" class="btn btn-primary signup-next-btn" id="submitBtn" disabled>Submit</button>
                            </div>
                        </div>
                    </div>                
                </form>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    const agreeBtn = document.querySelectorAll(".signup-agree-btn");
    const termsCheckbox = document.getElementById("termsCheckbox");
    const steps = document.querySelectorAll('.form-step');
    const genderInput = document.getElementById('genderInput'); 
    const genderRadioInput = document.querySelectorAll('.genderRadioInput');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const unmatchedPasswordErr = document.getElementById('unmatchedPasswordErr')
    const submitBtn = document.getElementById('submitBtn');
    const passwordCriteriaMsg = document.getElementById('passwordCriteriaMsg');
    const signUpForm = document.getElementById('signUpForm');
    const errMsg = document.getElementById('errMsg');
    const invalidStudNoErr = document.getElementById('invalidStudNoErr');
    const studentNoInput = document.getElementById('studentNoInput');
    const requiredInputs = document.querySelectorAll('.required');

    <?php if ($err) { echo 'errorModal.show();'; } ?>

    const checkRequiredFields = () => {
        let allInputsValid = true;
        [...requiredInputs].forEach((input) => {
            if (input.value.length === 0) {
                allInputsValid = false;
            }
        });
        return allInputsValid;
    }

    const isPasswordFormatValid = pw => {
        // password should contain atleast one uppercase, lowercase letter, and a special char
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/
        return regex.test(pw);
    }

    const isStudentNoValid = no => {
        const regex = /^\d{2}-\d{4}$/ 
        return regex.test(no);
    }

    const doesPasswordMatch = () => {
        return confirmPassword.value === password.value; 
    }

    const updateSubmitBtnState = () => {
        if(!termsCheckbox.checked) { return submitBtn.disabled = true; } 
        if(!isPasswordFormatValid(password.value)) { return submitBtn.disabled = true; } 
        if(!doesPasswordMatch()) { return submitBtn.disabled = true; }
        submitBtn.disabled = false;
    }

    const updatePasswordCriteriaMsgState = () => {
        passwordCriteriaMsg.classList.remove('text-secondary', 'text-danger', 'text-success');
        if(password.value.length === 0) return passwordCriteriaMsg.classList.add('text-secondary');
        if(!isPasswordFormatValid(password.value)) return passwordCriteriaMsg.classList.add('text-danger');
        return passwordCriteriaMsg.classList.add('text-success');
    }

    const updateUnmatchedPasswordErrState = () => {
        if(confirmPassword.value.length === 0) {
            return unmatchedPasswordErr.classList.add('d-none');
        }

        if(!doesPasswordMatch()) {
            return unmatchedPasswordErr.classList.remove('d-none');
        }

        unmatchedPasswordErr.classList.add('d-none');
    }

    const updateInvalidStudNoErrState = () => {
        invalidStudNoErr.classList.remove('d-none');

        if(isStudentNoValid(studentNoInput.value) 
        || studentNoInput.value.length === 0 ) {
            return invalidStudNoErr.classList.add('d-none');
        }
    }

    const setGenderValue = (gender) => {
        genderInput.value = gender;
    } 

    genderRadioInput.forEach(i => {
        i.addEventListener('click', function () {
            if(!this.checked) return;

            setGenderValue(this.value);
        })
    });

    studentNoInput.addEventListener('input', () => {
        updateInvalidStudNoErrState()
    });

    document.querySelectorAll('.togglePassword').forEach(button => {
        button.addEventListener('click', function () {
            const targetInput = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }); 
    });


    submitBtn.addEventListener('click', () => {
        if(!checkRequiredFields()) {
            errMsg.innerText = "Please fill in the required fields."
            return errorModal.show();
        } 
        signUpForm.submit();
    });

    termsCheckbox.addEventListener('change', () => { 
        if(termsCheckbox.checked) {
            termsCheckbox.value = true;
        } else {
            termsCheckbox.value = false;
        }
        updateSubmitBtnState(); 
    })

    password.addEventListener('input', () => {
        updateSubmitBtnState();
        updatePasswordCriteriaMsgState();
        updateUnmatchedPasswordErrState();
    });

    confirmPassword.addEventListener('input', () => {
        updateSubmitBtnState();
        updatePasswordCriteriaMsgState();
        updateUnmatchedPasswordErrState();
    });

    agreeBtn.forEach(b => {
        b.addEventListener('click', () => {
            termsCheckbox.checked = true;
            updateSubmitBtnState();
        });
    })

    let currentStep = 1;

    const showStep = (step) => { 
        steps.forEach((el, index) => {
            el.classList.toggle('hidden', index !== step - 1);
        });
    }

    document.querySelectorAll('.signup-next-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep < steps.length) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });

    showStep(currentStep);
</script>
</html> 