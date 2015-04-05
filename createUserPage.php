<?php


$serverRoot= $_SERVER['DOCUMENT_ROOT'];
$filename = $serverRoot.'/user/'.$u."/index.php";
$dirname = dirname($filename);
if (!is_dir($dirname))
{
    mkdir($dirname, 0755, true);
}

$myfile = fopen($filename, "w");

$userPageHTML = <<< "HTML"
<?php include("../../header.php");
\$username = basename(dirname(\$_SERVER['PHP_SELF']));?>
<div id="content">
    <div id="userPage" class="userID">
        <div id="edit">
            <h3><a href="#">Edit this page</a></h3>
        </div>
        <h1><?php echo \$username) ?>: <span id="userTitle">Let's eat</span></h1>
        <div id="userIcon" style="background-image: url('exampleIMGs/tumblr_nglbdlk6UT1qeqhrro1_500.jpg')"></div>
        <div id="userBio">
            <h3>Bio</h3>
            <?php
            include_once \$_SERVER['DOCUMENT_ROOT']."/php/class.users.inc.php";
            \$users = new users(\$db);
            echo \$users->getBio(\$username);
            ?>
        </div>
        <div class="clear"></div>
        <div id="userRecipes">
            <a href="recipe.php">
                    <span class="recipeLink" style="background-image: url('exampleIMGs/img2.jpg');">
                        <span class="recipeTitle"><span>Black Bean Burger </span></span>
                    </span>
            </a>
                <span class="recipeLink" style="background-image: url('exampleIMGs/img4.jpg');">
                    <span class="recipeTitle"><span>Veggie Packed Pasta</span></span>
                </span>
                <span class="recipeLink" style="background-image: url('exampleIMGs/img3.jpg');">
                    <span class="recipeTitle"><span>Pesto and Mushroom Pasta</span></span>
                </span>
                <span class="recipeLink" style="background-image: url('exampleIMGs/img4.jpg');">
                    <span class="recipeTitle"><span>Classic Pasta</span></span>
                </span>
                <span class="recipeLink" style="background-image: url('exampleIMGs/img2.jpg');">
                    <span class="recipeTitle"><span>Black Bean Burger</span></span>
                </span>
                <span class="recipeLink" style="background-image: url('exampleIMGs/img1.jpg');">
                    <span class="recipeTitle"><span>Tasty Watermelon Breakfast</span></span>
                </span>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php include("../../footer.php"); ?>
HTML;
fwrite($myfile, $userPageHTML);

?>