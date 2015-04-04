<?php
/**
 * Created by PhpStorm.
 * User: somalia
 * Date: 3/15/2015
 * Time: 5:17 PM
 */

include("header.php"); ?>
    <!--
    the # feed div must be populated based on the information stored in the feed database
    -->

    <div id="content">
        <div id="userPage" class="userID">
            <div id="edit">
                <!-- this will only appear on a user's OWN page-->
                <h3><a href="#">Edit this page</a></h3>
            </div>
            <h1>Food_Lover: <span id="userTitle">Let's eat</span></h1>
            <div id="userIcon" style="background-image: url('exampleIMGs/tumblr_nglbdlk6UT1qeqhrro1_500.jpg')"></div>
            <div id="userBio">
                <h3>Bio</h3>
                sit amet, consectetur adipiscing elit. Mauris sagittis, sem vel molestie pharetra, libero sem vehicula ligula, vel finibus libero erat vitae ex.
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
<?php include("footer.php"); ?>