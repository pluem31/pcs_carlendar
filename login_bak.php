<!DOCTYPE html>
<?php
include_once './db_connect.php';
include_once './functions.php';
sec_session_start();
?>
<html>
 
    <!-- iCheck -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <script type="text/JavaScript" src="assets/js/sha512.js"></script> 
    <script type="text/JavaScript" src="assets/js/forms.js"></script>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
            <img src="assets/images/logo.jpg" width="120px" />
                <br>
                <b><?= CompanyAcronym; ?></b> <?= CompanyName; ?>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <p>
                    <?php
                    loadCookie($mysqli);
                    ?> 
                    <?php
                    if (isset($_COOKIE['email_login']) && isset($_COOKIE['password_login'])) {
                        echo $_COOKIE['email_login'] . " " . $_COOKIE['password_login'];
                    }
                    ?></p>

                <form action ="process_login.php" method="post">
                    <div class="form-group has-feedback">
                        <input type="email" class="form-control" id="email" name="email" placeholder="รหัสพนักงาน" >
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="password" id="Password" class="form-control" placeholder="Password" >
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    <input id="remember" type="checkbox"> จดจำครั้งต่อไป
                                </label>
                            </div>
                        </div>
                        <div id="MsgDiv"></div>
                        <!-- /.col -->
                        <div class="col-xs-4">
                            <button type="button" onclick="saveCookie(email.value, Password.value, document.getElementById('remember').checked);formhash(this.form, this.form.password);" class="btn btn-primary btn-block btn-flat">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>


                <!-- /.social-auth-links -->

                <!--<a href="#">I forgot my password</a>-->

            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->

       
      

        <!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>--> 
        <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/south-street/jquery-ui.css" rel="stylesheet"> 
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script>

                                function getXMLHTTP() { //fuction to return the xml http object
                                    var xmlhttp = false;
                                    try {
                                        xmlhttp = new XMLHttpRequest();
                                    } catch (e) {
                                        try {
                                            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                                        } catch (e) {
                                            try {
                                                xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
                                            } catch (e1) {
                                                xmlhttp = false;
                                            }
                                        }
                                    }

                                    return xmlhttp;
                                }

                                $(function () {
                                    $('input').iCheck({
                                        checkboxClass: 'icheckbox_square-blue',
                                        radioClass: 'iradio_square-blue',
                                        increaseArea: '20%' // optional
                                    });
                                });



                                function saveCookie(email, password, remember) {
                                    if (remember) {
                                        var strURL = "process_cookie_login.php";
                                        var params = "email=" + email + "&password=" + password + "&remember=" + remember;
//                                       alert(params);
                                        var req = getXMLHTTP();
                                        req.open("POST", strURL, true);
                                        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                        req.setRequestHeader("Content-length", params.length);
                                        req.setRequestHeader("Connection", "close");
                                        if (req) {

                                            req.onreadystatechange = function () {
                                                if (req.readyState == 4) {
                                                    // only if "OK"
                                                    if (req.status == 200) {
//                                                        document.getElementById('MsgDiv').innerHTML = req.responseText;
                                                    } else {
                                                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                                                    }
                                                }
                                            };
                                            req.send(params);
                                        }
                                    }
                                }
        </script>
    </body>
</html>
