<?php
require('db_connect.php');

$Job_ID = mysqli_real_escape_string($mysqli,$_GET["Job_ID"]);

$sql = "DELETE  FROM job WHERE Job_ID = ?";
if ($stmt = $mysqli->prepare($sql)) {
  $stmt->bind_param('i', $Job_ID);  // สำหรับ Where
  if($stmt->execute()){
    header("location:index.php");
    exit(0);
}else{
    echo "เกิดข้อผิดพลาด";
}
}else{
  echo $stmt->error;
}




?>