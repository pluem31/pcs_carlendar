<?php
require("db_connect.php");
$name = $_POST["jobtype"];
$name = "%" . $name . "%";

$sql = "SELECT Job_ID,Job_CreateDate,Job_Type,Job_Project,Job_Assign,Job_Subject,Job_Progres,Job_StartDate,Job_FinishDate,Job_Priority,Job_CreateUser FROM job  WHERE Job_Type LIKE ? ";
if ($stmt = $mysqli->prepare($sql)) {
  $stmt->bind_param('s', $name);  // สำหรับ Where
  $stmt->execute();
  $stmt->store_result(); 
  $stmt->bind_result($Job_ID, $Job_CreateDate, $Job_Type, $Job_Project, $Job_Assign, $Job_Subject, $Job_Progres, $Job_StartDate, $Job_FinishDate, $Job_Priority, $Job_CreateUser);
  $count = $stmt->num_rows;
}else{
  echo $stmt->error;
}
$order = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ข้อมูลพนักงาน</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
</head>

<body>
  <div class="container">
    <h1 class="text-center">รายละเอียดการทำงานของพนักงาน</h1>

    <hr>
    <?php if ($count > 0) { ?>
      <!-- <form action="deleteTextField.php" class="form-group" method="POST"> ส่วนของการลบแบบป้อนข้อมูล 
  <label for="">รหัสพนักงาน</label>
  <input type="text" placeholder="ป้อนรหัสพนักงานเพื่อลบ" name="idemployee" class="form-control">
  <input type="submit" value="ลบข้อมูล" class="btn btn-danger my-2">
</form> -->
      <form action="searchData.php" class="form-group" method="POST">
        <!--ส่วนของการลบแบบป้อนข้อมูล --->
        <label for="">ค้นหาพนักงาน</label>
        <input type="text" placeholder="ป้อนชื่อพนักงานเพื่อค้นหา" name="jobtype" class="form-control">
        <input type="submit" value="Search" class="btn btn-primary my-2">
      </form>
      <a href="insertform.php" class="btn btn-success">บันทึกข้อมูลพนักงาน++</a>
      <p>
      <table class="table table-dark">
        <thead>
          <tr>
            <th>วันที่</th>
            <th>ประเภทงาน</th>
            <th>Project</th>
            <th>ผู้ดูแล</th>
            <th>รายละเอียด</th>
            <th>ความคืบหน้า</th>
            <th>วันที่เริ่ม</th>
            <th>วันที่สินสุด</th>
            <th>ระดับความสำคัญ</th>
            <th>ชื่อผู้ใช้</th>
            <th>จัดการ</th>
          </tr>

        </thead>
        <tbody>

          <?php
          while ($stmt->fetch()) {
          ?>
            <tr>
              <td><?= $Job_CreateDate; ?></td>
              <td><?= $Job_Type; ?></td>
              <td><?= $Job_Project; ?></td>
              <td><?= $Job_Assign; ?></td>
              <td><?= $Job_Subject; ?></td>
              <td><?= $Job_Progres; ?></td>
              <td><?= $Job_StartDate; ?></td>
              <td><?= $Job_FinishDate; ?></td>
              <td><?= $Job_Priority; ?></td>
              <td><?= $Job_CreateUser; ?></td>
              <td>
                <a href="editForm.php?Job_ID=<?= $Job_ID; ?>" class="btn btn-info">แก้ไข</a>
                <a href="deleteQueryString.php?Job_ID=<?= $Job_ID; ?>" class="btn btn-danger" onclick="return confirm('คุณต้องการลบข้อมูลหรือไม่')">ลบข้อมูล</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      </p>

    <?php } else { ?>
      <div class="alert alert-danger">
        <b>ไม่มีข้อมูลพนักงาน !!!</b>
      </div>
    <?php } ?>


    <?php if ($count > 0) { ?>
      <!-- <input type="submit" value="ลบข้อมูล (checkbox)" class="btn btn-danger">
  <button type="button" class="btn btn-primary" onclick="checkAll()">เลือกทั้งหมด</button>
  <button type="button" class="btn btn-warning" onclick="uncheckAll()">ยกเลิก</button> -->
    <?php } ?>
    </form>

  </div>
</body>
<script>
  // function checkAll(){
  //   var form_element = document.forms[1].length;
  //   for(i=0; i<form_element-1;  i++){
  //     document.forms[1].elements[i].checked=true;
  //   }
  // }
  // function uncheckAll(){
  //   var form_element = document.forms[1].length;
  //   for(i=0; i<form_element-1;  i++){
  //     document.forms[1].elements[i].checked=false;
  //   }
  // }
</script>

</html>