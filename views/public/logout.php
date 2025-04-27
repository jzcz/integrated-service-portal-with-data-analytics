<?php 
    session_start();
    $userRole = $_SESSION['userRole'];
    $redirectUrl = $userRole === 'Student' ? '../service-portal/login.php' : 'counselor-admin-login-page.php';

    session_unset();
    session_destroy();

    header("location: $redirectUrl");
?>