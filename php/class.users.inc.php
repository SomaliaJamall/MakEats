<?php
/**
 * Created by PhpStorm.
 * User: somalia
 * Date: 3/20/2015
 * Time: 3:01 PM
 */

class users
{
    private $_db;

    public function __construct($db = NULL)
    {
        if (is_object($db)) {
            $this->_db = $db;
        } else {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
    }

    public function createAccount()
    {
        $successfulAccountCreation = 0;
        $u = strtolower(trim($_POST['user_username']));
        if (empty($u)){
            return "<h2 class='error'> Error </h2>"
            . "<p>Please enter a username</p>";
        }
        $e = strtolower(trim($_POST['user_email']));
        if (empty($e)){
            return "<h2 class='error'> Error </h2>"
            . "<p>Please enter an email below</p>";
        }
        $p = MD5(trim($_POST['user_password']));
        if (empty($p)){
            return "<h2 class='error'> Error </h2>"
            . "<p>Please enter a password</p>";
        }


        $sql = "SELECT COUNT(username) AS theCount
                FROM user
                WHERE username=:username";

        if ($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":username", $u, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row['theCount'] != 0) {
                return "<h2 class='error'> Error </h2>"
                . "<p> Sorry, that username is already in use. "
                . "Please try again. </p>";
            }
            $stmt->closeCursor();
        }

        $sql = "SELECT COUNT(email) AS theCount
                FROM user
                WHERE email=:email";

        if ($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $e, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row['theCount'] != 0) {
                return "<h2 class='error'> Error </h2>"
                . "<p> Sorry, that email is already in use. "
                . "Please try again. </p>";
            }
            $stmt->closeCursor();
        }

        $sql = "INSERT INTO user(username, email, password)
                VALUES(:userid, :email, :password)";
        if ($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":userid", $u, PDO::PARAM_STR);
            $stmt->bindParam(":email", $e, PDO::PARAM_STR);
            $stmt->bindParam(":password", $p, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            $successfulAccountCreation = 1;
        }
        else{
            return "<h2 class='error'> Error </h2>"
            . "<p>Please make sure you entered a username, email, and password</p>";
        }

        if ($successfulAccountCreation == 1){
            $sql = "SELECT userid FROM user
                    WHERE username=:user";
            try{
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':user', $u, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                if($stmt->rowCount()==1)
                {
                    $row = $stmt->fetch();
                    include("createUserPage.php");
                }
            }
            catch(PDOException $e){
                return False;
            }
        }

        return "<h2 class='success'> Success! </h2>"
        . "<p> You can now log into your account</p>";
    }

    public function accountLogin()
    {
        $u = strtolower($_POST['login_username']);
        $p = $_POST['login_password'];

        $sql = "SELECT username, userid
                FROM user
                WHERE username=:user
                AND password=MD5(:pass)
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $u, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $p, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                $_SESSION['UserID'] = $row['userid'];
                $_SESSION['Username'] = htmlentities($u, ENT_QUOTES);
                $_SESSION['LoggedIn'] = 1;
                $_SESSION['passwordCorrect'] = 1;
                $_SESSION['changesMade'] = 0;

                return TRUE;
            }
            else
            {
                $_SESSION['LoggedIn'] = -1;
                return FALSE;
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function changeEmail()
    {
        $e = strtolower($_POST['newEmail']);
        $uid = $_SESSION['UserID'];
        $sql = "UPDATE user SET email=:email
                WHERE userid=:userid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':email', $e, PDO::PARAM_STR);
            $stmt->bindParam(':userid', $uid, PDO::PARAM_STR);
            $stmt->execute();
            $_SESSION['changesMade'] = 1;
            header("Location: ".$_SERVER['REQUEST_URI']);
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function changeUsername()
    {
        $oldU = strtolower($_SESSION['Username']);
        $newU = strtolower($_POST['newUsername']);
        $uid = $_SESSION['UserID'];
        $sql = "UPDATE user SET username=:username
                WHERE userid=:userid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':username', $newU, PDO::PARAM_STR);
            $stmt->bindParam(':userid', $uid, PDO::PARAM_STR);
            $stmt->execute();
            $_SESSION['changesMade'] = 1;
            rename($_SERVER['DOCUMENT_ROOT']."/user/".$oldU, $_SERVER['DOCUMENT_ROOT']."/user/".$newU);
            $_SESSION['Username'] = $newU;
            header("Location: ".$_SERVER['REQUEST_URI']);
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function changePassword()
    {
        $sql = "SELECT userid
                FROM user
                WHERE userid=:user
                AND password=MD5(:pass)
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $_SESSION['UserID'], PDO::PARAM_STR);
            $stmt->bindParam(':pass', $_POST['oldPassword'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $_SESSION['passwordCorrect'] = 1;
                $sql = "UPDATE user SET password=MD5(:newPassword)
                WHERE userid=:userid";
                try {
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindParam(':newPassword', $_POST['newPassword'], PDO::PARAM_STR);
                    $stmt->bindParam(':userid', $_SESSION['UserID'], PDO::PARAM_STR);
                    $stmt->execute();
                    $_SESSION['changesMade'] = 1;
                    header("Location: ".$_SERVER['REQUEST_URI']);
                    return True;
                } catch (PDOException $e) {
                    return FALSE;
                }

            }
            else
            {
                $_SESSION['passwordCorrect'] = 0;
                return FALSE;
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }

    }

    public function getUsername($userid)
    {

        $sql = "SELECT username
                FROM user
                WHERE userid=:uid
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $userid, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['username'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getBio($username)
    {

        $sql = "SELECT bio
                FROM user
                WHERE username=:username
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['bio'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getTitle($username)
    {

        $sql = "SELECT title
                FROM user
                WHERE username=:username
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['title'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getIcon($username)
    {
        $sql = "SELECT icon
                FROM user
                WHERE username=:username
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['icon'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getUserRecipeThumbnailList($username)
    {
        $uid = getUserID($username);
        $sql = "SELECT recipeid, timestamp, title, pic1
                FROM recipe
                WHERE userid=:uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()>=1)
            {
                while($row = $stmt->fetch()) {
                    echo <<< THUMBHTML
<a href="recipes/{$row['recipeid']}">
        <span class="recipeLink" style="background-image: url('recipes/{$row['recipeid']}/recipeimages/{$row['pic1']}');">
            <span class="recipeTitle"><span>{$row['title']}</span></span>
        </span>
</a>
THUMBHTML;
                }
            }
            else
            {
                echo "<div class='noRecipes'>You haven't uploaded any recipes yet! Click the New Recipe link at the top right to get started.</div>";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
        return True;
    }

    /**
     * @return bool
     */
    public function submitRecipe()
    {
        $uid = $_SESSION['UserID'];
        $timestamp = time();
        $title = $_POST['recipe_title'];
        $description = $_POST['description'];
        $sql = "INSERT INTO recipe (userid, timestamp, title, description)
                VALUES (:uid, :timestamp, :title, :description)";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
            $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();

        }
        catch(PDOException $e)
        {
            return FALSE;
        }

        $recipeId = $this->_db->lastInsertID();

        $recipeDir=HTTP_SERVER."/user/".$_SESSION["Username"]."/recipes/".$recipeId."/recipeImages/";
        if (!is_dir($recipeDir))
        {
            mkdir($recipeDir, 0755, true);
        }
        $i=1;
        foreach ($_FILES["photos"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {

                $target_dir = $recipeDir;
                $file_name =  basename($_FILES["photos"]["name"][$key]);
                $target_file = $target_dir .$file_name;
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["photos"]["tmp_name"][$key]);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
                // Check if file already exists
                if (file_exists($target_file)) {
                    echo "Sorry, file already exists.";
                    $uploadOk = 0;
                }
                // Check file size
                if ($_FILES["photos"]["size"][$key] > 500000) {
                    echo "Sorry, your file is too large.";
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif"
                ) {
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["photos"]["tmp_name"][$key], $target_file)) {
                        $sql = "UPDATE recipe
                                SET pic".$i." = :picname
                                WHERE recipeid = :rid";
                        try
                        {
                            $stmt = $this->_db->prepare($sql);
                            $stmt->bindParam(':picname', $file_name, PDO::PARAM_STR);
                            $stmt->bindParam(':rid', $recipeId, PDO::PARAM_STR);
                            $stmt->execute();

                        }
                        catch(PDOException $e)
                        {
                            return FALSE;
                        }
                        $i++;
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                }
            }
        }

        $i=1;
        foreach ($_POST["ingredients"] as $ingredient){
            if (!empty($ingredient)){

                $sql = "INSERT INTO recipeingredients (ingredientnumber, recipeid, ingredient)
                VALUES (:number, :recipeid, :ingredient)";
                try
                {
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindParam(':number', $i, PDO::PARAM_STR);
                    $stmt->bindParam(':recipeid', $recipeId, PDO::PARAM_STR);
                    $stmt->bindParam(':ingredient', $ingredient, PDO::PARAM_STR);
                    $stmt->execute();
                    $i++;
                }
                catch(PDOException $e)
                {
                    return FALSE;
                }
            }
        }

        $i=1;
        foreach ($_POST["steps"] as $step){
            if (!empty($step)){

                $sql = "INSERT INTO recipestep (stepid, recipeid_fk, stepText)
                VALUES (:number, :recipeid, :step)";
                try
                {
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindParam(':number', $i, PDO::PARAM_STR);
                    $stmt->bindParam(':recipeid', $recipeId, PDO::PARAM_STR);
                    $stmt->bindParam(':step', $step, PDO::PARAM_STR);
                    $stmt->execute();
                    $i++;
                }
                catch(PDOException $e)
                {
                    return FALSE;
                }
            }
        }
        include("createRecipePage.php");
        return True;
    }

    public function getRecipeTitle($recipeNum)
    {

        $sql = "SELECT title
                FROM recipe
                WHERE recipeid=:rid
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['title'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getRecipeUserID($recipeNum)
    {

        $sql = "SELECT userid
                FROM recipe
                WHERE recipeid=:rid
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['userid'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getRecipeTimestamp($recipeNum)
    {

        $sql = "SELECT timestamp
                FROM recipe
                WHERE recipeid=:rid
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['timestamp'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
    public function getRecipeDescription($recipeNum)
    {

        $sql = "SELECT description
                FROM recipe
                WHERE recipeid=:rid
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                return $row['description'];
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getRecipeThumbnails($recipeNum)
    {

        $sql = "SELECT pic1, pic2, pic3
                FROM recipe
                WHERE recipeid=:rid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()==1)
            {
                $row = $stmt->fetch();
                if($row['pic1'] and $row['pic2'] and $row['pic3']){
                    echo "<div class='threePics foodPics''>";
                }
                elseif($row['pic1'] and $row['pic2']){
                    echo "<div class='twoPics foodPics''>";
                }
                else{
                    echo "<div class='onePic foodPics''>";
                }
                echo "<ul>";
                if ($row['pic1']){
                    echo "<li><a href='recipeImages/".$row['pic1']."' data-lightbox='".$recipeNum."'><img src='recipeImages/".$row['pic1']."'></a></li>";
                }
                if ($row['pic2']){
                    echo "<li><a href='recipeImages/".$row['pic2']."' data-lightbox='".$recipeNum."'><img src='recipeImages/".$row['pic2']."'></a></li>";
                }
                if ($row['pic3']){
                    echo "<li><a href='recipeImages/".$row['pic3']."' data-lightbox='".$recipeNum."'><img src='recipeImages/".$row['pic3']."'></a></li>";
                }
                echo "</ul>";
                echo "</div>";
                return True;
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getRecipeIngredients($recipeNum)
    {

        $sql = "SELECT ingredient
                FROM recipeingredients
                WHERE recipeid=:rid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()>=1)
            {
                echo "<ul>";
                while($row = $stmt->fetch()) {
                    echo "<li>".$row['ingredient']."</li>";
                }
                echo "</ul>";
                return True;
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getRecipeSteps($recipeNum)
    {

        $sql = "SELECT stepText
                FROM recipestep
                WHERE recipeid_fk=:rid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()>=1)
            {
                echo "<ol>";
                while($row = $stmt->fetch()) {
                    echo "<li>".$row['stepText']."</li>";
                }
                echo "</ol>";
                return True;
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function addToRecipeBook($recipeNum, $userID)
    {
        $sql = "SELECT recipeid_fk
                FROM recipebook
                WHERE recipeid_fk=:rid
                AND userid_fk=:uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
            $stmt->bindParam(':uid', $userID, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==0) {
                $sql = "INSERT INTO recipebook (userid_fk, recipeid_fk)
                        VALUES (:uid, :rid)";
                try {
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindParam(':rid', $recipeNum, PDO::PARAM_STR);
                    $stmt->bindParam(':uid', $userID, PDO::PARAM_STR);
                    $stmt->execute();
                    return "success";
                } catch (PDOException $e) {
                    return FALSE;
                }
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getUserRecipeBook($userid)
    {
        $sql = "SELECT recipeid_fk, userid_fk
                FROM recipeBook
                WHERE userid_fk=:uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $userid, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()>=1)
            {
                while($row = $stmt->fetch()) {
                    $recipeID = $row["recipeid_fk"];
                    $sqlIn = "SELECT timestamp, title, pic1, recipeid, userid
                    FROM recipe
                    WHERE recipeid=:rid
                    LIMIT 1";
                    try
                    {
                        $stmt2 = $this->_db->prepare($sqlIn);
                        $stmt2->bindParam(':rid', $recipeID, PDO::PARAM_STR);
                        $stmt2->execute();
                        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
                        if($stmt2->rowCount()==1)
                        {
                            $reciperow = $stmt2->fetch();
                            $recipeAuthorUsername = getUsername($reciperow['userid']);
                            echo <<< THUMBHTML
<a href="user/$recipeAuthorUsername/recipes/{$reciperow['recipeid']}">
        <span class="savedRecipe" style="background-image: url('user/$recipeAuthorUsername/recipes/{$reciperow['recipeid']}/recipeimages/{$reciperow['pic1']}');">
            <span class="recipeTitle"><span>{$reciperow['title']}</span></span>
        </span>
</a>
THUMBHTML;
                        }
                        else
                        {
                            echo "<div class='noRecipes'>Error getting recipes</div>";
                        }
                    }
                    catch(PDOException $e)
                    {
                        echo "error accessing recipe book database";
                        return FALSE;
                    }
                }
            }
            else
            {
                echo "<div class='noRecipes'>You haven't saved any recipes to your Recipe Book yet! Click the \"Add to Recipe Book\" link on any recipe to save it here.</div>";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
        return True;
    }

    public function updateIcon()
    {
        $uid = $_SESSION['UserID'];
        $userDir=HTTP_SERVER."/user/".$_SESSION["Username"]."/";
        if (!is_dir($userDir))
        {
            mkdir($userDir, 0755, true);
        }
        if ($_FILES["user_icon"]["error"] == UPLOAD_ERR_OK) {

            $target_dir = $userDir;
            $file_name =  basename($_FILES["user_icon"]["name"]);
            $target_file = $target_dir .$file_name;
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["user_icon"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
            // Check file size
            if ($_FILES["user_icon"]["size"] > 500000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }
            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif"
            ) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["user_icon"]["tmp_name"], $target_file)) {
                    $sql = "UPDATE user
                            SET icon = :picname
                            WHERE userid = :uid";
                    try
                    {
                        $imagePath = "user/".$_SESSION['Username']."/".$file_name;
                        $stmt = $this->_db->prepare($sql);
                        $stmt->bindParam(':picname', $imagePath, PDO::PARAM_STR);
                        $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                    catch(PDOException $e)
                    {
                        return FALSE;
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
        return True;
    }

    public function updateBio()
    {

        $sql = "UPDATE user
                SET bio=:bio
                WHERE userid=:uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':bio', $_POST["user_bio"], PDO::PARAM_STR);
            $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
    public function updateTitle()
    {

        $sql = "UPDATE user
                SET title=:title
                WHERE userid=:uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':title', $_POST["user_title"], PDO::PARAM_STR);
            $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            return FALSE;
        }

    }
    public function followUser($userid)
    {

        $sql = "INSERT INTO followedusers (userid_fk, followeduserid)
                VALUES (:uid, :fuid)";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_STR);
            $stmt->bindParam(':fuid', $userid, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            return "success";
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function checkIfFollow($userid)
    {

        $sql = "SELECT followeduserid
                FROM followedusers
                WHERE followeduserid=:fuid
                AND userid_fk=:uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_STR);
            $stmt->bindParam(':fuid', $userid, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if ($stmt->rowCount()>=1){
                return "yes";
            }
            else{
                return "no";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    /**
     * @return bool
     */
    public function getFeed()
    {

        $sql = "SELECT userid, title, description, pic1, pic2, pic3, recipeid, timestamp
                FROM recipe
                  INNER JOIN followedusers
                    ON recipe.userid = followedusers.followeduserid
                WHERE followedusers.userid_fk=:uid
                ORDER BY timestamp DESC";
        try
        {

            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION["UserID"], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($row = $stmt->fetch()){
                $username = getUsername($row["userid"]);
                $timestamp = date("M d y", $row["timestamp"]);
                $recipeTitle = $row["title"];
                $recipeDescription = $row["description"];
                echo <<< "HTML"
<div class={$row["title"]}>
    <!-- the user link will be dynamically generated-->
    <div class="userID userName"><h1><a href="user/$username">$username </a></h1></div>
    <div class="timeStamp">$timestamp</div>
    <div class="clear"></div>
HTML;
                if($row['pic1'] and $row['pic2'] and $row['pic3']){
                    echo "<div class='threePics foodPics''>";
                }
                elseif($row['pic1'] and $row['pic2']){
                    echo "<div class='twoPics foodPics''>";
                }
                else{
                    echo "<div class='onePic foodPics''>";
                }
                echo "<ul>";
                if ($row['pic1']){
                    echo "<li><a href='user/".$username."/recipes/".$row["recipeid"]."/recipeImages/".$row['pic1']."' data-lightbox='".$row["recipeid"]."'><img src='user/".$username."/recipes/".$row["recipeid"]."/recipeImages/".$row['pic1']."'></a></li>";
                }
                if ($row['pic2']){
                    echo "<li><a href='user/".$username."/recipes/".$row["recipeid"]."/recipeImages/".$row['pic2']."' data-lightbox='".$row["recipeid"]."'><img src='user/".$username."/recipes/".$row["recipeid"]."/recipeImages/".$row['pic2']."'></a></li>";
                }
                if ($row['pic3']){
                    echo "<li><a href='user/".$username."/recipes/".$row["recipeid"]."/recipeImages/".$row['pic3']."' data-lightbox='".$row["recipeid"]."'><img src='user/".$username."/recipes/".$row["recipeid"]."/recipeImages/".$row['pic3']."'></a></li>";
                }
                echo "</ul>";
                echo "</div>";

                echo <<< "RECIPECONTENT"
    <div class="recipeContent">
            <div class="recipeDescription">
                <h2><a href='user/$username/recipes/{$row["recipeid"]}'>$recipeTitle</a></h2>
                $recipeDescription
            </div>
RECIPECONTENT;
                $sql2 = "SELECT ingredient
                FROM recipeingredients
                WHERE recipeid=:rid";
                try
                {
                    $stmt2 = $this->_db->prepare($sql2);
                    $stmt2->bindParam(':rid', $row["recipeid"], PDO::PARAM_STR);
                    $stmt2->execute();
                    $stmt2->setFetchMode(PDO::FETCH_ASSOC);
                    if($stmt2->rowCount()>=1)
                    {
                        echo "<div class='recipeItemList'>";
                        echo "<h2>Ingredients</h2>";
                        echo "<ul>";
                        while($row2 = $stmt2->fetch()) {
                            echo "<li>".$row2['ingredient']."</li>";
                        }
                        echo "</ul>";
                    }
                }
                catch(PDOException $e)
                {
                    return FALSE;
                }

                $sql2 = "SELECT stepText
                FROM recipestep
                WHERE recipeid_fk=:rid";
                try
                {
                    $stmt2 = $this->_db->prepare($sql2);
                    $stmt2->bindParam(':rid', $row["recipeid"], PDO::PARAM_STR);
                    $stmt2->execute();
                    $stmt2->setFetchMode(PDO::FETCH_ASSOC);
                    if($stmt2->rowCount()>=1)
                    {
                        echo "<div class='recipeProcess'>";
                        echo "<h2>Steps</h2>";
                        echo "<ol>";
                        while($row2 = $stmt2->fetch()) {
                            echo "<li>".$row2['stepText']."</li>";
                        }
                        echo "</ol>";
                    }
                }
                catch(PDOException $e)
                {
                    return FALSE;
                }
                echo <<< "HTML"
        <div class="viewMore"><a href='user/$username/recipes/{$row["recipeid"]}'>...</a></div>
    </div>
</div>
</div>
</div>
HTML;
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function addToPantry()
    {
        if ($_POST['category']!="Enter New Category Name"){
            $sql = "INSERT INTO pantry (userid_fk, categorytitle)
                    VALUES (:uid, :catTitle)";
            try {
                $toInsert=ucwords(strtolower($_POST['category']));
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_STR);
                $stmt->bindParam(':catTitle', $toInsert, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return FALSE;
            }
            $newCategoryID = $this->_db->lastInsertID();
        }
        else{
            $sql = "SELECT categoryid FROM pantry WHERE categorytitle = :catTitle AND userid_fk=:uid";
            try {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_STR);
                $stmt->bindParam(':catTitle', $_POST['categoryDrop'], PDO::PARAM_STR);
                $stmt->execute();
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                while ($row = $stmt->fetch()) {
                    $newCategoryID = $row["categoryid"];
                }

            } catch (PDOException $e) {
                return FALSE;
            }
        }
        $categoryID = $newCategoryID;
        foreach ($_POST["itemName"] as $item){
            if (!empty($item)){

                $sql = "INSERT INTO pantryitems (categoryid_fk, itemName)
                VALUES (:catID, :item)";
                try
                {
                    $toInsert=ucwords(strtolower($item));
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindParam(':catID', $categoryID, PDO::PARAM_STR);
                    $stmt->bindParam(':item',$toInsert, PDO::PARAM_STR);
                    $stmt->execute();
                }
                catch(PDOException $e)
                {
                    return FALSE;
                }
            }
        }
    }

    public function getPantry()
    {

        $sql = "SELECT categoryid, categorytitle
                FROM pantry
                WHERE pantry.userid_fk = :uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION["UserID"], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()>=1){
                while($row = $stmt->fetch()) {
                    echo "<div class='pantryCategory' id='".$row['categoryid']."'>";
                    echo "<h2>" . $row['categorytitle'] . "</h2><ul>";
                    $sql2 = "SELECT itemName
                            FROM pantryitems
                            WHERE categoryid_fk = :catID";
                    try
                    {
                        $stmt2 = $this->_db->prepare($sql2);
                        $stmt2->bindParam(':catID', $row['categoryid'], PDO::PARAM_STR);
                        $stmt2->execute();
                        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
                        if($stmt2->rowCount()>=1){
                            while($row2 = $stmt2->fetch()) {
                                echo "<li>" . $row2['itemName'] . " <span class='".$row2['itemName']."'>X</span></li>";
                            }
                        }
                    }
                    catch(PDOException $e)
                    {
                        return FALSE;
                    }
                    echo "</ul></div>";
                }
                return True;
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function getPantryCategories()
    {

        $sql = "SELECT categorytitle
                FROM pantry
                WHERE pantry.userid_fk = :uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION["UserID"], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()>=1){
                while($row = $stmt->fetch()) {
                    echo "<option value='" . $row['categorytitle'] . "'>" . $row['categorytitle'] . "</option>";
                }
                return True;
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function deleteFromPantry($categoryID, $itemName)
    {
        $sql = "DELETE FROM pantryitems
                WHERE categoryid_fk = :catid
                AND itemName= :itemName";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':catid', $categoryID, PDO::PARAM_STR);
            $stmt->bindParam(':itemName', $itemName, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function addToList()
    {
        foreach ($_POST["listItemName"] as $item){
            if (!empty($item)){
                $sql = "INSERT INTO list (userid_fk, item)
                VALUES (:uid, :item)";
                try
                {
                    $toInsert = ucwords(strtolower($item));
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_STR);
                    $stmt->bindParam(':item', $toInsert, PDO::PARAM_STR);
                    $stmt->execute();
                }
                catch(PDOException $e)
                {
                    return FALSE;
                }
            }
        }
    }

    public function getList()
    {

        $sql = "SELECT item, itemnumber
                FROM list
                WHERE userid_fk = :uid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $_SESSION["UserID"], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($stmt->rowCount()>=1){
                while($row = $stmt->fetch()) {
                    echo "<li>".$row["item"]." <span class='deleteItem' id='".$row["itemnumber"]."'>X</span></li>";
                }
                return True;
            }
            else
            {
                return "nothing";
            }
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }

    public function deleteFromList($itemNumber)
    {
        $sql = "DELETE FROM list
                WHERE itemnumber = :itemNum";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':itemNum', $itemNumber, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
}

function getUserID($username)
{
    try {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
        $db = new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }

    $sql = "SELECT userid
                FROM user
                WHERE username = :username";
    try {
        $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                return  $row['userid'];
            }
            else{
                return -1;
            }
        } catch (PDOException $e) {
        return FALSE;
    }


}

function getUsername($userid)
{
    try {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
        $db = new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }

    $sql = "SELECT username
                FROM user
                WHERE userid = :userid";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch();
            return  $row['username'];
        }
        else{
            return -1;
        }
    } catch (PDOException $e) {
        return FALSE;
    }
}