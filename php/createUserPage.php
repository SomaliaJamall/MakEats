<?php

$filename = HTTP_SERVER.'/user/'.$u."/index.php";
$dirname = dirname($filename);
if (!is_dir($dirname))
{
    mkdir($dirname, 0755, true);
}

$myfile = fopen($filename, "w");

$userPageHTML = <<< "HTML"
<?php
\$username = basename(dirname(\$_SERVER['PHP_SELF']));
\$pageTitle = ucwords(\$username);
include("../../header.php");
include_once "../../php/class.users.inc.php";?>
<div id="content">
    <?php
    if(!empty(\$_POST['recipe_title'])):
        include_once "../../php/class.users.inc.php";
        \$users = new users(\$db);
        echo \$users->submitRecipe();
    endif;
    ?>
    <div id="userPage" class="user<?php echo getUserID(\$username); ?>">
        <div id="edit">
            <h3><a href="#">Edit this page/Follow This User</a></h3>
        </div>
        <h1><?php echo \$username ?>: <span id="userTitle">
                <?php
                \$users = new users(\$db);
                echo \$users->getTitle(\$username);
                ?>
            </span></h1>
        <div id="userIcon" style="background-image: url('<?php echo \$users->getIcon(\$username);?>')"></div>
        <div id="userBio">
            <h3>Bio</h3>
            <?php
            echo \$users->getBio(\$username);
            ?>
        </div>
        <div class="clear"></div>
        <div id="userRecipes">
            <?php \$users->getUserRecipeThumbnailList(\$username) ?>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php include("../../footer.php"); ?>
HTML;
fwrite($myfile, $userPageHTML);

?>