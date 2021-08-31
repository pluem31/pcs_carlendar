<?php
include_once './db_connect.php';
include_once './functions.php';
sec_session_start(); // Our custom secure way of starting a PHP session.

if (isset($_POST['email'], $_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login_for_email($email, $password, $mysqli) == true) {
        // Login success 
        header('Location: index.php');
        exit();
    } else {
        // Login failed 
        echo "<script>alert('รหัสผ่านผิด ถ้าหากลืมให้สอบถามเจ้าหน้าที่ PCS ');location.href = 'index.php';</script>";
    }
} else {
    // The correct POST variables were not sent to this page. 
    header('Location: error.php?err=Could not process login');
    exit();
}
$mysqli->close();