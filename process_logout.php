<?php
include_once './db_connect.php';
include_once './functions.php';
sec_session_start();
$_SESSION = array();
session_destroy();
    setcookie("email_login", null, time() + ( 365 * 24 * 60 * 60), "/");
    setcookie("password_login", null, time() + ( 365 * 24 * 60 * 60), "/");
?>
<script>window.location.href = 'index.php';</script>