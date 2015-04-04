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
                <a href="#"><img src="images/add.png" alt="add" />
                    Add Items</a>
            </div>
            <div class="clear"></div>
            <div id="container">
                <div class="pantryCategory">
                    <h2>Category Name</h2>
                    <ul>
                        <li>Item 1 <span class="deleteItem">X</span></li>
                        <li>Item 2 <span class="deleteItem">X</span></li>
                        <li>Item 3 <span class="deleteItem">X</span></li>
                        <li>Item 4 <span class="deleteItem">X</span></li>
                        <li>Item 5 <span class="deleteItem">X</span></li>
                    </ul>
                </div>
                <div class="pantryCategory">
                    <h2>Category Name</h2>
                    <ul>
                        <li>Item 1 <span class="deleteItem">X</span></li>
                        <li>Item 2 <span class="deleteItem">X</span></li>
                    </ul>
                </div>
                <div class="pantryCategory">
                    <h2>Category Name</h2>
                    <ul>
                        <li>Item 1 <span class="deleteItem">X</span></li>
                        <li>Item 2 <span class="deleteItem">X</span></li>
                        <li>Item 3 <span class="deleteItem">X</span></li>
                        <li>Item 4 <span class="deleteItem">X</span></li>
                        <li>Item 5 <span class="deleteItem">X</span></li>
                    </ul>
                </div>
                <div class="pantryCategory">
                    <h2>Category Name</h2>
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

                    </ul>
                </div>
                <div class="pantryCategory">
                    <h2>Category Name</h2>
                    <ul>
                        <li>Item 1 <span class="deleteItem">X</span></li>
                        <li>Item 2 <span class="deleteItem">X</span></li>
                        <li>Item 3 <span class="deleteItem">X</span></li>
                        <li>Item 4 <span class="deleteItem">X</span></li>
                        <li>Item 5 <span class="deleteItem">X</span></li>
                        <li>Item 6 <span class="deleteItem">X</span></li>
                    </ul>
                </div>
                <div class="pantryCategory">
                    <h2>Category Name</h2>
                    <ul>
                        <li>Item 1 <span class="deleteItem">X</span></li>
                        <li>Item 2 <span class="deleteItem">X</span></li>
                        <li>Item 3 <span class="deleteItem">X</span></li>
                        <li>Item 4 <span class="deleteItem">X</span></li>
                        <li>Item 5 <span class="deleteItem">X</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php include("footer.php");
else:
    include("index_loggedOut.php");
endif;
?>