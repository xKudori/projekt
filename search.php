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
    <title>Document</title>
</head>
<body>
    <section id="main">
        <section id="leftTab">
            <div id="fInputTitle">Select File</div>
            <div id="fInput">
                <form action="" method="post" id="f2" enctype="multipart/form-data">
                    <?php 
                        $x->formDisplay(); 
                    ?>
                </form>
            </div>
            <?php
                $x->getSongData();
            ?>
            <div id="createPlaylistTitle">Create Playlist</div>
            <div id="createPlaylist">
                <form action="" method="post" id="f1" enctype="multipart/form-data">
                    <label for="playlistName">Playlist name: </label>
                    <input type="text" name="playlistName">
                    <br>
                    <br>
                    <!--<label for="cover-art">Cover art: </label>
                    <input type="file" name="cover-art">-
                    <br>
                    <br>-->
                    <label>Playlist type: </label>
                    <br>
                    <div class="type">
                        <label for="Public">Public</label>
                        <div class="Help">(?)
                            <span class="helpText">A public playlist will be visible to everyone</span>
                        </div>
                        <input type="radio" name="playlistType" value="Public">
                    </div>
                    <div class="type">
                        <label for="Private">Private</label>
                        <div class="Help">(?)
                            <span class="helpText">A private playlist will only be visible to you and can only by accessed by you</span>
                        </div>
                        <input type="radio" name="playlistType" value="Private">
                    </div>
                    <div class="type">
                        <label for="Local">Local</label>
                        <div class="Help">(?)
                            <span class="helpText">
                                A local playlist is where you can store your <br>
                                 own imported audio files from your device. <br>
                                It will not be accessible to anyone and cannot be shared.
                            </span>
                        </div>
                        <input type="radio" name="playlistType" value="Local">
                    </div>
                    <br>
                    <button name="Create">Create</button>
                </form>
                <?php
                    $x->getPlaylistData();
                ?> 
            </div>
        </section>         
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