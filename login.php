<!DOCTYPE html>
<?php
include_once './db_connect.php';
include_once './functions.php';
sec_session_start();
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script type="text/JavaScript" src="assets/js/sha512.js"></script>
    <script type="text/JavaScript" src="assets/js/forms.js"></script>
    <style>
        .login {
            min-height: 100vh;
        }

        .bg-image {
            background-image: url('assets/images/bg_login.jpg');
            background-size: cover;
            background-position: center;
        }

        .login-heading {
            font-weight: 300;
        }

        .btn-login {
            font-size: 0.9rem;
            letter-spacing: 0.05rem;
            padding: 0.75rem 1rem;
        }
    </style>
</head>

<body>
    <?php
    loadCookie($mysqli);
    ?>
    <?php
    if (isset($_COOKIE['email_login']) && isset($_COOKIE['password_login'])) {
        echo $_COOKIE['email_login'] . " " . $_COOKIE['password_login'];
    }
    ?>
    <form action="process_login.php" method="post">
        <div class="container-fluid ps-md-0">
            <div class="row g-0">
                <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
                <div class="col-md-8 col-lg-6">
                    <div class="login d-flex align-items-center py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-9 col-lg-8 mx-auto">
                                    <h3 class="login-heading mb-4"><?= CompanyAcronym; ?></h3>

                                    <!-- Sign In Form -->
                                    <form>
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="รหัสพนักงาน">
                                            <label for="floatingInput">รหัสพนักงาน</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="password" id="Password" placeholder="รหัสผ่าน">
                                            <label for="floatingPassword">รหัสผ่าน</label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" value="" id="remember">
                                            <label class="form-check-label" for="rememberPasswordCheck">
                                                Remember password
                                            </label>
                                        </div>

                                        <div class="d-grid">
                                            <button class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" type="button" onclick="saveCookie(email.value, Password.value, document.getElementById('remember').checked);formhash(this.form, this.form.password);">Sign in</button>

                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</body>
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

                req.onreadystatechange = function() {
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

</html>