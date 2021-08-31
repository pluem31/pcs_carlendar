<?php
require("db_connect.php");
$Job_ID = mysqli_real_escape_string($mysqli,$_POST["Job_ID"]);
$Job_Type = mysqli_real_escape_string($mysqli,$_POST["Job_Type"]);
$Job_Project = mysqli_real_escape_string($mysqli,$_POST["Job_Project"]);
$Job_Assign = mysqli_real_escape_string($mysqli,$_POST["Job_Assign"]);
$Job_Subject = mysqli_real_escape_string($mysqli,$_POST["Job_Subject"]);
$Job_StartDate = mysqli_real_escape_string($mysqli,$_POST["Job_StartDate"]);
$Job_FinishDate = mysqli_real_escape_string($mysqli,$_POST["Job_FinishDate"]);
$Job_Priority = mysqli_real_escape_string($mysqli,$_POST["Job_Priority"]); 
$Job_CreateUser = mysqli_real_escape_string($mysqli,$_POST["Job_CreateUser"]);



$sql = "UPDATE job SET 
Job_Subject = ?,
Job_Type = ?,
Job_Project = ?,
Job_Assign = ?,
Job_StartDate = ?,
Job_FinishDate = ?,
Job_Priority = ?,
Job_CreateUser = ?
WHERE Job_ID = ? ";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('siiissisi', $Job_Subject,$Job_Type,$Job_Project,$Job_Assign,$Job_StartDate,$Job_FinishDate,$Job_Priority,$Job_CreateUser,$Job_ID);  // สำหรับ Where
    if ($stmt->execute()) {
        header("location:index.php");
        exit(0);
    }else{
        echo $stmt->error;
    }
  }else{
    echo $stmt->error;
  }
 
?>