<?php
require("db_connect.php");
require("function.php");
$Job_ID  = $_GET["Job_ID"];
//$job = $_GET["Job_ID"];

$sql = "SELECT Job_ID,Job_CreateDate,Job_Type,Job_Project,Job_Assign,Job_Subject,Job_Progres,Job_StartDate,Job_FinishDate,Job_Priority,Job_CreateUser FROM job WHERE Job_ID = ?";
if ($stmt = $mysqli->prepare($sql)) {
  $stmt->bind_param('i', $Job_ID);  // สำหรับ Where
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($Job_ID, $Job_CreateDate, $Job_Type, $Job_Project, $Job_Assign, $Job_Subject, $Job_Progres, $Job_StartDate, $Job_FinishDate, $Job_Priority, $Job_CreateUser);
  $count = $stmt->num_rows;
} else {
  echo $stmt->error;
}


// $row = mysqli_fetch_assoc($result);
//$row = mysqli_num_rows($result);
//$skill_arr=array("Java","PHP","Python","HTML","CSS"); //เตรียมตัวเลือก 4 ตัวเลือก
//echo $row["skills"];  // string >> array // java,python => ["java","python"]
//print_r($row);
while ($stmt->fetch()) {
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกรายละเอียดการทำงาน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  </head>

  <body>
    <?php
    include_once './menu.php';
    ?>
    <div class="container">
      <h2 class="text-center my-2">แบบฟอร์มแก้ไขรายละเอียดการทำงาน</h2>
      <form action="updataData.php" method="POST">
        <!-- post ส่งข้อมูล action ระบุให้ทำอะไร -->
        <input type="hidden" value="<?= $Job_ID; ?>" name="Job_ID">


        <div class="form-group">
          <label for="Type">ประเภทงาน</label>
          <select name="Job_Type" id="" class="form-control">
            <option value="0"> กรุณาเลือกประเภทงาน </option>
            <?php
            $str_0 = "";
            $str_1 = "";
            $str_2 = "";
            $str_3 = "";
            $str_4 = "";
            // $Job_Type = $row['Job_Type'];
            if ($Job_Type == "1") {
              $str_0 = "selected";
            }
            if ($Job_Type == "2") {
              // code...
              $str_1 = "selected";
            }
            if ($Job_Type == "3") {
              $str_2 = "selected";
            }
            if ($Job_Type == "4") {
              // code...
              $str_3 = "selected";
            }
            if ($Job_Type == "5") {
              $str_4 = "selected";
            }

            ?>
            <option <?= $str_0; ?> value="1"> 1 </option>
            <option <?= $str_1; ?> value="2"> 2 </option>
            <option <?= $str_2; ?> value="3"> 3 </option>
            <option <?= $str_3; ?> value="4"> 4 </option>
            <option <?= $str_4; ?> value="5"> 5 </option>
          </select>

        </div>

        <div class="form-group">
          <label for="Project">Project</label>
          <select name="Job_Project" id="" class="form-control">
            <option value="0"> กรุณาเลือกProject </option>
            <?php
            $str_0 = "";
            $str_1 = "";
            $str_2 = "";
            $str_3 = "";
            $str_4 = "";
            // $Job_Project = $row['Job_Project'];
            if ($Job_Project == "1") {
              $str_0 = "selected";
            }
            if ($Job_Project == "2") {
              // code...
              $str_1 = "selected";
            }
            if ($Job_Project == "3") {
              $str_2 = "selected";
            }
            if ($Job_Project == "4") {
              // code...
              $str_3 = "selected";
            }
            if ($Job_Project == "5") {
              $str_4 = "selected";
            }

            ?>
            <option <?= $str_0; ?> value="1"> 1 </option>
            <option <?= $str_1; ?> value="2"> 2 </option>
            <option <?= $str_2; ?> value="3"> 3 </option>
            <option <?= $str_3; ?> value="4"> 4 </option>
            <option <?= $str_4; ?> value="5"> 5 </option>
          </select>
        </div>

        <div class="form-group">
          <?php
          // $Job_Assign = $row['Job_Assign'];
          ?>
          <label for="Assign">ผู้ดูแล</label>
          <select name="Job_Assign" id="" class="form-control">
            <option value="0"> กรุณาเลือกผู้ดูแล</option>
            <option <?= iif($Job_Assign == 1, "selected", "") ?> value="1"> 1 </option>
            <option <?= iif($Job_Assign == 2, "selected", "") ?> value="2"> 2 </option>
            <option <?= iif($Job_Assign == 3, "selected", "") ?> value="3"> 3 </option>
            <option <?= iif($Job_Assign == 4, "selected", "") ?> value="4"> 4 </option>
            <option <?= iif($Job_Assign == 5, "selected", "") ?> value="5"> 5 </option>
          </select>
        </div>

        <div class="form-group">
          <label for="Subject">รายละเอียด</label>
          <textarea name="Job_Subject" id="" cols="50" rows="4" class="form-control"><?= $Job_Subject; ?></textarea>
        </div>

        <div class="form-group">
          <label for="StartDate">วันที่เริ่ม</label>
          <input type="date" name="Job_StartDate" id="" class="form-control" value="<?= $Job_StartDate; ?>">
        </div>

        <div class="form-group">
          <label for="FinishDate">วันที่สินสุด</label>
          <input type="date" name="Job_FinishDate" id="" class="form-control" value="<?= $Job_FinishDate; ?>">
        </div>

        <div class="form-group">
          <label for="Priority">ระดับความสำคัญ</label>
          <input type="number" name="Job_Priority" id="" class="form-control" min="1" max="3" value="<?= $Job_Priority; ?>">
        </div>

        <div class="form-group">
          <label for="CreateUser">ชื่อผู้ใช้(รหัสพนักงาน)</label>
          <input type="text" name="Job_CreateUser" id="" pattern="[0-9]{6}" title="ต้องเป็นรหัสพนักงาน 6 ตัวเท่านั้น" class="form-control" value="<?= $Job_CreateUser; ?>">
        </div>




        <input type="submit" value="บันทึกข้อมูล" class="btn btn-success my-4">
        <input type="reset" value="ล้างข้อมูล" class="btn btn-danger  my-4">
        <a href="index.php" class="btn btn-primary my-4">ย้อนกลับ</a>
      </form>
    </div>
  </body>

  </html>
<?php } ?>