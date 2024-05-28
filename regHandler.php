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

        // Walidacja nazwy użytkownika
        if (strlen($username) < 6) {
            echo "Username must be at least 6 characters long.";
            exit();
        }

        // Walidacja hasła
        if (!validatePassword($password)) {
            echo "Password must be at least 8 characters long, contain at least one uppercase letter, and one special character.";
            exit();
        }

        require_once("./db.php");
        $x = new Db_Connection("localhost", "music_site", "root", "");
        $userExists = $x->checkIfUserExists($username, $email);
        
        switch($userExists) {
            case false:
                $success = $x->registerUser($username, $password, $email);
                $x->createUserPlaylist($username, "User");
                $x->createUserPlaylist($username, "Liked");
                header("Location: login.php");
                break;
            case true:
                echo "User already exists. Please choose a different username or email.";
                break;
        }
    }
}
?>
