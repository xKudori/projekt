<?php
    session_start();
    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");
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
    <link rel="icon" type="image/x-icon" href="moon3.png">
    <title>LunaChord</title>
    <script src="https://unpkg.com/htmx.org@1.7.0/dist/htmx.min.js"></script>
</head>
<body>
    <section id="main">
        <?php
            require_once("leftTab.php");
        ?>
        <section id="middleTab">
            <?php
                require_once("navbar.php");
            ?>
            <div id="songsTitle">   
            <form method="post">
                <button name="songSearchDisplay" class="btn" id="songSearchDisplay" value="Songs">Songs</button>
                <button name="playlistSearchDisplay" class="btn" id="playlistSearchDisplay" value="Playlists">Playlists</button>
                <button name="userSearchDisplay" class="btn" id="userSearchDisplay" value="Users">Users</button> 
            </form>
            <?php
                $q = $_GET["query"];
                if (isset($_POST["songSearchDisplay"])) {
                    //echo "<div id=\"currentDisplay\">Songs</div>";
                    echo "<script>window.location.href = './search.php?query=$q&songs';</script>"; 
                }
                else if (isset($_POST["playlistSearchDisplay"])) {
                    echo "<script>window.location.href = './search.php?query=$q&playlists';</script>";
                    //echo "<div id=\"currentDisplay\">Playlists</div>";
                } 
                else if (isset($_POST["userSearchDisplay"])) {
                    echo "<script>window.location.href = './search.php?query=$q&users';</script>";
                    //echo "<div id=\"currentDisplay\">Users</div>";   
                }
                else {
                    //echo "<div id=\"currentDisplay\"></div>";   
                }

                if (isset($_GET["songs"])) {
                    echo "<div id=\"currentDisplay\">Songs</div>";
                } else if (isset($_GET["playlists"])) {
                    echo "<div id=\"currentDisplay\">Playlists</div>";
                } else if (isset($_GET["users"])) {
                    echo "<div id=\"currentDisplay\">Users</div>";
                } else {
                    echo "<div id=\"currentDisplay\"></div>";   
                }
                
                ?>
            </div>
            <?php
            if (isset($_GET["songs"])) {
                $x->songDisplayHtml();
            } 
            if (isset($_GET["playlists"])) {
                $x->playlistQueryDisplay();
            } 
            if (isset($_GET["users"])) {
                $x->userDisplayHtml();
            }
            ?>
        </section>
        <?php
            require_once("rightTab.php");
            require_once("bottomTab.html");
        ?>
    <script src="./audioPlayer.js">
    
    </script>
</body>
</html>