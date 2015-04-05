<?php
include_once $_SERVER['DOCUMENT_ROOT']."/php/pdo.php";

$HTMLRoot = "/SE%20Project%20-%20MakEats/";
function getLinkFromRoot($fileName){
    $HTMLRoot = "/SE%20Project%20-%20MakEats/";
    return "'".$HTMLRoot.$fileName."'";
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>MakEats - <?php echo $pageTitle;?></title>
    <link rel="stylesheet" type="text/css" href=<?php echo getLinkFromRoot("styles.css");?>>
    <script src=<?php echo getLinkFromRoot("scripts/masonry/masonry.pkgd.min.js");?>></script>
    <script src=<?php echo getLinkFromRoot("scripts/jquery-1.11.2.min.js");?>></script>
    <script src=<?php echo getLinkFromRoot("scripts/jquery.validate.js");?>></script>
    <script src=<?php echo getLinkFromRoot("scripts/script.js");?>></script>
</head>
<body>
<div id="heading">
    <div id="logo"><a id="logoLink" href=<?php echo getLinkFromRoot("index.php");?>>MakEat</a></div>
    <div id="accountLinks">
        <a href=<?php echo getLinkFromRoot("user/".$_SESSION['Username']."/");?>><?php echo $_SESSION['Username'];?></a>
        <a href="#" id="accountSettingsLink"><img alt="Account Settings" src=<?php echo getLinkFromRoot("images/gear.png");?> /></a>
    </div>
    <div class="clear"></div>
    <div id="sidebar">
        <ul>
            <li><a href=<?php echo getLinkFromRoot("index.php");?>>Home</a></li>
            <li><a href=<?php echo getLinkFromRoot("recipeBook.php");?>>Recipe Book</a></li>
            <li><a href=<?php echo getLinkFromRoot("pantry.php");?>>Pantry</a></li>
            <li><a href=<?php echo getLinkFromRoot("list.php");?>>Shopping Lists</a></li>
        </ul>
    </div>
    <div id="newRecipeLink">
        <a href="#"><img alt="add" src=<?php echo getLinkFromRoot("images/add.png");?> />
            New Recipe</a>
    </div>
    <div class="clear"></div>
</div>

<?php
include("accountSettings.php");
?>