<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>MakEats - Log In</title>
    <link rel="stylesheet" type="text/css" href="loggedOutInStyles.css">
    <script src="scripts/jquery-1.11.2.min.js"></script>
    <script src="scripts/jquery.validate.js"></script>
    <script>
        /**
         * Created by somalia on 3/19/2015.
         */
        $(document).ready(function(){
            var $_POST = <?php echo json_encode($_POST); ?>;

            if(typeof $_POST["user_username"] == 'undefined'){
                $("#screen2").css("display","none");
            }
            else{
                $("#screen1").css("display","none");
            }

            $("#makAccountImage").click(function() {
                $("#screen1").fadeOut("fast", function(){
                    $("#screen2").fadeIn("fast");
                });
            });
            $("#return").click(function() {
                $("#screen2").fadeOut("fast", function(){
                    $("#screen1").fadeIn("fast");
                });
            });
            $("#headerLoggedOut").click(function(){
                $("#screen2").fadeOut("fast", function(){
                    $("#screen1").fadeIn("fast");
                });
            });

            jQuery.validator.addMethod("noSpace", function(value, element)
            { return value.indexOf(" ") < 0 && value != ""; }, "No spaces are allowed in the username and password fields");

            $("#registerform").validate({
                rules: {
                    user_reenterpassword: {
                        equalTo: "#user_password"
                    },
                    user_username:{
                        noSpace: true
                    },
                    user_password:{
                        noSpace: true
                    }
                },
                errorLabelContainer: $("#screen2 div.errorContainer"),
                messages: {
                    user_username: {
                        required: 'Enter your username'
                    },
                    user_email: {
                        required: 'Enter your email'
                    },
                    user_password: {
                        required: 'Enter your password'
                    },
                    user_reenterpassword: {
                        required: 'Re-enter your password',
                        equalTo: "Make sure passwords are the same"
                    }
                }
            });
        });

    </script>
</head>
<body id="bodyLoggedOut">
<?php
include_once "php/pdo.php";
$pageTitle = "Register";
?>
<div id="headerLoggedOut">
    <a href="#" id="logoLink">MakEat</a>
</div>
<div id="contentLoggedOut">
    <div id="screen1">
        <div id="logInFromLoggedOut">
            <form method="post" action="login.php" id="loginform">
                <div id="usernameContainer">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="login_username">
                </div>

                <div id="passwordContainer">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="login_password">
                </div>
                <div class="button">
                    <button type="submit">Log In</button>
                </div>
                <div class="errorContainer">
                    <?php
                    if(!empty($_SESSION['LoggedIn'])):
                        if($_SESSION['LoggedIn'] == -1):
                            echo "<p class='error'> Your username or password was incorrect </p>";
                        endif;
                    endif;
                    ?>
                </div>
            </form>
        </div>
        <div id="makAccountLink">
                <img id="makAccountImage" src="images/makAccount.png" alt="Make Account?" />
        </div>
        <div class="clear"></div>
    </div>
    <div id="screen2">
        <div id="return">X</div>
        <form method="post" action="index.php" id="registerform">
            <div id="column1">
                <div id="newUsernameContainer">
                    <label for="user_username">Username</label>
                    <input type="text" id="user_username" name="user_username" maxlength="20" required aria-required="true">
                </div>

                <div id="newEmailContainer">
                    <label for="user_email">Email</label>
                    <input type="email" id="user_email" name="user_email" required aria-required="true">
                </div>

            </div>
            <div id="column2">
                <div id="newPasswordContainer">
                    <label for="user_password">Password</label>
                    <input type="password" id="user_password" name="user_password" required aria-required="true">
                </div>

                <div id="newReenterPasswordContainer">
                    <label for="user_reenterpassword">Re-Enter Password</label>
                    <input type="password" id="user_reenterpassword" name="user_reenterpassword" required aria-required="true">
                </div>

                <div class="button">
                    <button type="submit" id="submit">Make Account</button>
                </div>
            </div>
            <div class="clear"></div>
            <div class="errorContainer">
                <?php
                if(!empty($_POST['user_username'])):
                    include_once "php/class.users.inc.php";
                    $users = new users($db);
                    echo $users->createAccount();
                endif;
                ?>
            </div>
        </form>
    </div>

</div>
</body>
</html>