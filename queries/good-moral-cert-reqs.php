<?php 

    function getGoodMoralCertReqs($db_conn, $page, $status = null, $dateRange = null) {
        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;
        
        $sql = "SELECT good_moral_cert_reqs.*, programs.program_name FROM good_moral_cert_reqs JOIN programs ON good_moral_cert_reqs.program_id = programs.program_id ";

        $currentDateVals = [
            'today' => date('Y-m-d'),
            'this month' => date('n'),
            'this week' => ''
        ];
        
        if($status) {
            $sql .= " WHERE status = '" . $status . "' ";
        }

        if($dateRange) {
            if($status) {
                if($status === "Pending") {
                    if($dateRange == 'today') {
                        $sql = $sql . " AND DATE(created_at) = '" .  $currentDateVals[$dateRange] . "' " . " ";
                    } else if($dateRange == 'this month') {
                        $sql = $sql . " AND MONTH(created_at) = '" . $currentDateVals[$dateRange] . "' " .  " ";
                    } else if($dateRange == 'this week'){
                        $sql = $sql . " AND  YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)" . " ";
                    }
                } else {
                    if($dateRange == 'today') {
                        $sql = $sql . " AND DATE(pickup_date) = '" .  $currentDateVals[$dateRange] . "' " . " ";
                    } else if($dateRange == 'this month') {
                        $sql = $sql . " AND MONTH(pickup_date) = '" . $currentDateVals[$dateRange] . "' " .  " ";
                    } else if($dateRange == 'this week'){
                        $sql = $sql . " AND  YEARWEEK(pickup_date, 1) = YEARWEEK(CURDATE(), 1)" . " ";
                    }
                }
            } else {
                if($dateRange == 'today') {
                    $sql = $sql . " WHERE DATE(pickup_date) = '" .  $currentDateVals[$dateRange] . "' " . "OR DATE(created_at) = '" .  $currentDateVals[$dateRange] . "' " . " ";
                } else if($dateRange == 'this month') {
                    $sql = $sql . " WHERE MONTH(pickup_date) = '" . $currentDateVals[$dateRange] . "' " .  "OR MONTH(created_at) = '" .  $currentDateVals[$dateRange] . "' " . " ";
                } else if($dateRange == 'this week'){
                    $sql = $sql . " WHERE  YEARWEEK(pickup_date, 1) = YEARWEEK(CURDATE(), 1)" . " OR YEARWEEK(created_at) = YEARWEEK(CURDATE(), 1) " . " ";
                }
            }
        }

        $sql =  $sql . " ORDER BY gmc_req_id DESC LIMIT $pageSize OFFSET $offset;";

        $stmt = $db_conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC); 
    }

    function approveGoodMoralCertReq($db_conn, $gmc_req_id) {
        $sql = "UPDATE good_moral_cert_reqs SET status = 'Approved' WHERE gmc_req_id = ?;";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("i", $gmc_req_id);
        $stmt->execute();
        $res = $stmt->affected_rows;
        
        if($res) {
            $sql = "SELECT * FROM good_moral_cert_reqs WHERE gmc_req_id = ?;";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $gmc_req_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function declineGMCReq($db_conn, $id, $declineReason) {
        $sql = "UPDATE good_moral_cert_reqs SET status = 'Declined', decline_reason = ? WHERE gmc_req_id = ?";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("si", $declineReason, $id);
        $res = $stmt->execute();
        
        if($res) {
            $sql = "SELECT * FROM good_moral_cert_reqs WHERE gmc_req_id = ?";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $id); // Changed $appt_id to $id
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function deleteGMCReq($db_conn, $appt_id) {
        $sql = "DELETE FROM good_moral_cert_reqs WHERE gmc_req_id = ?;";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("i", $appt_id);
        return $stmt->execute();
    }

    function setPickUpDate($db_conn, $gmc_req_id, $pickup_date) {
        $sql = "UPDATE good_moral_cert_reqs SET status = 'For Pickup', pickup_date = ? WHERE gmc_req_id = ?;";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("si", $pickup_date, $gmc_req_id);
        $stmt->execute();
        $res = $stmt->affected_rows;
        
        if($res) {
            $sql = "SELECT * FROM good_moral_cert_reqs WHERE gmc_req_id = ?;";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $gmc_req_id); // Changed $appt_id to $gmc_req_id
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function completeGMCReq($db_conn, $id) {
        $sql = "UPDATE good_moral_cert_reqs SET status = 'Completed' WHERE gmc_req_id = ?;";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->affected_rows;
        
        if($res) {
            $sql = "SELECT * FROM good_moral_cert_reqs WHERE gmc_req_id = ?;";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function addGMCReq(
        $db_conn,
        $first_name,
        $last_name,
        $middle_name,
        $suffix,
        $student_no,
        $program_id,
        $email,
        $contact_no,
        $start_school_year,
        $last_school_year,
        $semester,
        $graduate_status,
        $reason_desc,
        $pickup_date
    ) {
        $sql = "INSERT INTO good_moral_cert_reqs (status, first_name, last_name, middle_name, suffix, student_no, program_id, email, contact_no, start_school_year, last_school_year, semester, graduate_status, reason_desc, pickup_date) VALUES ('For Pickup',?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("sssssissssssss", $first_name, $last_name, $middle_name, $suffix, $student_no, $program_id, $email, $contact_no, $start_school_year, $last_school_year, $semester, $graduate_status, $reason_desc, $pickup_date);
        return $stmt->execute();
    }
?>