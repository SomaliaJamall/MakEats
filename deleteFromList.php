<?php
include_once "php/pdo.php";
include_once "php/class.users.inc.php";
$users = new users($db);
$users->deleteFromList($_POST['itemNum']);
exit();