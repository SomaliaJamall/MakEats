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
                    $uid = $row['userid'];
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

                return TRUE;
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

        return False;
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
}