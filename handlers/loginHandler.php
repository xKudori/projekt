<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" type="image/x-icon" href="./images/misc/moon3.png">
    <title>LunaChord</title>
</head>
<body>
<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["Uname"]) && isset($_POST["password"])) {
                $username = $_POST["Uname"];
                $password = $_POST["password"];

                require_once("../db.php");

                $logObj = new SQL_Functions("localhost","music_site","root","");
                
                $isLoggedIn = $logObj->loginUser($username, $password);
                
                if ($isLoggedIn) {
                    $_SESSION["username"] = $username;
                    $likedId = $logObj->getUserLikedSongs($username);
                    header("Location: ../index.php?x=$likedId"); 
                    exit;
                } else {
                    echo "Incorrect username or password. Please try again.";
                }
            }
        }
?>
</body>