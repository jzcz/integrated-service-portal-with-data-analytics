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

?>