<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["Uname"]) && isset($_POST["password"])) {
                $username = $_POST["Uname"];
                $password = $_POST["password"];

                require_once("./db.php");

                $x = new Db_Connection("localhost", "music_site", "root", "");

                $isLoggedIn = $x->loginUser($username, $password);
                
                if ($isLoggedIn) {
                    $_SESSION["username"] = $username;
                    header("Location: ./index.php"); 
                    exit;
                } else {
                    echo "Incorrect username, email, or password. Please try again.";
                }
            }
        }
?>