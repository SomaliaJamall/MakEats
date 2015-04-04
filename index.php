<?php
include_once "php/pdo.php";
if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['UserID'])):
    include("index_loggedin.php");
else:
    include("index_loggedOut.php");
endif;
?>