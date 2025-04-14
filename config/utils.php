<?php 
    function sanitizeData($conn, $data) {
        $data = trim($data);
        $data = $conn->real_escape_string($data);
        return $data;
    }
?>