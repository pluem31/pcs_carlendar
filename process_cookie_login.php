<?php
include_once './db_connect.php';
$email = $_POST['email'];
$password = $_POST['password'];
$remember = $_POST['remember'];
if($remember==true){
    setcookie("email_login", $email, time() + ( 365 * 24 * 60 * 60), "/");
    setcookie("password_login", $password, time() + ( 365 * 24 * 60 * 60), "/");
}else{
    setcookie("email_login", null, time() + ( 365 * 24 * 60 * 60), "/");
    setcookie("password_login", null, time() + ( 365 * 24 * 60 * 60), "/");
}

