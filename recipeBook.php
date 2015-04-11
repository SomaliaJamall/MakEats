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
$pageTitle="Recipe Book";
include("header.php"); ?>
<!--
Each .savedRecipe div must be generated based on the information stored in the savedRecipes database for the user
-->

    <div id="content">
        <div id="recipeBook">
            <h1>Recipe Book</h1>
            <?php
            include_once "php/pdo.php";
            include_once "php/class.users.inc.php";
            $users = new users($db);
            $users->getUserRecipeBook($_SESSION['UserID'])?>
            <!--<a href="recipe.php">
                <span class="savedRecipe" style="background-image: url('exampleIMGs/img2.jpg');">
                    <span class="recipeTitle"><span>Black Bean Burger </span></span>
                </span>
            </a>
            <span class="savedRecipe" style="background-image: url('exampleIMGs/img4.jpg');">
                <span class="recipeTitle"><span>Veggie Packed Pasta</span></span>
            </span>
            <span class="savedRecipe" style="background-image: url('exampleIMGs/img3.jpg');">
                <span class="recipeTitle"><span>Pesto and Mushroom Pasta</span></span>
            </span>
            <span class="savedRecipe" style="background-image: url('exampleIMGs/img4.jpg');">
                <span class="recipeTitle"><span>Classic Pasta</span></span>
            </span>
            <span class="savedRecipe" style="background-image: url('exampleIMGs/img2.jpg');">
                <span class="recipeTitle"><span>Black Bean Burger</span></span>
            </span>
            <span class="savedRecipe" style="background-image: url('exampleIMGs/img1.jpg');">
                <span class="recipeTitle"><span>Tasty Watermelon Breakfast</span></span>
            </span>-->
            <div class="clear"></div>
        </div>
    </div>
<?php include("footer.php");
else:
    include("index_loggedOut.php");
endif;
?>