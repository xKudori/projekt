<?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $email = $_POST["email"];
            require_once("./db.php");
            $x = new Db_Connection("localhost","music_site","root","");
            $userExists = $x->checkIfUserExists($username,$email);
            switch($userExists) {
                case false:
                    $success = $x->registerUser($username, $password, $email);
                    $x->createUserPlaylist($username);
                        header("Location: login.php");
                break;
            case true:
                echo "User already exists. Please choose a different username or email.";
                break;
        }
    }
}
?>