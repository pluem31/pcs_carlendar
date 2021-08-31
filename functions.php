<?php
/*
 * Copyright (C) 2013 peredur.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once './config.php';

function sec_session_start()
{
    $session_name = 'sec_session_id';   // Set a custom session name 
    $secure = SECURE;

    // This stops JavaScript being able to access the session id.
    $httponly = true;

    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }

    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

    // Sets the session name to the one set above.
    session_name($session_name);

    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerated the session, delete the old one. 
}

function login($Username, $password, $mysqli)
{
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT `Staff ID`, `First NameTH`, `SurnameTH` , `Password` , `salt` , `Force Change Password`, `Analyst`,`Supervisor/Analyst`,`FullTime_Position`, `Count Supervisor`,`Check Supervisor`
				  FROM `Staff Table`
                  WHERE `Staff ID` = ? ")) {
        $stmt->bind_param('s', $Username);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($StaffID, $FirstName, $Surname, $db_password, $salt, $ForceChangePassword, $Analyst, $Supervisor, $Position, $CountSupervisor, $CheckSupervisor);
        $stmt->fetch();
        //echo "PasswordBefore:" . $password;
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);

        if ($stmt->num_rows == 1) {

            // Check if the password in the database matches 
            // the password the user submitted.
            if ($db_password == $password) {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                // XSS protection as we might print this value
                $StaffID = preg_replace("/[^0-9]+/", "", $StaffID);
                $_SESSION['StaffID'] = $StaffID;
                $_SESSION['Temporary'] = 0;

                $_SESSION['Level'] = 2;



                $_SESSION['login_string'] = hash('sha512', $password . $user_browser);

                // Login successful. 
                return true;
            } else {
                // Password is not correct 
                // We record this attempt in the database 
                // $now = time();


                return false;
            }
        } else {
            // No user exists. 
            return false;
        }
    } else {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

function login_for_email($email, $password, $mysqli)
{
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT `Staff ID`, `First NameTH`, `SurnameTH` , `Password` , `salt` , `Force Change Password`, `Analyst`,`Supervisor/Analyst`,`FullTime_Position`, `Count Supervisor`,`Check Supervisor`
				  FROM `Staff Table`
                                  WHERE `Staff ID` = ? ")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($StaffID, $FirstName, $Surname, $db_password, $salt, $ForceChangePassword, $Analyst, $Supervisor, $Position, $CountSupervisor, $CheckSupervisor);
        $stmt->fetch();
        //echo "PasswordBefore:" . $password;
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);

        if ($stmt->num_rows == 1) {

            // Check if the password in the database matches 
            // the password the user submitted.
            if ($db_password == $password) {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                // XSS protection as we might print this value
                $StaffID = preg_replace("/[^0-9]+/", "", $StaffID);
                $_SESSION['StaffID'] = $StaffID;
                $_SESSION['Email'] = $email;
                $_SESSION['Name'] = $FirstName . " " . $Surname;
                $_SESSION['Temporary'] = 0;
                $_SESSION['ClientID'] = null;
                $_SESSION['UserType'] = 1;
                switch ($Position) {
                    case 4:
                        $_SESSION['Level'] = 2;
                        break;

                    case 1:
                        $_SESSION['Level'] = 1;
                        break;
                }
                $_SESSION['login_string'] = hash('sha512', $password . $user_browser);

                // Login successful. 
                return true;
            } else {
                // Password is not correct 
                // We record this attempt in the database 
                // $now = time();


                return false;
            }
        } else {
            // No user exists. 
            return false;
        }
    } else {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

function login_for_client($email, $password, $mysqli)
{
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT `Email`, `FirstName`, `Surname` , `Password` , `salt` , `level`, `Status`,`Client ID`
				  FROM `UserClient`
                                  WHERE `Email` = ? ")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($Email, $FirstName, $Surname, $db_password, $salt, $level, $Status, $ClientID);
        $stmt->fetch();
        //echo "PasswordBefore:" . $password;
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);

        if ($stmt->num_rows == 1) {

            // Check if the password in the database matches 
            // the password the user submitted.
            if ($db_password == $password) {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                // XSS protection as we might print this value
                $StaffID = preg_replace("/[^0-9]+/", "", $StaffID);
                $_SESSION['StaffID'] = $Email;
                $_SESSION['Email'] = $Email;
                $_SESSION['Name'] = $FirstName . " " . $Surname;
                $_SESSION['UserType'] = 2;
                $_SESSION['ClientID'] = $ClientID;
                $_SESSION['Level'] = $level;
                $_SESSION['login_string'] = hash('sha512', $password . $user_browser);

                // Login successful. 
                return true;
            } else {
                // Password is not correct 
                // We record this attempt in the database 
                // $now = time();


                return false;
            }
        } else {
            // No user exists. 
            return false;
        }
    } else {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

function login_admin($Username, $password, $mysqli_asset)
{

    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli_asset->prepare("SELECT `password` , `salt`  FROM  `users`  ")) {
        //  $stmt->bind_param('s', $Username);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($db_password, $salt);
        $stmt->fetch();
        //echo "PasswordBefore:" . $password;
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);

        if ($stmt->num_rows == 1) {

            // Check if the password in the database matches 
            // the password the user submitted.
            if ($db_password == $password) {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                // XSS protection as we might print this value
                //  $Username = preg_replace("/[^0-9]+/", "", $Username);
                $_SESSION['StaffID'] = $Username;
                $_SESSION['Temporary'] = 1;
                $_SESSION['Level'] = 1;
                $_SESSION['login_string'] = hash('sha512', $password . $user_browser);

                // Login successful. 
                return true;
            } else {
                // Password is not correct 
                // We record this attempt in the database 
                $now = time();


                return false;
            }
        } else {
            // No user exists. 
            return false;
        }
    } else {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

function checkTemporary($StaffID, $mysqli)
{
    $StaffID = str_pad($StaffID, 7, '0', STR_PAD_LEFT);
    if ($stmtD = $mysqli->prepare("select count(*) as CStaff from `staff temporary` where `Staff ID` = ?")) {
        $stmtD->bind_param('s', $StaffID);
        $stmtD->execute();
        $stmtD->store_result();
        $stmtD->bind_result($CStaff);
        $stmtD->fetch();
    }

    //    $stmt->close();
    if ($CStaff > 0) {
        return True;
    } else {
        return False;
    }
}

function checkbrute($StaffID, $mysqli)
{
    // Get timestamp of current time 
    $now = time();

    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);

    if ($stmt = $mysqli->prepare("SELECT time 
                                  FROM login_attempts 
                                  WHERE `Staff ID` = ? AND time > '$valid_attempts'")) {
        $stmt->bind_param('s', $StaffID);

        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();

        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    } else {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

//function login_check($mysqli) {
//    // Check if all session variables are set 
//    if (isset($_SESSION['StaffID'], $_SESSION['login_string'])) {
//        $login_string = $_SESSION['login_string'];
//        $StaffID = $_SESSION['StaffID'];
//
//        // Get the user-agent string of the user.
//        $user_browser = $_SERVER['HTTP_USER_AGENT'];
//
//        if ($stmt = $mysqli->prepare("SELECT Password 
//				      FROM `Staff Table`
//				      WHERE `Staff ID` = ? AND `BlackList` = 0 LIMIT 1")) {
//            // Bind "$user_id" to parameter. 
//            $stmt->bind_param('s', $StaffID);
//            $stmt->execute();   // Execute the prepared query.
//            $stmt->store_result();
//
//            if ($stmt->num_rows == 1) {
//                // If the user exists get variables from result.
//                $stmt->bind_result($Password);
//                $stmt->fetch();
//                $login_check = hash('sha512', $Password . $user_browser);
//
//                if ($login_check == $login_string) {
//                    // Logged In!!!! 
//                    return true;
//                } else {
//                    // Not logged in 
//                    return false;
//                }
//            } else {
//                // Not logged in 
//                return false;
//            }
//        } else {
//            // Could not prepare statement
//            header("Location: ../error.php?err=Database error: cannot prepare statement");
//            exit();
//        }
//    } else {
//        // Not logged in 
//        return false;
//    }
//}

function login_check($mysqli)
{

    // Check if all session variables are set 
    if (isset($_SESSION['StaffID'], $_SESSION['login_string'])) {
        $login_string = $_SESSION['login_string'];
        $StaffID = $_SESSION['StaffID'];

        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if (is_numeric($StaffID)) {
            // check login normal

            if ($stmt = $mysqli->prepare("SELECT Password 
				      FROM `Staff Table`
				      WHERE `Staff ID` = ? AND `BlackList` = 0 LIMIT 1")) {
                // Bind "$user_id" to parameter. 
                $stmt->bind_param('s', $StaffID);
                $stmt->execute();   // Execute the prepared query.
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // If the user exists get variables from result.
                    $stmt->bind_result($Password);
                    $stmt->fetch();
                    $login_check = hash('sha512', $Password . $user_browser);

                    if ($login_check == $login_string && $_SESSION['Temporary'] == 0) {
                        // Logged In!!!! 
                        return true;
                    } else {
                        // Not logged in 
                        return false;
                    }
                } else {
                    // Not logged in 
                    return false;
                }
                $stmt->close();
            } else {
                // Could not prepare statement
                header("Location: ../error.php?err=Database error: cannot prepare statement");
                exit();
            }
        } else {

            if ($stmt = $mysqli->prepare("SELECT `Password` FROM `UserClient` WHERE `Email` = ? LIMIT 1")) {

                // Bind "$user_id" to parameter. 
                $stmt->bind_param('s', $_SESSION['Email']);
                $stmt->execute();   // Execute the prepared query.
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // If the user exists get variables from result.
                    $stmt->bind_result($Password);
                    $stmt->fetch();
                    $login_check = hash('sha512', $Password . $user_browser);

                    if ($login_check == $login_string) {
                        // Logged In!!!! 
                        return true;
                    } else {
                        // Not logged in 
                        return false;
                    }
                } else {
                    // Not logged in 
                    return false;
                }
                $stmt->close();
            } else {
                // Could not prepare statement
                header("Location: ../error.php?err=Database error: cannot prepare statement");
                exit();
            }
        }
    } else {
        // Not logged in 
        return false;
    }
}

function loadCookie($mysqli)
{
    //   echo "<script>alert('" .$_COOKIE['email_login'] . " - " . $_COOKIE['password_login'] ."');</script>";
    if (isset($_COOKIE['email_login']) && isset($_COOKIE['password_login'])) {
        $password = hash('sha512', $_COOKIE['password_login']);

        if (login_for_email($_COOKIE['email_login'], $password, $mysqli) == true) {
            // Login success 
            //echo "มีcookie".$_COOKIE['email_login'].$_COOKIE['password_login'];
            header('Location: index.php');
            exit();
        } else {
            setcookie("email_login", null, time() + (365 * 24 * 60 * 60), "/");
            setcookie("password_login", null, time() + (365 * 24 * 60 * 60), "/");
            header('Location: login.php');
        }
    }
}

function esc_url($url)
{

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function ForceChangePassword_check($mysqli)
{
    if (isset($_SESSION['StaffID'])) {
        $StaffID = $_SESSION['StaffID'];
        if (is_numeric($StaffID)) {
            if ($stmt = $mysqli->prepare("SELECT `Force Change Password` 
				      FROM `Staff Table`
				      WHERE `Staff ID` = ? LIMIT 1")) {
                // Bind "$user_id" to parameter. 
                $stmt->bind_param('s', $StaffID);
                $stmt->execute();   // Execute the prepared query.
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // If the user exists get variables from result.
                    $stmt->bind_result($ForceChangePassword);
                    $stmt->fetch();

                    if ($ForceChangePassword == -1) {
                        // Logged In!!!! 
                        return true;
                    } else {
                        // Not logged in 
                        return false;
                    }
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Could not prepare statement
                header("Location: ../error.php?err=Database error: cannot prepare statement");
                exit();
            }
        } else {
            return true;
        }
    } else {
        // Not logged in 
        return false;
    }
}

function ImportantData_check($mysqli)
{
    if (isset($_SESSION['StaffID'])) {
        $StaffID = $_SESSION['StaffID'];
        if ($stmt = $mysqli->prepare("SELECT `First NameTH` , `SurnameTH` , `IDCard` 
				      FROM `Staff Table`
				      WHERE `Staff ID` = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('s', $StaffID);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($FirstNameTH, $SurnameTH, $IDCard);
                $stmt->fetch();

                if ($FirstNameTH <> "" && $SurnameTH <> "" && $IDCard <> "") {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            header("Location: ../error.php?err=Database error: cannot prepare statement");
            exit();
        }
    } else {
        return false;
    }
}

function iif($condition, $true, $false)
{
    return ($condition ? $true : $false);
}

function BlackList($StaffID, $mysqli)
{
    $StaffID = str_pad($StaffID, 7, '0', STR_PAD_LEFT);
    if ($stmt = $mysqli->prepare("SELECT `BlackList`
				  FROM `Staff Table`
                                  WHERE `Staff ID` = ? ")) {
        $stmt->bind_param('s', $StaffID);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($BlackList);
        $stmt->fetch();

        if ($BlackList == -1) {
            return true;
        } else {
            return false;
        }
    }
}

function checkReadyWork($StaffID, $StaffProfileDate, $StaffProfileStartTime, $mysqli)
{
    $count = 0;
    $StatusShow = true;
    $sql = "select `Stocktake ID` from `Availability Table` where `Staff ID` = " . $StaffID . " AND datediff(`Work Date`, now()) >= 0 ";
    $rsSK = $mysqli->query($sql);
    $count = $rsSK->num_rows;
    if ($count > 0) {
        while ($rowSK = $rsSK->fetch_array()) {
            $sql_SK = "select `Date`,`Gunners Start Time` from `Stocktake Table` where `Stocktake ID` = " . $rowSK['Stocktake ID'];
            $rs_SK = $mysqli->query($sql_SK);
            while ($row_SK = $rs_SK->fetch_array()) {
                //              echo "StocktakeID".$row['Stocktake ID']." ";
                $date_A = new DateTime(date_format(date_create($row_SK['Date']), "Y-m-d") . " " . date_format(date_create($row_SK['Gunners Start Time']), "H:i:s"));
                $date_S = new DateTime(date_format(date_create($StaffProfileDate), "Y-m-d") . " " . date_format(date_create($StaffProfileStartTime), "H:i:s"));

                $interval = $date_A->diff($date_S);

                $chkHours = ($interval->days * 24) + $interval->h;
                if ($chkHours < 17) {
                    $StatusShow = false;
                    break;
                }
            }
        }
    }
    return $StatusShow;
}

function Update_Variance($mysqli_asset, $StocktakeID, $Costcenter)
{



    // Truncate Talbe IOH
    if ($stmt = $mysqli_asset->prepare("Truncate table `ioh`")) {

        if (!$stmt->execute()) {
?>
            <h6 class="w3-text-red">Error Truncate IOH</h6>
        <?php
        }
    }

    // Truncate Talbe consolidated
    if ($stmt = $mysqli_asset->prepare("Truncate table `consolidated`")) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error Truncate consolidated</h6>
        <?php
        }
    }

    // Truncate Talbe OverOnHand
    if ($stmt = $mysqli_asset->prepare("Truncate table `overonhand`")) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error Truncate OverOnHand</h6>
    <?php
        }
    }

    //     // update clear null DocumentID
    //    $UpdateDocumentID = "UPDATE counted_data SET counted_data.DocumentID = '' WHERE counted_data.DocumentID is null" ;
    //    if ($stmt = $mysqli_asset->prepare($UpdateDocumentID)) {
    //
    //        if (!$stmt->execute()) {
    //            
    ?>
    <!--<h6 class="w3-text-red">Error update clear null DocumentID</h6>-->
    <?php
    //        }
    //    }
    // Append Over on hand
    $OverOnHand = "Insert into overonhand (Counted_ID,CD_Barcode,StocktakeID,Costcenter,AssetsNo,SerialNo,Description,Reason,DocumentID)  SELECT `tb_productdetail`.PR_ID,`tb_productdetail`.PR_Barcode," . $StocktakeID . ",'','','',`tb_productdetail`.`PM_ProductMasterID`,'','' 
FROM `isd_soh` RIGHT JOIN `tb_productdetail` ON `isd_soh`.SKU = `tb_productdetail`.PR_Barcode
WHERE (`isd_soh`.SKU Is Null AND `tb_productdetail`.PR_Barcode <> '')";
    if ($stmt = $mysqli_asset->prepare($OverOnHand)) {

        if (!$stmt->execute()) {
    ?>
            <h6 class="w3-text-red">Error Over On Hand</h6>
        <?php
        }
    }


    // Query Client Append To IOH
    $Append_To_IOH = "INSERT INTO ioh ( StocktakeID,QRCode,Description, Qty ) Select " . $StocktakeID . ",SKU,Description,qty From `isd_soh` ";
    if ($stmt = $mysqli_asset->prepare($Append_To_IOH)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error Client Append To IOH</h6>
        <?php
        }
    }

    // Query Append To IOH OverOnHand
    $AppendOverOnHand = "INSERT INTO ioh ( StocktakeID,StoreNumber , QRCode,AssetsNo,SerialNo,Description,Reason,DocumentID) SELECT " . $StocktakeID . " AS StocktakeID, overonhand.Costcenter,overonhand.CD_Barcode, overonhand.AssetsNo,overonhand.SerialNo,overonhand.Description,overonhand.Reason,overonhand.DocumentID FROM overonhand Where StocktakeID=  " . $StocktakeID;
    if ($stmt = $mysqli_asset->prepare($AppendOverOnHand)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error Append To IOH OverOnHand</h6>
        <?php
        }
    }


    //    // Query Append To IOH asset no tag
    $AppendOverOnHand = "INSERT INTO ioh ( StocktakeID,Counted_ID, QRCode ,Description,Reason,DocumentID ) SELECT " . $StocktakeID . " AS StocktakeID, tb_productdetail.PR_ID,tb_productdetail.PR_Barcode, tb_productdetail.PM_ProductMasterID,'' as Reason, '' as  DocumentID  FROM tb_productdetail WHERE (((tb_productdetail.PR_Barcode)=''));";
    //    $AppendOverOnHand = "INSERT INTO ioh ( StocktakeID,Counted_ID, QRCode ,Description,Reason,DocumentID ) SELECT " . $StocktakeID . " AS StocktakeID, counted_data.ID,counted_data.QRCode, counted_data.Description,counted_data.Reason,counted_data.DocumentID  FROM tb_productdetail WHERE (((tb_productdetail.PR_Barcode)='') and counted_data.StocktakeID=" . $StocktakeID . ");";
    if ($stmt = $mysqli_asset->prepare($AppendOverOnHand)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error Client Append To IOH</h6>
        <?php
        }
    }

    // Query Consolidate_Count
    $Consolidate_Count = "INSERT INTO `consolidated` (StocktakeID,Code,Quantity) SELECT " . $StocktakeID . ",`PR_Barcode`,`PR_Qty`   From `tb_productdetail` where `PR_Barcode` is not null ";
    if ($stmt = $mysqli_asset->prepare($Consolidate_Count)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error Consolidate_Count</h6>
        <?php
        }
    }
    //
    // Query UPDATE Count PCS
    $Update_IOH = "UPDATE ioh INNER JOIN consolidated ON ioh.QRCode = consolidated.Code SET ioh.Count = consolidated.Quantity Where ioh.StocktakeID =  " . $StocktakeID;
    if ($stmt = $mysqli_asset->prepare($Update_IOH)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error UPDATE IOH </h6>
        <?php
        }
    }

    // Query UPDATE Variance
    $Update_IOH_All = "UPDATE ioh SET ioh.Variance = `Count`-`Qty` Where ioh.StocktakeID =  " . $StocktakeID;
    if ($stmt = $mysqli_asset->prepare($Update_IOH_All)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error UPDATE Variance </h6>
    <?php
        }
    }
    //
    //    // update pending
    //    $UpdatePending = "UPDATE ioh SET ioh.StoreNumber = '".$Costcenter."' WHERE (((ioh.StoreNumber) Is Null));";
    //    if ($stmt = $mysqli_asset->prepare($UpdatePending)) {
    //
    //        if (!$stmt->execute()) {
    //            
    ?>
    <!--<h6 class="w3-text-red">Error update pending </h6>-->
    <?php
    //        }
    //    }
    //
    //
    //
    //    // update Status_Count = Short
    $UpdateStatusCountShort = "UPDATE ioh INNER JOIN `tb_product_lookup_table` ON ioh.QRCode = `tb_product_lookup_table`.PL_Barcode SET ioh.Status_Count = 'Short'
WHERE (((ioh.StocktakeID)=" . $StocktakeID . "));";
    //    $UpdateStatusCountShort = "UPDATE ioh INNER JOIN product_lookup_table ON ioh.QRCode = product_lookup_table.QRCode SET ioh.Status_Count = 'Short'
    //WHERE (((ioh.StocktakeID)=" . $StocktakeID . ") AND ((product_lookup_table.Costcenter)=" . $Costcenter . "));";
    if ($stmt = $mysqli_asset->prepare($UpdateStatusCountShort)) {

        if (!$stmt->execute()) {
    ?>
            <h6 class="w3-text-red">Error update Status_Count = Short</h6>
        <?php
        }
    }

    // update Status_Count = Match
    $UpdateStatusCountMatch = "UPDATE `tb_productdetail` INNER JOIN ioh ON `tb_productdetail`.PR_Barcode = ioh.QRCode SET ioh.Status_Count = 'Match'
WHERE (((ioh.StocktakeID)=" . $StocktakeID . "));";
    //    $UpdateStatusCountMatch = "UPDATE `tb_productdetail` INNER JOIN ioh ON `tb_productdetail`.PR_Barcode = ioh.QRCode SET ioh.Status_Count = 'Match'
    //WHERE (((ioh.StocktakeID)=" . $StocktakeID . ") AND ((counted_data.StocktakeID)=" . $StocktakeID . "));";
    if ($stmt = $mysqli_asset->prepare($UpdateStatusCountMatch)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error update Status_Count = Match</h6>
    <?php
        }
    }
    //
    //
    //
    //    // update Status_Count = Over
    //    $UpdateStatusCountOver = "UPDATE overonhand INNER JOIN ioh ON overonhand.CD_Barcode = ioh.QRCode SET ioh.Status_Count = 'Over'
    //WHERE (((ioh.StocktakeID)=" . $StocktakeID . ") AND ((overonhand.StocktakeID)=" . $StocktakeID . "));";
    //    if ($stmt = $mysqli_asset->prepare($UpdateStatusCountOver)) {
    //
    //        if (!$stmt->execute()) {
    //            
    //    
    ?>
    <!--<h6 class="w3-text-red">Error update Status_Count = Over</h6>-->
    <?php
    //        }
    //    }
    // update QRCode pading for asset notag
    $UpdateQRCodePading = "UPDATE `ioh` SET `QRCode`='Pending',ioh.Status_Count = 'Over',Reason = '' WHERE `QRCode` = '' and `StocktakeID` = " . $StocktakeID;
    if ($stmt = $mysqli_asset->prepare($UpdateQRCodePading)) {

        if (!$stmt->execute()) {
    ?>
            <h6 class="w3-text-red">Error QRCode pading for asset notag</h6>
        <?php
        }
    }

    // Update CountedID from ioh
    $UpdateCountedID = "UPDATE `tb_productdetail` INNER JOIN ioh ON `tb_productdetail`.`PR_Barcode` = ioh.QRCode SET `Counted_ID`= `tb_productdetail`.`PR_ID`";
    if ($stmt = $mysqli_asset->prepare($UpdateCountedID)) {

        if (!$stmt->execute()) {
        ?>
            <h6 class="w3-text-red">Error CountedID from ioh</h6>
    <?php
        }
    }
    //
    //
    //    // update Description from Counted_Data
    //    $UpdateDesc = "UPDATE counted_data INNER JOIN ioh ON counted_data.ID = ioh.Counted_ID SET ioh.AssetsNo=counted_data.AssetsNo, ioh.SerialNo =counted_data.SerialNo,  ioh.Description = counted_data.Description WHERE counted_data.StocktakeID =". $StocktakeID . " and ioh.StocktakeID=" . $StocktakeID ;
    //    if ($stmt = $mysqli_asset->prepare($UpdateDesc)) {
    //
    //        if (!$stmt->execute()) {
    //            
    ?>
    <!--<h6 class="w3-text-red">Error Description from Counted_Data</h6>-->
    <?php
    //        }
    //    }
    //    
    //    // update Reason
    //    $UpdateReason = "UPDATE ioh INNER JOIN counted_data ON ioh.Counted_ID = counted_data.ID SET ioh.Reason = counted_data.Reason,ioh.DocumentID = counted_data.DocumentID WHERE counted_data.StocktakeID =". $StocktakeID . " and ioh.StocktakeID=" . $StocktakeID ;
    //    if ($stmt = $mysqli_asset->prepare($UpdateReason)) {
    //
    //        if (!$stmt->execute()) {
    //            
    ?>
    <!--<h6 class="w3-text-red">Error Update Reason</h6>-->
    <?php
    //        }
    //    }
    //    
    //    
    //    
    // 
    //     // update clear null Reason
    //    $UpdateReason = "UPDATE ioh SET ioh.Reason = '' WHERE ioh.Reason = null and counted_data.StocktakeID =". $StocktakeID . " and ioh.StocktakeID=" . $StocktakeID ;
    //    if ($stmt = $mysqli_asset->prepare($UpdateReason)) {
    //
    //        if (!$stmt->execute()) {
    //            
    ?>
    <!--<h6 class="w3-text-red">Error update clear null Reason</h6>-->
<?php
    //        }
    //    }
}

function showStaffName($StaffID, $mysqli)
{
    $staffname = "";
    $sql = "SELECT `First NameTH`,`SurnameTH` FROM `Staff Table` WHERE `Staff ID` = '" . $StaffID . "'";
    $rs = $mysqli->query($sql);

    while ($row = $rs->fetch_array()) {
        $staffname = $row['First NameTH'] . " " . $row['SurnameTH'];
    }


    return $staffname;
}

function base64_to_jpeg($base64_string, $output_file)
{
    // open the output file for writing
    $ifp = fopen($output_file, 'wb');

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode(',', $base64_string);

    // we could add validation here with ensuring count( $data ) > 1
    fwrite($ifp, base64_decode($data[1]));

    // clean up the file resource
    fclose($ifp);

    return $output_file;
}

function chkDateTimeJob($mysqli, $dtStart, $dtFinish, $TargetPersonal)
{
    $result = false;
    $begin = new DateTime($dtStart);
    $end = new DateTime($dtFinish);

    for ($i = $begin; $i <= $end; $i->modify('+1 day')) {

        $d = $i->format("d");
        $m = $i->format("m");
        $Y = $i->format("Y");

        $rs = $mysqli->query("Select sum(job.Job_AmountStaff) AS TotalAmount,sum(job.Job_AmountSupervisor) AS TotalAmountSup FROM job_staff INNER JOIN job ON job_staff.Job_ID = job.Job_ID  Where job.Job_Status = 2 AND `dtDateTime` between '" . $Y . "-" . $m . "-" . $d . " 00:00:00.000000' AND '" . $Y . "-" . $m . "-" . $d . " 23:59:59.000000'");
        while ($rows = $rs->fetch_array()) {
            $rs = $mysqli->query("Select ManPower FROM carlendar Where dtDate = '" . $Y . "-" . $m . "-" . $d . "'");
            while ($rowM = $rs->fetch_array()) {
                $ManPower = $rowM['ManPower'];
            }


            $Stock = ($ManPower - ($rows['TotalAmount'] + $rows['TotalAmountSup']));


            if ($Stock < $TargetPersonal) {
                $result = true;
                break;
            }
        }
    }
    return $result;
}

function MessageDateTimeJob($mysqli, $dtStart, $dtFinish, $TargetPersonal)
{
    $result = array();
    $begin = new DateTime($dtStart);
    $end = new DateTime($dtFinish);

    for ($i = $begin; $i <= $end; $i->modify('+1 day')) {

        $d = $i->format("d");
        $m = $i->format("m");
        $Y = $i->format("Y");

        $rs = $mysqli->query("Select sum(job.Job_AmountStaff) AS TotalAmount,sum(job.Job_AmountSupervisor) AS TotalAmountSup FROM job_staff INNER JOIN job ON job_staff.Job_ID = job.Job_ID  Where job.Job_Status = 2 AND `dtDateTime` between '" . $Y . "-" . $m . "-" . $d . " 00:00:00.000000' AND '" . $Y . "-" . $m . "-" . $d . " 23:59:59.000000'");
        while ($rows = $rs->fetch_array()) {
            $rs = $mysqli->query("Select ManPower FROM carlendar Where dtDate = '" . $Y . "-" . $m . "-" . $d . "'");
            while ($rowM = $rs->fetch_array()) {
                $ManPower = $rowM['ManPower'];
            }


            $Stock = ($ManPower - ($rows['TotalAmount'] + $rows['TotalAmountSup']));


            if ($Stock < $TargetPersonal) {
                // echo $i->format("d/m/Y");
                array_push($result, $i->format("d/m/Y"));
            }
        }
    }

    return $result;
}

function StaffReadyToJob($mysqli, $dtStart, $dtFinish)
{
    $sql_clear = "Truncate Table `staff_not_ready_by_job_staff_work`";
    $mysqli->query($sql_clear);
    $rs = $mysqli->query("Select Job_ID,Staff_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work` Where `Job_Staff_Work_DateStart` >=CURRENT_DATE()  order by Job_Staff_Work_DateStart");
    while ($row = $rs->fetch_array()) {
        //        echo "Select Job_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Staff_ID = '" . $Staff_ID . "'";
        $Job_dtStart = new DateTime($row['Job_Staff_Work_DateStart']);
        $Job_dtFinish = new DateTime($row['Job_Staff_Work_DateFinish']);
        //        $S_dtStart = new DateTime($dtStart);
        //        $S_dtFinish = new DateTime($dtFinish);
        for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
            //   echo $dtStart. " === ".$dtFinish."----".$i->format("Y-m-d");
            //  echo "<br>";
            $S_dtStart = new DateTime($dtStart);
            $S_dtFinish = new DateTime($dtFinish);
            for ($S = $S_dtStart; $S <= $S_dtFinish; $S->modify('+1 day')) {
                //                echo $i->format("Y-m-d") . " " . $S->format("Y-m-d");
                //                echo "<br>";
                if ($i->format("Y-m-d") == $S->format("Y-m-d")) {
                    $sql_insert = "insert into `staff_not_ready_by_job_staff_work` (staff_id) values('" . $row['Staff_ID'] . "')";
                    $mysqli->query($sql_insert);
                }
            }
        }
    }
}

// Check 1 person per day
// ตรวจสอบ 1 คน ต่อ 1 วัน
function chkDateTimeJobStaffWork($mysqli, $Staff_ID, $dtStart, $dtFinish)
{
    $result = false;
    $rs = $mysqli->query("Select Job_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Staff_ID = '" . $Staff_ID . "' order by Job_Staff_Work_DateStart");
    while ($row = $rs->fetch_array()) {
        //        echo "Select Job_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Staff_ID = '" . $Staff_ID . "'";
        $Job_dtStart = new DateTime($row['Job_Staff_Work_DateStart']);
        $Job_dtFinish = new DateTime($row['Job_Staff_Work_DateFinish']);
        //        $S_dtStart = new DateTime($dtStart);
        //        $S_dtFinish = new DateTime($dtFinish);
        for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
            //            echo $i->format("Y-m-d");
            //            echo "<br>";
            $S_dtStart = new DateTime($dtStart);
            $S_dtFinish = new DateTime($dtFinish);
            for ($S = $S_dtStart; $S <= $S_dtFinish; $S->modify('+1 day')) {
                //                echo $i->format("Y-m-d") . " " . $S->format("Y-m-d");
                //                echo "<br>";
                if ($i->format("Y-m-d") == $S->format("Y-m-d")) {
                    //                    echo "Hello";
                    $result = true;
                    break;
                }
            }
        }
    }
    return $result;
}

function MessageDateTimeJobStaffWork($mysqli, $Staff_ID, $dtStart, $dtFinish)
{

    $rs = $mysqli->query("Select Job_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Staff_ID = '" . $Staff_ID . "' order by Job_Staff_Work_DateStart");
    while ($row = $rs->fetch_array()) {
        $Job_dtStart = new DateTime($row['Job_Staff_Work_DateStart']);
        $Job_dtFinish = new DateTime($row['Job_Staff_Work_DateFinish']);

        for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
            //            echo $i->format("d/m/Y");
            //            echo "<br>";
            $S_dtStart = new DateTime($dtStart);
            $S_dtFinish = new DateTime($dtFinish);
            for ($S = $S_dtStart; $S <= $S_dtFinish; $S->modify('+1 day')) {
                if ($i->format("Y-m-d") == $S->format("Y-m-d")) {
                    echo $i->format("d/m/Y");
                    echo "<br>";
                }
            }
        }
    }
}

// Check 1 Job / All Slot Day
// ตรวจสอบว่า งานนี้ เต็มแล้วหรือยัง
function chkAmountJobStaffWork($mysqli, $Job_ID, $TargetAmount)
{
    $result = false;
    $Jrs = $mysqli->query("SELECT (abs(datediff(`Job_DateTimeStart`,`Job_DateTimeFinish`))+1)*(`Job_AmountStaff`+`Job_AmountSupervisor`) AS TotalManPower FROM `job`  Where Job_ID = '" . $Job_ID . "'");
    while ($Jrow = $Jrs->fetch_array()) {
        $TotalManPower = $Jrow['TotalManPower'];
    }

    $TotalAmountNow = 0;
    $rs = $mysqli->query("Select sum(abs(datediff(`Job_Staff_Work_DateStart`,`Job_Staff_Work_DateFinish`))+1) AS TotalAmountNow FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $rs->fetch_array()) {
        $TotalAmountNow = $row['TotalAmountNow'];
    }

    // echo $TotalManPower . " < " . $TotalAmountNow;


    if ($TotalManPower < ($TotalAmountNow + $TargetAmount)) {
        $result = true;
    }

    return $result;
}

// ตรวจสอบ พนักงาน ปัจจุบันมีกี่คน/ที่ต้องการกี่คน
function MessageAmountJobStaffWork($mysqli, $Job_ID)
{
    $result = "";
    $Jrs = $mysqli->query("SELECT (abs(datediff(`Job_DateTimeStart`,`Job_DateTimeFinish`))+1)*(`Job_AmountStaff`+`Job_AmountSupervisor`) AS TotalManPower FROM `job`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $Jrs->fetch_array()) {
        $TotalManPower = $row['TotalManPower'];
    }
    $TotalAmountNow = 0;

    $rs = $mysqli->query("SELECT sum(abs(datediff(`Job_Staff_Work_DateStart`,`Job_Staff_Work_DateFinish`))+1) AS TotalAmountNow FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $rs->fetch_array()) {
        $TotalAmountNow = $row['TotalAmountNow'];
    }
    if (is_null($TotalAmountNow)) {
        $TotalAmountNow = 0;
    }
    $result = $TotalAmountNow . "/" . $TotalManPower;


    return $result;
}

// ถ้าพนักงาน เต็มแล้ว ให้แจ้ง ไปที่ Line
function MessageAmountJobStaffWork_AlertLine($mysqli, $Job_ID)
{

    $Jrs = $mysqli->query("SELECT (abs(datediff(`Job_DateTimeStart`,`Job_DateTimeFinish`))+1)*(`Job_AmountStaff`+`Job_AmountSupervisor`) AS TotalManPower FROM `job`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $Jrs->fetch_array()) {
        $TotalManPower = $row['TotalManPower'];
    }
    $TotalAmountNow = 0;

    $rs = $mysqli->query("SELECT sum(abs(datediff(`Job_Staff_Work_DateStart`,`Job_Staff_Work_DateFinish`))+1) AS TotalAmountNow FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $rs->fetch_array()) {
        $TotalAmountNow = $row['TotalAmountNow'];
    }
    if (is_null($TotalAmountNow)) {
        $TotalAmountNow = 0;
    }

    if ($TotalAmountNow == $TotalManPower) {
        MessageStaffWorkJob($mysqli, $Job_ID);
    }
}

function MessageAmountJobStaffWork_Diff($mysqli, $Job_ID, $TargetAmount)
{
    $result = "";
    $Jrs = $mysqli->query("SELECT (abs(datediff(`Job_DateTimeStart`,`Job_DateTimeFinish`))+1)*(`Job_AmountStaff`+`Job_AmountSupervisor`) AS TotalManPower FROM `job`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $Jrs->fetch_array()) {
        $TotalManPower = $row['TotalManPower'];
    }
    $TotalAmountNow = 0;

    $rs = $mysqli->query("Select sum(abs(datediff(`Job_Staff_Work_DateStart`,`Job_Staff_Work_DateFinish`))+1) AS TotalAmountNow FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $rs->fetch_array()) {
        $TotalAmountNow = $row['TotalAmountNow'];
    }
    $result = abs($TotalManPower - ($TotalAmountNow + $TargetAmount));


    return $result;
}

// Check 1 Job / 1 Day
function chkOneJobSlotDay_BAK($mysqli, $Job_ID)
{
    $result = false;
    $arrDay = [];
    $Totals = 0;
    $rs = $mysqli->query("Select Job_ID,Job_DateTimeStart,Job_DateTimeFinish,Job_AmountStaff FROM `job`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $rs->fetch_array()) {
        $Job_dtStart = new DateTime($row['Job_DateTimeStart']);
        $Job_dtFinish = new DateTime($row['Job_DateTimeFinish']);

        for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
            $TotalAmount = 0;
            $rsJS = $mysqli->query("Select Staff_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "'");
            while ($rowJS = $rsJS->fetch_array()) {
                $JobSW_dtStart = new DateTime($rowJS['Job_Staff_Work_DateStart']);
                $JobSW_dtFinish = new DateTime($rowJS['Job_Staff_Work_DateFinish']);
                echo $rowJS['Staff_ID'];
                echo "<br>";

                for ($S = $JobSW_dtStart; $S <= $JobSW_dtFinish; $S->modify('+1 day')) {
                    //                    $rsJS2 = $mysqli->query("Select Staff_ID FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "' AND Job_Staff_Work_DateStart between '" . $S->format("Y-m-d 00:00:00") . "' AND '" . $S->format("Y-m-d 23:59:59") . "' Group By Staff_ID");
                    //                    while ($rowJS2 = $rsJS2->fetch_array()) {
                    //                        $TotalAmount++;
                    //                    }

                    if ($i->format("Y-m-d") == $S->format("Y-m-d")) {
                        $Totals++;
                    }
                    echo $i->format("Y-m-d") . " " . $S->format("Y-m-d") . " = " . $Totals;
                    echo "<br>";
                    if ($TotalAmount > $row['Job_AmountStaff']) {
                        $result = true;
                        break;
                    }
                }
                echo "-----------------";
            }
        }
        //        $Job_dtStart = new DateTime($row['Job_DateTimeStart']);
        //        $Job_dtFinish = new DateTime($row['Job_DateTimeFinish']);
        //        for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
        //            foreach ($arrDay[$i->format("Y-m-d")] as $item) {
        //                $sum += $item;
        //            }
        //            echo "Totals:".$sum;
        //        }
        return $result;
    }

    return $result;
}

function chkOneJobSlotDay($mysqli, $Job_ID, $FocusdtStart, $FocusdtFinish, $Position_ID)
{
    $result = false;
    $arrDay = array();
    if ($Position_ID == 2) {

        // กำหนดค่าเริ่มต้น แต่ละวัน ให้เป็น 0
        $rs = $mysqli->query("Select Job_ID,Job_DateTimeStart,Job_DateTimeFinish,Job_AmountStaff FROM `job`  Where Job_ID = '" . $Job_ID . "'");
        while ($row = $rs->fetch_array()) {
            $Job_dtStart = new DateTime($row['Job_DateTimeStart']);
            $Job_dtFinish = new DateTime($row['Job_DateTimeFinish']);
            for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
                $arrDay[$i->format("Y-m-d")] = 0;
            }
        }

        //นับว่า ตำแหน่ง พนักงานทำความสะอาด แต่ละวัน มีจำนวนกี่คน
        $rsJS = $mysqli->query("Select Staff_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "' AND Position_ID = 2");
        while ($rowJS = $rsJS->fetch_array()) {
            $JobSW_dtStart = new DateTime($rowJS['Job_Staff_Work_DateStart']);
            $JobSW_dtFinish = new DateTime($rowJS['Job_Staff_Work_DateFinish']);
            for ($S = $JobSW_dtStart; $S <= $JobSW_dtFinish; $S->modify('+1 day')) {
                $arrDay[$S->format("Y-m-d")]++;
            }
        }


        $rsF = $mysqli->query("Select Job_ID,Job_DateTimeStart,Job_DateTimeFinish,Job_AmountSupervisor,Job_AmountStaff FROM `job`  Where Job_ID = '" . $Job_ID . "'");
        while ($rowF = $rsF->fetch_array()) {
            $Job_dtStart = new DateTime($rowF['Job_DateTimeStart']);
            $Job_dtFinish = new DateTime($rowF['Job_DateTimeFinish']);

            for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
                $F_dtStart = new DateTime($FocusdtStart);
                $F_dtFinish = new DateTime($FocusdtFinish);
                for ($f = $F_dtStart; $f <= $F_dtFinish; $f->modify('+1 day')) {
                    if ($arrDay[$i->format("Y-m-d")] >= $rowF['Job_AmountStaff'] && $i->format("Y-m-d") == $f->format("Y-m-d")) {
                        $result = true;
                        break;
                    }
                }
            }
        }
    }

    return $result;
}

function chkOneJobSlotDaySupervisor($mysqli, $Job_ID, $FocusdtStart, $FocusdtFinish, $Position_ID)
{
    $result = false;
    $arrDay = array();
    if ($Position_ID == 3) {
        // กำหนดค่าเริ่มต้น แต่ละวัน ให้เป็น 0
        $rs = $mysqli->query("Select Job_ID,Job_DateTimeStart,Job_DateTimeFinish,Job_AmountStaff FROM `job`  Where Job_ID = '" . $Job_ID . "'");
        while ($row = $rs->fetch_array()) {
            $Job_dtStart = new DateTime($row['Job_DateTimeStart']);
            $Job_dtFinish = new DateTime($row['Job_DateTimeFinish']);
            for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
                $arrDay[$i->format("Y-m-d")] = 0;
            }
        }

        //นับว่า ตำแหน่ง พนักงานทำความสะอาด แต่ละวัน มีจำนวนกี่คน
        $rsJS = $mysqli->query("Select Staff_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "' AND Position_ID = 3");
        while ($rowJS = $rsJS->fetch_array()) {
            $JobSW_dtStart = new DateTime($rowJS['Job_Staff_Work_DateStart']);
            $JobSW_dtFinish = new DateTime($rowJS['Job_Staff_Work_DateFinish']);
            for ($S = $JobSW_dtStart; $S <= $JobSW_dtFinish; $S->modify('+1 day')) {
                $arrDay[$S->format("Y-m-d")]++;
            }
        }


        $rsF = $mysqli->query("Select Job_ID,Job_DateTimeStart,Job_DateTimeFinish,Job_AmountSupervisor,Job_AmountStaff FROM `job`  Where Job_ID = '" . $Job_ID . "'");
        while ($rowF = $rsF->fetch_array()) {
            $Job_dtStart = new DateTime($rowF['Job_DateTimeStart']);
            $Job_dtFinish = new DateTime($rowF['Job_DateTimeFinish']);

            for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
                $F_dtStart = new DateTime($FocusdtStart);
                $F_dtFinish = new DateTime($FocusdtFinish);
                for ($f = $F_dtStart; $f <= $F_dtFinish; $f->modify('+1 day')) {
                    if ($arrDay[$i->format("Y-m-d")] >= $rowF['Job_AmountSupervisor'] && $i->format("Y-m-d") == $f->format("Y-m-d")) {
                        $result = true;
                        break;
                    }
                }
            }
        }
    }

    return $result;
}

// แสดงจำนวนพนักงาน ต่อ วัน
function MessageOneJobSlotDay($mysqli, $Job_ID)
{
    $result = "";
    $arrDay = array();
    $arrDaySup = array();

    $rs = $mysqli->query("Select Job_ID,Job_DateTimeStart,Job_DateTimeFinish,Job_AmountSupervisor,Job_AmountStaff FROM `job`  Where Job_ID = '" . $Job_ID . "'");
    while ($row = $rs->fetch_array()) {
        $Job_dtStart = new DateTime($row['Job_DateTimeStart']);
        $Job_dtFinish = new DateTime($row['Job_DateTimeFinish']);
        for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
            $arrDay[$i->format("Y-m-d")] = 0;
            $arrDaySup[$i->format("Y-m-d")] = 0;
        }
    }

    $rsJS = $mysqli->query("Select Staff_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "' AND Position_ID = 3");
    while ($rowJS = $rsJS->fetch_array()) {
        $JobSW_dtStart = new DateTime($rowJS['Job_Staff_Work_DateStart']);
        $JobSW_dtFinish = new DateTime($rowJS['Job_Staff_Work_DateFinish']);
        for ($S = $JobSW_dtStart; $S <= $JobSW_dtFinish; $S->modify('+1 day')) {
            $arrDaySup[$S->format("Y-m-d")]++;
        }
    }

    $rsJS = $mysqli->query("Select Staff_ID,Job_Staff_Work_DateStart,Job_Staff_Work_DateFinish FROM `job_staff_work`  Where Job_ID = '" . $Job_ID . "' AND Position_ID = 2");
    while ($rowJS = $rsJS->fetch_array()) {
        $JobSW_dtStart = new DateTime($rowJS['Job_Staff_Work_DateStart']);
        $JobSW_dtFinish = new DateTime($rowJS['Job_Staff_Work_DateFinish']);
        for ($S = $JobSW_dtStart; $S <= $JobSW_dtFinish; $S->modify('+1 day')) {
            $arrDay[$S->format("Y-m-d")]++;
        }
    }

    $rsF = $mysqli->query("Select Job_ID,Job_DateTimeStart,Job_DateTimeFinish,Job_AmountSupervisor,Job_AmountStaff FROM `job`  Where Job_ID = '" . $Job_ID . "'");
    while ($rowF = $rsF->fetch_array()) {
        $Job_dtStart = new DateTime($rowF['Job_DateTimeStart']);
        $Job_dtFinish = new DateTime($rowF['Job_DateTimeFinish']);

        for ($i = $Job_dtStart; $i <= $Job_dtFinish; $i->modify('+1 day')) {
            
            if($rowF['Job_AmountSupervisor'] > 0){
                $result .= $i->format("d/m/Y");
                $result .= "หัวหน้างาน";
                if ($arrDaySup[$i->format("Y-m-d")] >= $rowF['Job_AmountSupervisor']) {
                    $result .= " = เต็ม";
                    $result .= "<br>";
                } else {
                    $result .= " = " . ($rowF['Job_AmountSupervisor'] - $arrDaySup[$i->format("Y-m-d")]);
                    $result .= "<br>";
                }
            }else{

            }
            if($rowF['Job_AmountStaff'] > 0){
                $result .= $i->format("d/m/Y");
                $result .= "Cleaning";
                if ($arrDay[$i->format("Y-m-d")] >= $rowF['Job_AmountStaff']) {
                    $result .= " = เต็ม";
                    $result .= "<br>";
                } else {
                    $result .= " = " . ($rowF['Job_AmountStaff'] - $arrDay[$i->format("Y-m-d")]);
                    $result .= "<br>";
                }
            }else{

            }
            
        }
    }

    return $result;
}

function QueryProcessJobStaffByDate($mysqli)
{
    if ($stmt = $mysqli->prepare("Truncate Table `job_staff`")) {
        if ($stmt->execute()) {
            $sql = "SELECT `Staff Table`.`Staff ID`,`Staff Table`.`First NameTH` "
                . "FROM `Staff Table` Order By `Staff Table`.`First NameTH`;";
            $rs = $mysqli->query($sql);
            while ($row = $rs->fetch_array()) {
                $sqlJ = "Select `Job_ID`,`Job_DateTimeStart`,`Job_DateTimeFinish` From job Where `Job_Staff_AE` = '" . $row['Staff ID'] . "'";
                $rsJ = $mysqli->query($sqlJ);
                while ($rowJ = $rsJ->fetch_array()) {
                    //                echo $row['Staff ID'] . " ".$row['First NameTH'] . " : " . $rowJ['Job_DateTimeStart'] . " - ". $rowJ['Job_DateTimeFinish'];      //Debug
                    $dateStart = new DateTime($rowJ['Job_DateTimeStart']);
                    $dateFinish = new DateTime($rowJ['Job_DateTimeFinish']);
                    for ($i = $dateStart; $i <= $dateFinish; $dateStart->modify('+1 day')) {

                        $sqlI = "INSERT INTO `job_staff` (`Job_ID`,`Staff_ID`, `dtDateTime`) VALUES ('" . $rowJ['Job_ID'] . "','" . $row['Staff ID'] . "','" . $i->format('Y-m-d') . "')";
                        $mysqli->query($sqlI);
                    }
                }
            }
        }
    } else {
        echo mysqli_stmt_error($stmt);
    }
}

function QueryProcessJobStaffByDate_StaffWorking($mysqli)
{
    if ($stmt = $mysqli->prepare("Truncate Table `job_staff_work_date`")) {
        if ($stmt->execute()) {
            $sql = "SELECT `Staff Table`.`Staff ID`,`Staff Table`.`First NameTH` "
                . "FROM `Staff Table` Order By `Staff Table`.`First NameTH`;";
            $rs = $mysqli->query($sql);
            while ($row = $rs->fetch_array()) {
                $sqlJ = "Select `Job_ID`,`Job_DateTimeStart`,`Job_DateTimeFinish` From job Where `Job_Staff_AE` = '" . $row['Staff ID'] . "'";
                $rsJ = $mysqli->query($sqlJ);
                while ($rowJ = $rsJ->fetch_array()) {
                    //                echo $row['Staff ID'] . " ".$row['First NameTH'] . " : " . $rowJ['Job_DateTimeStart'] . " - ". $rowJ['Job_DateTimeFinish'];      //Debug
                    $dateStart = new DateTime($rowJ['Job_DateTimeStart']);
                    $dateFinish = new DateTime($rowJ['Job_DateTimeFinish']);
                    for ($i = $dateStart; $i <= $dateFinish; $dateStart->modify('+1 day')) {

                        $sqlI = "INSERT INTO `job_staff` (`Job_ID`,`Staff_ID`, `dtDateTime`) VALUES ('" . $rowJ['Job_ID'] . "','" . $row['Staff ID'] . "','" . $i->format('Y-m-d') . "')";
                        $mysqli->query($sqlI);
                    }
                }
            }
        }
    } else {
        echo mysqli_stmt_error($stmt);
    }
}

function QueryProcessJobStaffWorkByDate_StaffWorking($mysqli, $Job_ID)
{
    if ($stmt = $mysqli->prepare("Truncate Table `job_staff_work_date`")) {
        if ($stmt->execute()) {

            $sqlJ = "SELECT `Staff_ID`,`Job_Staff_Work_DateStart`,`Job_Staff_Work_DateFinish` FROM job_staff_work WHERE `Job_ID` = '" . $Job_ID . "'";
            $rsJ = $mysqli->query($sqlJ);
            while ($rowJ = $rsJ->fetch_array()) {
                //                echo $row['Staff ID'] . " ".$row['First NameTH'] . " : " . $rowJ['Job_DateTimeStart'] . " - ". $rowJ['Job_DateTimeFinish'];      //Debug
                $dateStart = new DateTime($rowJ['Job_Staff_Work_DateStart']);
                $dateFinish = new DateTime($rowJ['Job_Staff_Work_DateFinish']);
                for ($i = $dateStart; $i <= $dateFinish; $dateStart->modify('+1 day')) {

                    $sqlI = "INSERT INTO `job_staff_work_date` (`Job_ID`,`Staff_ID`, `dtDateTime`) VALUES ('" . $Job_ID . "','" . $rowJ['Staff_ID'] . "','" . $i->format('Y-m-d') . "')";
                    $mysqli->query($sqlI);
                }
            }
        }
    } else {
        echo mysqli_stmt_error($stmt);
    }
}

function MessageStaffWorkJob($mysqli, $Job_ID)
{

    QueryProcessJobStaffWorkByDate_StaffWorking($mysqli, $Job_ID);

    if ($stmt = $mysqli->prepare("SELECT `Store_ID`,`Job_ClientName`,`Job_Staff_AE`,`Job_DateTimeStart`,`Job_DateTimeFinish`,`Job_Status`,`Job_AmountSupervisor`,`Job_AmountStaff`,`Job_Remark` FROM `job` WHERE `Job_ID` = ? ")) {
        $stmt->bind_param('s', $Job_ID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($Store_ID, $Job_ClientName, $Job_Staff_AE, $Job_DateTimeStart, $Job_DateTimeFinish, $Job_Status, $Job_AmountSupervisor, $Job_AmountStaff, $Job_Remark);
        $stmt->fetch();
    } else {
        echo "Error JobID";
    }

    $dtStart = new DateTime($Job_DateTimeStart);
    $dtFinish = new DateTime($Job_DateTimeFinish);

    $Message = $Job_ID . "\r\n";
    $Message .= StaffInfo($mysqli, $Job_Staff_AE) . "\r\n";
    $Message .= "Site ID : " . str_pad($Store_ID, 4, '0', STR_PAD_LEFT) . "\r\nลูกค้า : " . $Job_ClientName . "\r\n";
    $Message .= "- หัวหน้างาน " . $Job_AmountSupervisor . " คน/วัน\r\n";
    $Message .= "- พนักงาน " . $Job_AmountStaff . " คน/วัน\r\n";
    $Message .= "รวมจำนวนคนทั้งสิ้น " .  ($Job_AmountStaff + $Job_AmountSupervisor) . " คน/วัน\r\n";

    $Message .= "จำนวนวันทำงาน " . (date_diff($dtStart, $dtFinish)->format("%d") + 1) . " วัน\r\n";


    $condition = "";
    $sqlR = "SELECT Requirement.Requirement_Name
FROM Requirement INNER JOIN (job INNER JOIN job_requirement ON job.Job_ID = job_requirement.Job_ID) ON Requirement.Requirement_ID = job_requirement.Requirement_ID
WHERE (((job.Job_ID)='" . $Job_ID . "'));
";
    $no = 1;
    $rsR = $mysqli->query($sqlR);
    while ($rowR = $rsR->fetch_array()) {
        $condition .= $no . ". ";
        $condition .= $rowR['Requirement_Name'];
        $condition .= ",\r\n";
        $no++;
    }
    if ($condition == "") {
        $condition = "ปกติ";
    } else {
        $Message .= "รายละเอียดงาน \r\n" . $condition . "\r\n";
    }


    $Message .= "รายชื่อพนักงาน\r\n";
    for ($i = $dtStart; $i <= $dtFinish; $i->modify('+1 day')) {
        $sql = "SELECT `Staff Table`.PrefixTH, `Staff Table`.`First NameTH`, `Staff Table`.SurnameTH
        FROM job_staff_work_date INNER JOIN `Staff Table` ON job_staff_work_date.Staff_ID = `Staff Table`.`Staff ID`
        WHERE job_staff_work_date.Job_ID=? AND job_staff_work_date.dtDateTime = ?
        GROUP BY `Staff Table`.PrefixTH, `Staff Table`.`First NameTH`, `Staff Table`.SurnameTH, job_staff_work_date.dtDateTime;
    ";

        $Message .= $i->format("d/m/Y") . "\r\n";
        $dtDateTime = $i->format("Y-m-d 00:00:00");
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('ss', $Job_ID, $dtDateTime);
            $stmt->execute();
            $stmt->bind_result($PrefixTH, $FirstNameTH, $SurnameTH);
        }

        while ($stmt->fetch()) {
            $Message .= $PrefixTH . $FirstNameTH . $SurnameTH . "\r\n";
        }
    }
    $res = notify_message($Message, Token);
}

function notify_message($message, $token)
{
    $queryData = array('message' => $message);
    $queryData = http_build_query($queryData, '', '&');
    $headerOptions = array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                . "Authorization: Bearer " . $token . "\r\n"
                . "Content-Length: " . strlen($queryData) . "\r\n",
            'content' => $queryData
        ),
    );
    $context = stream_context_create($headerOptions);
    $result = file_get_contents(LINE_API, FALSE, $context);
    $res = json_decode($result);
    return $res;
}

function StaffInfo($mysqli, $Staff_ID)
{
    $result = "";
    $sql = "Select `Staff ID`, Concat(`First NameTH`,' ',`SurnameTH`,' (',`Nickname`,')') as StaffName FROM `Staff Table` WHERE `Staff ID` = '" . $Staff_ID . "'";
    $rs = $mysqli->query($sql);
    while ($row = $rs->fetch_array()) {
        $result = $row['StaffName'];
    }
    return $result;
}

function StaffIsPosition($mysqli, $Staff_ID)
{
    $result = "";
    $sql = "Select FullTime_Position FROM `Staff Table` WHERE `Staff ID` = '" . $Staff_ID . "'";
    $rs = $mysqli->query($sql);
    while ($row = $rs->fetch_array()) {
        $result = $row['FullTime_Position'];
    }
    return $result;
}

function checkPositionWorkInvalid($StaffPositionMain, $WorkPosition)
{
    $result = false;
    if ($StaffPositionMain == 3) {
        if ($WorkPosition == 2) {
            $result = false;
        } elseif ($WorkPosition == 3) {
            $result = false;
        }
    } elseif ($StaffPositionMain == 2) {
        if ($WorkPosition == 2) {
            $result = false;
        } elseif ($WorkPosition == 3) {
            $result = true;
        }
    }
    return $result;
}

function left($str, $length)
{
    return substr($str, 0, $length);
}

function right($str, $length)
{
    return substr($str, -$length);
}
