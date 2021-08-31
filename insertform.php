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
        <h2 class="text-center my-2">แบบฟอร์มบันทึกรายละเอียดการทำงาน</h2>
        <form action="insertData.php" method="post">
            <!-- post ส่งข้อมูล action ระบุให้ทำอะไร -->

            <div class="form-group">
                <label for="Type">ประเภทงาน</label>
                <select name="Job_Type" id="" class="form-control">
                    <option value="0"> กรุณาเลือกประเภทงาน </option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                </select>

            </div>

            <div class="form-group">
                <label for="Project">Project</label>
                <select name="Job_Project" id="" class="form-control">
                    <option value="0"> กรุณาเลือกProject </option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                </select>
            </div>

            <div class="form-group">
                <label for="Assign">ผู้ดูแล</label>
                <select name="Job_Assign" id="" class="form-control">
                    <option value="0"> กรุณาเลือกผู้ดูแล </option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                </select>
            </div>

            <div class="form-group">
                <label for="gender">รายละเอียด</label>
                <textarea name="Job_Subject" id="" cols="50" rows="4" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="gender">วันที่เริ่ม</label>
                <input type="date" name="Job_StartDate" id="" class="form-control">
            </div>

            <div class="form-group">
                <label for="gender">วันที่สินสุด</label>
                <input type="date" name="Job_FinishDate" id="" class="form-control">
            </div>

            <div class="form-group">
                <label for="gender">ระดับความสำคัญ</label>
                <input type="number" name="Job_Priority" id="" class="form-control" min="1" max="3">
            </div>

            <div class="form-group">
                <label for="gender">ชื่อผู้ใช้(รหัสพนักงาน)</label>
                <input type="text" name="Job_CreateUser" id="" pattern="[0-9]{6}" title="ต้องเป็นรหัสพนักงาน 6 ตัวเท่านั้น" class="form-control">
            </div>




            <input type="submit" value="บันทึกข้อมูล" class="btn btn-success my-4">
            <input type="reset" value="ล้างข้อมูล" class="btn btn-danger my-4">
            <a href="index.php" class="btn btn-primary my-4">ย้อนกลับ</a>
        </form>
    </div>
</body>

</html>