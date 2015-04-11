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
    $pageTitle="New Recipe";
    include("header.php"); ?>
    <div id="content">
        <div id="newRecipe">
            <h1>Add New Recipe</h1>
            <form method="post" enctype="multipart/form-data" action=<?php echo "user/".$_SESSION['Username']."/index.php"; ?> id="addRecipeForm">
                <label for="recipe_title">Title</label>
                <div class="title">
                    <div><input type="text" name="recipe_title" required></div>
                </div>

                <label for="description">Description</label>
                <div class="description">
                    <div><textarea name="description" required></textarea></div>
                </div>

                <label for="photos[]">Pictures</label>
                <div class="photos">
                    <div class="pictures_fields_wrap">
                        <div><input type="file" name="photos[]" required></div>
                    </div>
                    <button class="addPicturesButton">+</button>
                </div>

                <label for="ingredients[]">Ingredients</label>
                <div class="ingredients_fields_wrap">
                    <div><input type="text" name="ingredients[]" required></div>
                    <div><input type="text" name="ingredients[]"></div>
                    <div><input type="text" name="ingredients[]"></div>
                    <div><input type="text" name="ingredients[]"></div>
                    <div><input type="text" name="ingredients[]"></div>
                </div>
                <button class="addIngredientButton">+</button>

                <label for="steps[]">Steps</label>
                <div>
                    <ol class="steps_fields_wrap">
                    <li><input type="text" name="steps[]" required></li>
                    <li><input type="text" name="steps[]"></<li>
                    <li><input type="text" name="steps[]"></li>
                    </ol>
                </div>
                <button class="addStepButton">+</button>
                <div>
                    <button class="submitRecipeButton">Add Recipe</button>
                </div>
            </form>
            <script>
                $("#addRecipeForm").validate();
            </script>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            dynamicFields(20, "text", ".ingredients_fields_wrap", ".addIngredientButton", 5, "ingredients");
            dynamicFields(3, "file", ".pictures_fields_wrap", ".addPicturesButton", 1, "photos");
            dynamicFieldsOL(20, "text", ".steps_fields_wrap", ".addStepButton", 3, "steps");
        });

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

        function dynamicFieldsOL(max_fields, type, wrapper_id, add_button_id, initialCount, inputName){
            var wrapper         = $(wrapper_id); //Fields wrapper
            var add_button      = $(add_button_id); //Add button ID

            var x = initialCount; //initial text box count
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).append('<li><input type="'+type+'" name="'+inputName+'[]"/><a href="#" class="remove_field">X</a></li>'); //add input box
                }
            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('li').remove(); x--;
            })
        }
    </script>
    <?php include("footer.php");
else:
    include("index_loggedOut.php");
endif;
?>