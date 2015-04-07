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
                    include($_SERVER['DOCUMENT_ROOT']."/createUserPage.php");
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
<a href="{$row['recipeid']}">
        <span class="recipeLink" style="background-image: url('recipe/recipeimages/{$row['pic1']}');">
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
        $title = $_POST['title'];
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

        $recipeDir="user/".$_SESSION["Username"]."/recipes/".$recipeId."/recipeImages/";
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
        return True;
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