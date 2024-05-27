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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/htmx.org@1.7.0/dist/htmx.min.js"></script>
    <script src="./easyTimer/easytimer.js"></script>
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
            <form hx-post="queryDisplay.php" hx-trigger="click" hx-target="#queryResult" hx-swap="innerHTML" method="post">
                <button name="songSearchDisplay" class="btn" id="songSearchDisplay" value="Songs">Songs</button>
                <input type="hidden" name="query" value=<?=$_GET["query"]?>>
            </form>  
            <form hx-post="queryDisplay.php" hx-trigger="click" hx-target="#queryResult" hx-swap="innerHTML" method="post">  
                <button name="playlistSearchDisplay" class="btn" id="playlistSearchDisplay" value="Playlists">Playlists</button>
                <input type="hidden" name="query" value=<?=$_GET["query"]?>>
            </form>  
            <form hx-post="queryDisplay.php" hx-trigger="click" hx-target="#queryResult" hx-swap="innerHTML" method="post">     
                <button name="userSearchDisplay" class="btn" id="userSearchDisplay" value="Users">Users</button> 
                <input type="hidden" name="query" value=<?=$_GET["query"]?>>
            </form>
            <?php
                $q = $_GET["query"];
                /*
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
*/
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
            <div id="queryResult">
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
            </div>
        </section>
        <?php
            require_once("rightTab.php");
            require_once("bottomTab.html");
        ?>

</body>
</html>