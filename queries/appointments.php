<?php 

    function getAllAppointments($db_conn, $page, $status = null, $dateRange = null) {
        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;
        $sql = "SELECT * FROM appointments appt LEFT JOIN appt_attendee atts ON appt.attendee_id = atts.attendee_id LEFT JOIN programs ON atts.program_id = programs.program_id";

        if($status) {
            $sql = $sql . " WHERE status = '". $status . "' ";
        }

        if($dateRange) {
            $currentDateVals = [
                'today' => date('Y-m-d'),
                'this month' => date('n'),
                'this year' => date("Y"),
                'this week' => '',
            ];

            if($status) {
                if($status == 'Pending') {
                    if($dateRange == 'today') {
                        $sql = $sql . " AND DATE(created_at) = '" .  $currentDateVals[$dateRange] . "' ";
                    } else if($dateRange == 'this month') {
                        $sql = $sql . " AND MONTH(created_at) = '" . $currentDateVals[$dateRange] . "' " . "AND YEAR(created_at) = '" . $currentDateVals['this year'] . "' ";
                    } else if($dateRange == 'this year') {
                        $sql = $sql . " AND YEAR(created_at) = '" . $currentDateVals[$dateRange] . "' ";
                    } else if($dateRange == 'this week'){
                        $sql = $sql . " AND  YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) " . " ";
                    }
    
                } else {
                    if($dateRange == 'today') {
                        $sql = $sql . " AND DATE(appt_date) = '" .  $currentDateVals[$dateRange] . "' ";
                    } else if($dateRange == 'this month') {
                        $sql = $sql . " AND MONTH(appt_date) = '" . $currentDateVals[$dateRange] . "' ";
                    } else if($dateRange == 'this year') {
                        $sql = $sql . " AND YEAR(appt_date) = '" . $currentDateVals[$dateRange] . "' ";
                    } else if($dateRange == 'this week') {
                        $sql = $sql . " AND YEARWEEK(appt_date, 1) = YEARWEEK(CURDATE(), 1)" . " ";
                    }
                }
            } else {
                if($dateRange == 'today') {
                    $sql = $sql . " WHERE appt_date = '" .  $currentDateVals[$dateRange] . "' ";
                } else if($dateRange == 'this month') {
                    $sql = $sql . " WHERE MONTH(appt_date) = '" . $currentDateVals[$dateRange] . "' ";
                } else if($dateRange == 'this year') {
                    $sql = $sql . " WHERE YEAR(appt_date) = '" . $currentDateVals[$dateRange] . "' ";
                } else if($dateRange == 'this week') {
                    $sql = $sql . " WHERE YEARWEEK(appt_date, 1) = YEARWEEK(CURDATE(), 1) " . " ";
                }
            }
            
        }

        $sql = $sql . " ORDER BY appt_id DESC LIMIT $pageSize OFFSET $offset ";

        $stmt = $db_conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); 
    }

    function confirmAppointment($db_conn, $appt_id, $appt_date, $appt_start_time, $appt_end_time) {
        $sql = "UPDATE appointments SET status = 'Upcoming', appt_date = ?, appt_start_time = ?, appt_end_time = ? WHERE appt_id = ?";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("sssi", $appt_date, $appt_start_time, $appt_end_time, $appt_id);
        $res = $stmt->execute();

        if($res) {
            $sql = "SELECT appt.*, att.* FROM appointments appt JOIN appt_attendee att ON appt.attendee_id = att.attendee_id WHERE appt_id = ?";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $appt_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function declineAppointment($db_conn, $appt_id, $declineReason) {
        $sql = "UPDATE appointments SET status = 'Declined', decline_reason = ? WHERE appt_id = ?";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("si", $declineReason, $appt_id);

        $res = $stmt->execute();

        if($res) {
            $sql = "SELECT appt.*, att.* FROM appointments appt JOIN appt_attendee att ON appt.attendee_id = att.attendee_id WHERE appt_id = ?";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $appt_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function cancelAppointment($db_conn, $appt_id, $cancellationReason) {
        $sql = "UPDATE appointments SET status = 'Cancelled', cancellation_reason = ? WHERE appt_id = ?";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("si", $cancellationReason, $appt_id);
        $res = $stmt->execute();

        if($res) {
            $sql = "SELECT appt.*, att.* FROM appointments appt JOIN appt_attendee att ON appt.attendee_id = att.attendee_id WHERE appt_id = ?";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $appt_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function updateAppointment($db_conn, $appt_id, $appt_date, $appt_start_time, $appt_end_time) {
        $sql = "UPDATE appointments SET appt_date = ?, appt_start_time = ?, appt_end_time = ? WHERE appt_id = ?";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("sssi", $appt_date, $appt_start_time, $appt_end_time, $appt_id);
        $res = $stmt->execute();

        if($res) {
            $sql = "SELECT appt.*, att.* FROM appointments appt JOIN appt_attendee att ON appt.attendee_id = att.attendee_id WHERE appt_id = ?";
            $stmt = $db_conn->prepare($sql);
            $stmt->bind_param("i", $appt_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); 
        } else {
            return false;
        }
    }

    function completeAppointment($db_conn, $appt_id) {
        $sql = "UPDATE appointments SET status = 'Completed' WHERE appt_id = ?";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("i", $appt_id);
        return $stmt->execute();
    }

    function deleteAppointment($db_conn, $appt_id) {
        $sql = "DELETE FROM appointments WHERE appt_id = ?";
        $stmt = $db_conn->prepare($sql);
        $stmt->bind_param("i", $appt_id);
        return $stmt->execute();
    }

?>