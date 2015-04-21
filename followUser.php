<?php
include_once "php/pdo.php";
include_once "php/class.users.inc.php";
$users = new users($db);
$follow = getUserID($_POST['fUsername']);
if($users->followUser($follow) == "success"){
    echo 1;
}
else{
    echo 0;
}
exit();