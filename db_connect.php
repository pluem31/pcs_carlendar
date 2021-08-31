<?php
include_once './config.php';

// Create connection
$mysqli = new mysqli(DBHost, DBUser, DBPasswd, DBName);

// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

mysqli_set_charset($mysqli, "utf8");

?>