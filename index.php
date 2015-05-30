<?php

session_start();
// Check if user has already logged in to the application
if (isset($_SESSION['access_token']) || isset($_SESSION['access_token']['oauth_token']) || isset($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: home.php');
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Twittery-Tweet</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- <link rel="icon" type="img/ico" href="images/favicon.ico">-->
    <style>


        .full {
            background: url('assets/img/bg.gif') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            background-size: cover;
            -o-background-size: cover;
        }
    </style>
</head>
<body class="full">
<div class="">
    <div class="">


        <div class="col-sm-12 text-center" style="margin-top: 10%;text-align: center">
            <?php

            if (isset($_SESSION['error']) && !empty($_SESSION['error'])&& isset($_GET['error'])) {
                ?>
                <div class="col-sm-12 text-center">
                <span class="alert alert-danger col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2 bold">
                    <b>Twetter auth Error: <?php echo $_SESSION['error'] ?>.&nbsp;&nbsp;&nbsp;Please try again.</b>
                </span>
                </div>
                <?php
                $_SESSION['error_88'] = false;

            }
            ?>

            <div>
                <img src="assets/img/logo2.png" alt="Sign in with Twitter"/>
                <center><a href="login.php"><img src="assets/img/login-twitter.png" alt="Sign in with Twitter"/>
                    </a></center>
            </div>

        </div>

    </div>
</div>


<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>

</html>
