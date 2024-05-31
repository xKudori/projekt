<?php
    session_start();
    require("./db.php");
    $displayObj = new HTML_Display_Functions("localhost","music_site","root","");
    $dataObj = new SQL_Functions("localhost","music_site","root","");
    
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    $user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="./images/misc/moon3.png">
    <title>LunaChord</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/htmx.org@1.7.0/dist/htmx.min.js"></script>
    <script src="./JS/easyTimer/easytimer.js"></script>
</head>
<body>
    <section id="main">
        <?php
            require_once("./site_parts/leftTab.php");
        ?>
        <section id="middleTab">
            <?php
                require_once("./site_parts/navbar.php");
                $q = $_GET["query"];
            ?>
            <div id="songsTitle">   
            <form hx-post="./htmx/queryDisplay.php" hx-push-url="./search.php?query=<?=$q?>&songs" hx-trigger="click" hx-target="#queryResult" hx-swap="innerHTML" method="post">
                <button name="songSearchDisplay" class="btn" id="songSearchDisplay" value="Songs">Songs</button>
                <input type="hidden" name="query" value=<?=$_GET["query"]?>>
            </form>  
            <form hx-post="./htmx/queryDisplay.php" hx-trigger="click" hx-target="#queryResult" hx-swap="innerHTML" method="post">  
                <button name="playlistSearchDisplay" class="btn" id="playlistSearchDisplay" value="Playlists">Playlists</button>
                <input type="hidden" name="query" value=<?=$_GET["query"]?>>
            </form>  
            <form hx-post="./htmx/queryDisplay.php" hx-trigger="click" hx-target="#queryResult" hx-swap="innerHTML" method="post">     
                <button name="userSearchDisplay" class="btn" id="userSearchDisplay" value="User">User</button> 
                <input type="hidden" name="query" value=<?=$_GET["query"]?>>
            </form>
            <div id="currentDisplay">
            <?php
                $q = $_GET["query"];
                if (isset($_GET["songs"])) {
                    echo "Songs";
                } else if (isset($_GET["playlists"])) {
                    echo "Playlists";
                } else if (isset($_GET["users"])) {
                    echo "User";
                } 
                ?>
            </div>
            </div>
            <div id="queryResult">
            <?php
            if (isset($_GET["songs"])) {
                $displayObj->songDisplayHtml();
            } 
            if (isset($_GET["playlists"])) {
                $displayObj->playlistQueryDisplay();
            } 
            if (isset($_GET["users"])) {
                $displayObj->userDisplayHtml();
            }
            ?>
            </div>
        </section>
        <?php
            require_once("./site_parts/rightTab.php");
            require_once("./site_parts/bottomTab.html");
        ?>
    <script>
        function displayButtonValue() {
            var displayButtons = document.querySelectorAll('.btn');
                displayButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        var value = button.value;
                        document.getElementById('currentDisplay').innerText = value;
                    });
                });
        }
        document.addEventListener('htmx:afterSwap', function() {
            displayButtonValue();
        });

        document.addEventListener('DOMContentLoaded', function() {
            displayButtonValue();
        });
    </script>
</body>
</html>