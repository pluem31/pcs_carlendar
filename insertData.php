<?php
//การเชื่อมต่อฐานข้อมูล
require('db_connect.php');

//รับค่าที่ส่งจากแบบฟอร์มลงในตัวแปร

$Job_Type = mysqli_real_escape_string($mysqli,$_POST["Job_Type"]);
$Job_Project = mysqli_real_escape_string($mysqli,$_POST["Job_Project"]);
$Job_Assign = mysqli_real_escape_string($mysqli,$_POST["Job_Assign"]);
$Job_Subject = mysqli_real_escape_string($mysqli,$_POST["Job_Subject"]);
$Job_StartDate = mysqli_real_escape_string($mysqli,$_POST["Job_StartDate"]);
$Job_FinishDate = mysqli_real_escape_string($mysqli,$_POST["Job_FinishDate"]);
$Job_Priority = mysqli_real_escape_string($mysqli,$_POST["Job_Priority"]); 
$Job_CreateUser = mysqli_real_escape_string($mysqli,$_POST["Job_CreateUser"]);


//บันทึกข้อมูล
$sql = "INSERT INTO job(Job_Subject,Job_Type,Job_Project,Job_Assign,Job_StartDate,Job_FinishDate,Job_Priority,Job_CreateUser) 
VALUES(?,?,?,?,?,?,?,?)";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('siiissis', $Job_Subject,$Job_Type,$Job_Project,$Job_Assign,$Job_StartDate,$Job_FinishDate,$Job_Priority,$Job_CreateUser);  // สำหรับ Where
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