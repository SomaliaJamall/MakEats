<?php
include_once "php/pdo.php";
include_once "php/class.users.inc.php";
$users = new users($db);
if ($users->addToRecipeBook($_POST['recipeID'], $_SESSION['UserID']) == "success"){
    echo 1;
}
else{
    echo  0;
}
exit();