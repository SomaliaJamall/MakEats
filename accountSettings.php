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

<div id="addToPantry">
    <div class="return"> X </div>
    <form method="post" action=<?php echo "'".$_SERVER['REQUEST_URI']."'"; ?> id="addToPantryForm">
        <label for="categoryDrop">Category Name</label>
        <select id="categoryDrop" name="categoryDrop" required aria-required="true" >
            <?php
                include_once $_SERVER['DOCUMENT_ROOT']."/SE Project - MakEats/php/class.users.inc.php";
                $users = new users($db);
                $users->getPantryCategories();
            ?>
            <option value='New Category' id="newCatSelect">New Category</option>
        </select><br/>
        <div id="categoryNameContainer" style="display: none;">
            <label for="category">Category Name</label>
            <input type="text" id="category" name="category" value="Enter New Category Name">
        </div>
        <label for="itemName">Items</label>
        <div id="itemNameContainer">
            <input type="text" id="itemName" name="itemName[]" required aria-required="true">
        </div>
        <button class="addItemButton">+</button>
        <div class="button">
            <button type="submit" id="submit">Add Items</button>
        </div>
    </form>
    <div id="passwordErrorText"></div>
    <script>
        if($("#categoryDrop").val() == 'New Category') {
            $("#categoryNameContainer").css("display", "inline")
        }
        $( "#categoryDrop" ).change(function() {
            if($(this).val() == 'New Category') {
                $("#categoryNameContainer").css("display", "inline")
            }
            else{
                $("#categoryNameContainer").css("display", "none")
            }
        });
        dynamicFields(10, "text", "#itemNameContainer", ".addItemButton", 1, "itemName");
        function dynamicFields(max_fields, type, wrapper_id, add_button_id, initialCount, inputName){
            var wrapper         = $(wrapper_id); //Fields wrapper
            var add_button      = $(add_button_id); //Add button ID

            var x = initialCount; //initial text box count
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).append('<div><input type="'+type+'" name="'+inputName+'[]"/><a href="#" class="remove_field">X</a></div>'); //add input box
                }
            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })
        }
    </script>
    <?php
    if(!empty($_POST['category'])):
        include_once $_SERVER['DOCUMENT_ROOT']."/SE Project - MakEats/php/class.users.inc.php";
        $users = new users($db);
        $users->addToPantry();
    endif;
    ?>
</div>

<div id="success">
    <div class="return"> X </div>
    <div></div>
    <h2>Recipe: <b><?php echo $recipeTitle; ?></b><br/>Added to RecipeBook!</h2>
</div>

<div id="failure">
    <div class="return"> X </div>
    <div></div>
    <h2>Recipe: <b><?php echo $recipeTitle; ?></b><br/>Already In RecipeBook</h2>
</div>

<div id="follow">
    <div class="return"> X </div>
    <div></div>
    <h2>Followed: <b><?php echo $username; ?></b></h2>
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