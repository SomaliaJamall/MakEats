<?php

$feedHTML = <<< "HTML"
<div id="postID1">
    <!-- the user link will be dynamically generated-->
    <div class="userID userName"><h1><a href="userfoodlover.php">$username</a></h1></div>
    <div class="timeStamp">$timestamp</div>
    <div class="clear"></div>
    <div class="threePics foodPics">
        <ul>
            <li><a href="#"><img src="exampleIMGs/img2.jpg"></a></li>
            <li><a href="#"><img src="exampleIMGs/img4.jpg"></a></li>
            <li><a href="#"><img src="exampleIMGs/img3.jpg"></a></li>
        </ul>
    </div>
    <div class="recipeContent">
        <div class="recipeDescription">
            <h2><a href="recipe.php">Mushroom Swiss Burger</a></h2>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc lorem magna, aliquet eu ipsum quis,
            laoreet sagittis dolor. Sed vel metus tristique, euismod eros et, accumsan justo. Vivamus nec ipsum
            eu leo posuere eleifend. Donec enim nunc, bibendum eget euismod eget, euismod a neque. Praesent
            sodales eros quis purus maximus dignissim. Suspendisse accumsan viverra ante, eget pulvinar orci.
            Cras gravida ornare neque porta hendrerit.
        </div>
        <div class="recipeItemList">
            <h2>Ingredients</h2>
            <ul>
                <li>2 lb Beef</li>
                <li>4 cups Mushrooms</li>
                <li>1 pinch Salt</li>
                <li>1 tbsp spoon Pepper</li>
            </ul>
        </div>
        <div class="recipeProcess">
            <h2>Process</h2>
            <ol>
                <li>Heat oil</li>
                <li>Brown Beef</li>
                <li>Add Mushrooms</li>
            </ol>
        </div>
        <div class="viewMore"><a href="recipe.php">...</a></div>
        <div class="shareOrSave"><a href="#"><img src="images/addRed.png" alt="add"/> Add To Recipe Book</a></div>
    </div>
</div>
HTML;
