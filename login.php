<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>MakEats - Logging In</title>
    <link rel="stylesheet" type="text/css" href="loggedOutInStyles.css">
    <script src="scripts/jquery-1.11.2.min.js"></script>
    <script>
        $(document).ready(function(){
            window.open("index.php","_self")
        });
    </script>
</head>
<body id="bodyLoggedOut">
<?php
include_once "php/pdo.php";
$pageTitle = "Register";

if(!empty($_POST['login_username'])):
    include_once "php/class.users.inc.php";
    $users = new users($db);
    $users->accountLogin();
endif;
?>
<div id="headerLoggedOut">
    <a href="#" id="logoLink">MakEat</a>
</div>
<div id="contentLoggingIn">
    <h1>Logging In</h1>
    <div id="loaderGif"><img src="images/ajax-loader.gif" alt="loading content" /></div>
</div>
</body>
</html>