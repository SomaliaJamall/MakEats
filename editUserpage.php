<?php
include_once "php/pdo.php";
if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['UserID'])):
    #this if checks if the user is logged in
    /**
     * Created by PhpStorm.
     * User: somalia
     * Date: 3/15/2015
     * Time: 5:17 PM
     */
    $pageTitle="Edit User Profile";
    include("header.php");
    include_once "php/class.users.inc.php";
    $users = new users($db);
    $username = $_SESSION['Username']?>
    <div id="content">
        <div id="editProfile">
            <h1>Edit Your Profile</h1>
            <form method="post" enctype="multipart/form-data" id="iconForm" action=<?php echo "user/".$_SESSION['Username']."/index.php"; ?>>
                <label for="user_icon">Icon:</label>
                <div class="current">
                    <h2>Current Icon:</h2>
                    <div id="userIcon" style="background-image: url('<?php echo $users->getIcon($username);?>')"></div>
                    <div class="icon">
                        <div><input type="file" name="user_icon" required></div>
                    </div>
                </div>
                <div>
                    <button class="submitRecipeButton" name="updateIconButton" value="True">Update Icon</button>
                </div>
            </form>
            <form method="post" id="titleForm" action=<?php echo "user/".$_SESSION['Username']."/index.php"; ?>>
                <label for="user_title">Title</label>
                <div class="current">
                    <h2>Current Title:</h2>
                    <?php
                    echo $users->getTitle($username);
                    ?>
                    <div class="title">
                        <div><input type="text" name="user_title" required></div>
                    </div>
                </div>
                <div>
                    <button class="submitRecipeButton">Update Title</button>
                </div>
            </form>
            <form method="post" id="bioForm" action=<?php echo "user/".$_SESSION['Username']."/index.php"; ?>>
                <label for="user_bio">Bio</label>
                <div class="current">
                    <h2>Current Bio:</h2>
                    <?php
                    echo $users->getBio($username);
                    ?>

                    <div class="bio">
                        <div><textarea name="user_bio" maxlength="500" required></textarea></div>
                    </div>
                </div>
                <div>
                    <button class="submitRecipeButton">Update Bio</button>
                </div>
            </form>
            <script>
                $("#iconForm").validate();
                $("#titleForm").validate();
                $("#bioForm").validate();
            </script>
        </div>
    </div>
    <?php include("footer.php");
else:
    include("index_loggedOut.php");
endif;
?>