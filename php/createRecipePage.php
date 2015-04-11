<?php

$filename = HTTP_SERVER."/user/".$_SESSION['Username']."/recipes/".$recipeId."/index.php";
$dirname = dirname($filename);
if (!is_dir($dirname))
{
    mkdir($dirname, 0755, true);
}

$myfile = fopen($filename, "w");

$userPageHTML = <<< "HTML"
<?php
/**
 * Created by PhpStorm.
 * User: somalia
 * Date: 3/15/2015
 * Time: 5:17 PM
 */
include_once "../../../../php/pdo.php";
\$recipeID = basename(dirname(\$_SERVER['PHP_SELF']));
include_once "../../../../php/class.users.inc.php";
\$users = new users(\$db);
\$recipeTitle = ucwords( \$users->getRecipeTitle(\$recipeID));
\$pageTitle = \$recipeTitle;
include("../../../../header.php");?>
    <!--
    the # feed div must be populated based on the information stored in the feed database
    -->

    <div id="content">
        <div id="recipePage">
            <div class="recipeTitle recipeID"><h1><?php echo \$recipeTitle; ?></h1></div>
            <div class="timeStamp"><?php echo date("M d y", (\$users->getRecipeTimestamp(\$recipeID))); ?></div>
            <div class="clear"></div>
            <div id=byUser"><h3><a href="../../">By
                        <?php echo  \$users->getUsername(\$users->getRecipeUserID(\$recipeID)); ?></a></h3></div>
            <?php \$users->getRecipeThumbnails(\$recipeID);?>
            <div class="recipeContent">
                <div class="recipeDescription">
                    <?php echo \$users->getRecipeDescription(\$recipeID) ;?>
                </div>
                <div class="recipeItemList">
                    <h2>Ingredients</h2>
                    <?php \$users->getRecipeIngredients(\$recipeID);?>
                </div>
                <div class="recipeProcess">
                    <h2>Process</h2>
                    <?php \$users->getRecipeSteps(\$recipeID); ?>
                </div>
                <div class="shareOrSave">
                    <form id="addToRecipeBook" method='post'>
                        <button class="addToRecipeBookButton" value="<?php echo \$recipeID; ?>" onclick="save(); return false;">
                            <img src="../../../../images/addRed.png" alt="add"/> Add To Recipe Book
                        </button>
                    </form>
                    <script type="text/javascript">
                        function save(){
                            var recipeID=$(".addToRecipeBookButton").val();
                            var query = {"recipeID": recipeID};
                            var url = '../../../../addToRecipeBook.php';
                            $.post(url, query, function (response) {
                                if (response==1){
                                    $("#overlay").fadeIn("fast");
                                    $("#success").fadeIn("fast");
                                }
                                else{
                                    $("#overlay").fadeIn("fast");
                                    $("#failure").fadeIn("fast");
                                }
                            });

                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
<?php include("../../../../footer.php"); ?>
HTML;
fwrite($myfile, $userPageHTML);

?>