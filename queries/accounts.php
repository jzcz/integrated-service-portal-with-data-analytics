<?php 

    function getAccountByEmail($db_conn, $email, $role) {
        $qry = "SELECT * FROM user WHERE email = ? AND role = ?;";
        $stmt = $db_conn->prepare($qry);
        $stmt->bind_param("ss", $email, $role);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
?>