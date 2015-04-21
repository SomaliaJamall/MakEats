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
    $pageTitle="List";
    include("header.php"); ?>
    <!--script so that .listColumn divs "flow" around one another-->

    <div id="content">
        <div id="list">
            <h1>Shopping Lists</h1>
            <div id="addItems">
                <a href="#" id="addItemsLink"><img src="images/add.png" alt="add" />
                    Add Items to List</a>
            </div>
            <div class="clear"></div>
            <div class="listColumn">
                <ul>
                    <?php
                    include_once $_SERVER['DOCUMENT_ROOT']."/SE Project - MakEats/php/class.users.inc.php";
                    $users = new users($db);
                    $users->getList();
                    ?>
                </ul>
            </div>
            <script>
                $('#addItemsLink').click(function(){
                    $("#overlay").fadeIn("fast");
                    $("#addToList").fadeIn("fast");
                });

                $("li span").click(function(e) {
                    var itemNum = e.target.getAttribute('id');
                    $(this).parent().css("display", "none");
                    var query = {"itemNum": itemNum};
                    var url = 'deleteFromList.php';
                    $.post(url, query, function (response) {
                        /*if (response==1){
                         $("#overlay").fadeIn("fast");
                         $("#success").fadeIn("fast");
                         }
                         else{
                         $("#overlay").fadeIn("fast");
                         $("#failure").fadeIn("fast");
                         }*/
                    });
                });
            </script>
            <!--<div class="listColumn">
                <ul>
                    <li>Item 1 <span class="deleteItem">X</span></li>
                    <li>Item 2 <span class="deleteItem">X</span></li>
                    <li>Item 3 <span class="deleteItem">X</span></li>
                    <li>Item 4 <span class="deleteItem">X</span></li>
                    <li>Item 5 <span class="deleteItem">X</span></li>
                    <li>Item 6 <span class="deleteItem">X</span></li>
                    <li>Item 7 <span class="deleteItem">X</span></li>
                    <li>Item 8 <span class="deleteItem">X</span></li>
                    <li>Item 9 <span class="deleteItem">X</span></li>
                    <li>Item 10 <span class="deleteItem">X</span></li>
                    <li>Item 11 <span class="deleteItem">X</span></li>
                    <li>Item 12 <span class="deleteItem">X</span></li>
                    <li>Item 13 <span class="deleteItem">X</span></li>
                    <li>Item 14 <span class="deleteItem">X</span></li>
                    <li>Item 15 <span class="deleteItem">X</span></li>
                    <li>Item 16 <span class="deleteItem">X</span></li>
                    <li>Item 17 <span class="deleteItem">X</span></li>
                    <li>Item 18 <span class="deleteItem">X</span></li>
                    <li>Item 19 <span class="deleteItem">X</span></li>
                    <li>Item 20 <span class="deleteItem">X</span></li>
                </ul>
            </div>
            <div class="listColumn">
                <ul>
                    <li>Item 1 <span class="deleteItem">X</span></li>
                    <li>Item 2 <span class="deleteItem">X</span></li>
                    <li>Item 3 <span class="deleteItem">X</span></li>
                    <li>Item 4 <span class="deleteItem">X</span></li>
                    <li>Item 5 <span class="deleteItem">X</span></li>
                    <li>Item 6 <span class="deleteItem">X</span></li>
                    <li>Item 7 <span class="deleteItem">X</span></li>
                    <li>Item 8 <span class="deleteItem">X</span></li>

                </ul>
            </div>-->
            <div class="clear"></div>
            <div id="addItemsToPantry"><img src="images/check.png" /> Add List Items to Pantry</div>
        </div>
    </div>

<?php include("footer.php");
else:
include("index_loggedOut.php");
endif;
?>