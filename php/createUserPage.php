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
include_once "../../php/pdo.php";
include_once "../../php/class.users.inc.php";
\$users = new users(\$db);
\$username = basename(dirname(\$_SERVER['PHP_SELF']));
\$pageTitle = ucwords(\$username)." - ". \$users->getTitle(\$username);
include("../../header.php");?>
<div id="content">
    <?php
    if(!empty(\$_POST['recipe_title'])):
        include_once "../../php/class.users.inc.php";
        \$users = new users(\$db);
        \$users->submitRecipe();
    endif;

    if(!empty(\$_POST['updateIconButton'])):
        include_once "../../php/class.users.inc.php";
        \$users = new users(\$db);
        \$users->updateIcon();
    endif;

    if(!empty(\$_POST['user_bio'])):
        include_once "../../php/class.users.inc.php";
        \$users = new users(\$db);
        \$users->updateBio();
    endif;

    if(!empty(\$_POST['user_title'])):
        include_once "../../php/class.users.inc.php";
        \$users = new users(\$db);
        \$users->updateTitle();
    endif;
    ?>
    <div id="userPage" class="user<?php echo getUserID(\$username); ?>">
        <div id="edit">
            <?php
                    echo \$users->checkIfFollow(getUserID(\$username));
                    if (\$username == \$_SESSION['Username']):
                        echo "<h3><a href='../../editUserpage.php'>Edit this page</a></h3>";
                    else:
                        if (\$users->checkIfFollow(getUserID(\$username)) == "no"):
                        ?>
                            <form id="followUserForm" method='post'>
                                <button class="followUserB" value="<?php echo \$username; ?>" onclick="follow(); return false;">
                                    Follow User
                                </button>
                            </form>
                            <script type="text/javascript">
                                function follow(){
                                    var username=$(".followUserB").val();
                                    var query = {"fUsername": username};
                                    var url = '../../followUser.php';
                                    $.post(url, query, function (response) {
                                        if (response==1){
                                            $("#overlay").fadeIn("fast");
                                            $("#follow").fadeIn("fast");
                                        }
                                        else{
                                            $("#overlay").fadeIn("fast");
                                            $("#failure").fadeIn("fast");
                                        }
                                    });
                                }
                            </script>
                <?php   endif;
                    endif; ?>

        </div>
        <h1><?php echo \$username ?>: <span id="userTitle">
                <?php
                \$users = new users(\$db);
                echo \$users->getTitle(\$username);
                ?>
            </span></h1>
        <div id="userIcon" style="background-image: url('../../<?php echo \$users->getIcon(\$username);?>')"></div>
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