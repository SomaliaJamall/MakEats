<?php
/**
 * Created by PhpStorm.
 * User: somalia
 * Date: 3/15/2015
 * Time: 5:17 PM
 */
$pageTitle="Feed";
include("header.php"); ?>

<!--
the # feed div must be populated based on the information stored in the feed database
-->

<div id="content">
    <div id="feed">
        <?php
        include_once "php/pdo.php";
        include_once "php/class.users.inc.php";
        $users = new users($db);
        $users->getFeed()
        ?>
    </div>
</div>
<?php include("footer.php"); ?>