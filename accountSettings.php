<div id="accountSettings">
    <div class="return">X</div>
    <h1>Account Settings</h1>
    <ul>
        <li><a href=<?php echo getLinkFromRoot("logout.php");?>>Logout</a></li>
        <li><a href="#" id="changeEmailLink">Change Email</a></li>
        <li><a href="#" id="changePasswordLink">Change Password</a></li>
        <li><a href="#" id="changeUsernameLink">Change Username</a></li>
    </ul>
</div>

<div id="changeEmail" style="display:none;">
    <div class="back"> << </div>
    <div class="return"> X </div>
    <form method="post" action=<?php echo "'".$_SERVER['REQUEST_URI']."'"; ?> id="changeEmailForm">
        <div id="newEmailContainer">
            <label for="newEmail">Enter New Email</label>
            <input type="email" id="newEmail" name="newEmail" required aria-required="true">
        </div>
        <div class="button">
            <button type="submit" id="submit">Change Email</button>
        </div>
    </form>
    <script>
        $("#changeEmailForm").validate();
    </script>
    <?php
    if(!empty($_POST['newEmail'])):
        include_once $_SERVER['DOCUMENT_ROOT']."/php/class.users.inc.php";
        $users = new users($db);
        echo $users->changeEmail();
    endif;
    ?>
</div>

<div id="changeUsername" style="display:none;">
    <div class="back"> << </div>
    <div class="return"> X </div>
    <form method="post" action=<?php echo "'".$_SERVER['REQUEST_URI']."'"; ?> id="changeUsernameForm">
        <div id="newUsernameContainer">
            <label for="newUsername">Enter New Username</label>
            <input type="text" id="newUsername" name="newUsername" required aria-required="true">
        </div>
        <div class="button">
            <button type="submit" id="submit">Change Username</button>
        </div>
    </form>
    <script>
        $("#changeUsernameForm").validate();
    </script>
    <?php
    if(!empty($_POST['newUsername'])):
        include_once $_SERVER['DOCUMENT_ROOT']."/php/class.users.inc.php";
        $users = new users($db);
        echo $users->changeUsername();
    endif;
    ?>
</div>

<div id="changePassword">
    <div class="back"> << </div>
    <div class="return"> X </div>
    <form method="post" action=<?php echo "'".$_SERVER['REQUEST_URI']."'"; ?> id="changePasswordForm">
        <div id="oldPasswordContainer">
            <label for="oldPassword">Enter Old Password</label>
            <input type="password" id="oldPassword" name="oldPassword" required aria-required="true">
        </div>
        <div id="newPasswordContainer">
            <label for="newPassword">Enter New Password</label>
            <input type="password" id="newPassword" name="newPassword" required aria-required="true">
        </div>
        <div class="button">
            <button type="submit" id="submit">Change Password</button>
        </div>
    </form>
    <div id="passwordErrorText"></div>
    <?php
    if(!empty($_POST['newPassword'])):
        include_once $_SERVER['DOCUMENT_ROOT']."/php/class.users.inc.php";
        $users = new users($db);
        $users->changePassword();
    endif;
    ?>
</div>

<div id="changesMade">
    <div class="back"> << </div>
    <div class="return"> X </div>
    Changes successfully made!
</div>

<div id="overlay">
    .
</div>

<?php
if ($_SESSION['passwordCorrect'] == 0):?>
    <script>
        $("#changePassword").css("display", "block");
        $("#overlay").css("display", "block");
        $("#passwordErrorText").html("Your old password was incorrect, try again");
    </script>
<?php
endif;
$_SESSION['passwordCorrect'] = 1;
?>

<?php
if ($_SESSION['changesMade'] == 1):?>
    <script>
        $("#changesMade").css("display", "block");
        $("#overlay").css("display", "block");
    </script>
<?php
endif;
$_SESSION['changesMade'] = 0;
?>