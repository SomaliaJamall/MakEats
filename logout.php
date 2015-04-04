<?php

session_start();

$_SESSION['LoggedIn'] = 0;
unset($_SESSION['UserID']);

?>

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
<div id="headerLoggedOut">
    <a href="#" id="logoLink">MakEat</a>
</div>
<div id="contentLoggingIn">
    <h1>Logging Out</h1>
    <div id="loaderGif"><img src="images/ajax-loader.gif" alt="loading content" /></div>
</div>
</body>
</html>