<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" type="image/x-icon" href="./images/misc/moon3.png">
    <title>LunaChord - ERR</title>
</head>
<body>
<?php
session_start();

function validatePassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*\W).{8,}$/', $password);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $email = trim($_POST["email"]);

        if (strlen($username) < 6) {
            echo "Username must be at least 6 characters long.";
            exit();
        }

        if (!validatePassword($password)) {
            echo "Password must be at least 8 characters long, contain at least one uppercase letter, and one special character.";
            exit();
        }

        require_once("../db.php");
        $regObj = new SQL_Functions("localhost","music_site","root","");
                
        $userExists = $regObj->checkIfUserExists($username, $email);
        
        switch($userExists) {
            case false:
                $success = $regObj->registerUser($username, $password, $email);
                $regObj->createUserPlaylist($username, "User");
                $regObj->createUserPlaylist($username, "Liked");
                header("Location: ../login.php");
                break;
            case true:
                echo "User already exists. Please choose a different username or email.";
                break;
        }
    }
}
?>
</body>