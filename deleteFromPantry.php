<?php
include_once "php/pdo.php";
include_once "php/class.users.inc.php";
$users = new users($db);
$users->deleteFromPantry($_POST['categoryID'], $_POST['itemName']);
exit();