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
    $pageTitle="Pantry";
    include("header.php"); ?>
    <!--script so that .pantryCategory divs "flow" around one another-->
    <script>
        $(document).ready(function(){
            var msnry = new Masonry( container, {
                // options
                columnWidth: 225,
                itemSelector: '.pantryCategory'
            });
        });
    </script>
    <div id="content">
        <div id="pantry">
            <h1>Pantry</h1>
            <div id="addItems">
                <a href="#" id="addItemsLink"><img src="images/add.png" alt="add" />
                    Add Items</a>
            </div>
            <div class="clear"></div>
            <div id="container">
                <?php
                    include_once $_SERVER['DOCUMENT_ROOT']."/SE Project - MakEats/php/class.users.inc.php";
                    $users = new users($db);
                    $users->getPantry();
                ?>
                <script>
                    $("li span").click(function(e) {
                        var itemName = e.target.getAttribute('class');
                        var categoryID = e.target.parentNode.parentNode.parentNode.getAttribute('id');
                        var query = {"itemName": itemName,
                                    "categoryID": categoryID};
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
                    });
                </script>
            </div>
        </div>
    </div>
    <script>
        $('#addItemsLink').click(function(){
            $("#overlay").fadeIn("fast");
            $("#addToPantry").fadeIn("fast");
        });
    </script>
    <?php include("footer.php");
else:
    include("index_loggedOut.php");
endif;
?>