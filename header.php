<?php
include_once "php/pdo.php";
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>MakEats - <?php echo $pageTitle;?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="scripts/masonry/masonry.pkgd.min.js"></script>
    <script src="scripts/jquery-1.11.2.min.js"></script>
    <script src="scripts/jquery.validate.js"></script>
    <script src="scripts/script.js"></script>
</head>
<body>
<div id="heading">
    <div id="logo"><a href="index.php" id="logoLink">MakEat</a></div>
    <div id="accountLinks">
        <a href="userfoodlover.php"><?php echo $_SESSION['Username'];?></a>
        <a href="#" id="accountSettingsLink"><img src="images/gear.png" alt="Account Settings" /></a>
    </div>
    <div class="clear"></div>
    <div id="sidebar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="recipeBook.php">Recipe Book</a></li>
            <li><a href="pantry.php">Pantry</a></li>
            <li><a href="list.php">Shopping Lists</a></li>
        </ul>
    </div>
    <div id="newRecipeLink">
        <a href="#"><img src="images/add.png" alt="add" />
            New Recipe</a>
    </div>
    <div class="clear"></div>
</div>

<?php
include("accountSettings.php");
?>