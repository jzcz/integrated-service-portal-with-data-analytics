<?php

function getAccountByEmail($db_conn, $email) {
    $qry = "SELECT * FROM user WHERE email = ?;";
    $stmt = $db_conn->prepare($qry);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAdminByUserId($db_conn, $userId) {
    $qry = "SELECT * FROM admin WHERE user_id = ?;";
    $stmt = $db_conn->prepare($qry);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getCounselorByUserId($db_conn, $userId) {
    $qry = "SELECT * FROM counselors WHERE user_id = ?;";
    $stmt = $db_conn->prepare($qry);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createCounselorAccount($db_conn, $info, $account) {
    $role = 'Counselor';

    try {
        $db_conn->begin_transaction();

        // Insert into user
        $userQry = "INSERT INTO user (email, password, role) VALUES (?,?,?)";
        $userStmt = $db_conn->prepare($userQry);
        $userStmt->bind_param('sss', $account['email'], $account['password'], $role);
        $userStmt->execute();

        // Now get the inserted user ID
        $userId = $db_conn->insert_id;

        // Insert into counselors
        $counselorQry = "INSERT INTO counselors (user_id, first_name, last_name, employee_id, middle_name, suffix, created_at)
                         VALUES (?,?,?,?,?,?,NOW())";
        $counselorStmt = $db_conn->prepare($counselorQry);
        $counselorStmt->bind_param("isssss",
            $userId,
            $info['firstName'],
            $info['lastName'],
            $info['employeeId'],
            $info['middleName'],
            $info['suffix']
        );
        $counselorStmt->execute();

        $db_conn->commit();
        return true;

    } catch (Exception $e) {
        $db_conn->rollback();
        return false;
    }
}

function createAdminAccount($db_conn, $info, $account) {
    $role = 'Admin';

    try {
        $db_conn->begin_transaction();

        // Insert into user
        $userQry = "INSERT INTO user (email, password, role) VALUES (?,?,?)";
        $userStmt = $db_conn->prepare($userQry);
        $userStmt->bind_param('sss', $account['email'], $account['password'], $role);
        $userStmt->execute();

        // Get the user ID after inserting user
        $userId = $db_conn->insert_id;

        // Insert into admin
        $adminQry = "INSERT INTO admin (user_id, first_name, last_name, employee_id, middle_name, suffix, created_at)
                     VALUES (?,?,?,?,?,?,NOW())";
        $adminStmt = $db_conn->prepare($adminQry);
        $adminStmt->bind_param("isssss",
            $userId,
            $info['firstName'],
            $info['lastName'],
            $info['employeeId'],
            $info['middleName'],
            $info['suffix']
        );
        $adminStmt->execute();

        $db_conn->commit();
        return true;

    } catch (Exception $e) {
        $db_conn->rollback();
        echo "Transaction failed: " . $e->getMessage(); exit();
        return false;
    }
}


?>