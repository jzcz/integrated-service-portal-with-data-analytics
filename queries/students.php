<?php

    function getAllPrograms($db_conn) {
        $qry = "SELECT program_id, program_name FROM programs"; 
        $stmt = $db_conn->query($qry);  
        return $stmt->fetch_all(MYSQLI_ASSOC);
    }

    function getStudentByStudNo($db_conn, $studNo) {
        $qry = "SELECT * FROM students WHERE student_no = ?;";
        $stmt = $db_conn->prepare($qry);
        $stmt->bind_param("s", $studNo);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function getStudentByUserId($db_conn, $userId) {
        $qry = "SELECT * FROM students WHERE user_id = ?;";
        $stmt = $db_conn->prepare($qry);
        $stmt->bind_param("i", $userId);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function getStudentByStudId($db_conn, $studId) {
        $qry = "SELECT s.*, u.email
                FROM students s
                INNER JOIN user u ON s.user_id = u.user_id
                WHERE s.student_id = ?;";
        $stmt = $db_conn->prepare($qry);
        $stmt->bind_param("i", $studId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function addNewStudentAcc($db_conn, $studentInfo) {
        $role = 'Student';
        // Query for creating user account
        $userQry = "INSERT INTO user (email, password, role) VALUES (?,?,?);";
        $userStmt = $db_conn->prepare($userQry);
        $userStmt->bind_param('sss', $studentInfo['email'], $studentInfo['password'], $role);
        
        $userId;

        // Query for creating student account profile
        $studentQry = "INSERT INTO students "
        . "(user_id, first_name, last_name, " 
        . " student_no, program_id, current_year_level, "
        . " gender, birthdate, agreedToDataPrivacyPolicy," 
        . " campus, suffix, middle_name)"
        . " VALUES (?,?,?,?,?,?,?,?,?,?,?,?);" ;

        // for optional values, just add null
        $suffix = array_key_exists("suffix", $studentInfo) ? $studentInfo['suffix'] : NULL;
        $middleName = array_key_exists("middleName", $studentInfo) ? $studentInfo['middleName'] : NULL;
        
        $studentStmt = $db_conn->prepare($studentQry);
        $studentStmt->bind_param("isssssssssss",
            $userId,
            $studentInfo['firstName'],
            $studentInfo['lastName'],
            $studentInfo['studentNo'],
            $studentInfo['programId'],
            $studentInfo['yearLevel'],
            $studentInfo['gender'],
            $studentInfo['birthDate'],
            $studentInfo['agreedToTermsConditions'],
            $studentInfo['campus'],
            $suffix,
            $middleName
        );

        $fetchStudQry = 'SELECT * FROM students WHERE student_id = ?;';
        $fetchStudStmt = $db_conn->prepare($fetchStudQry);
        $fetchStudStmt->bind_param('i', $studentId);
        $studentId;

        try {
            $db_conn->begin_transaction();
            $userStmt->execute();
            $userId = $db_conn->insert_id;

            $studentStmt->execute();
            $studentId = $db_conn->insert_id;

            $db_conn->commit();

            $fetchStudStmt->execute();
            $res = $fetchStudStmt->get_result();

            return $res->fetch_assoc();
        } catch (Exception $e) {
            
            $db_conn->rollback();
            echo $e->getMessage(); 
        }
    }


    function submitGmcRequest($db_conn, $firstName, $middleName, $lastName, $suffix, $studentNo, $programId, $startSchoolYear, $endSchoolYear, $semester, $reasonDesc, $specifyReason, $proofImgUrl, $contactNo, $email) {
        $query = "INSERT INTO good_moral_cert_reqs (
                        first_name, middle_name, last_name, suffix,
                        student_no, program_id, start_school_year, last_school_year,
                        semester, reason_desc, additional_req_des, proof_img_url, contact_no, email
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db_conn->prepare($query);
        $stmt->bind_param("ssssssssssssss",
                            $firstName, $middleName, $lastName, $suffix,
                            $studentNo, $programId, $startSchoolYear, $endSchoolYear,
                            $semester, $reasonDesc, $specifyReason, $proofImgUrl, $contactNo, $email);
        return $stmt->execute();
    }
?>