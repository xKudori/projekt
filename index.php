<?php
    session_start();
    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
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
            <div id="top">
                <div id="homeContainer">
                    Home
                </div>
                <a href="./account.php" id="accountContainer">
                    Account
                </a>
                <div id="searchContainer">
                    Search
                </div>
            </div> 
            <div id="displayPlaylistName">
                <?php
                    $x->playlistNameDisplayHtml();
                ?>
            </div>
            <div id="displaySongs">
                <form action="" method="post">
                    <?php                   
                    $tempVal=1;  
                        $x->songDisplayHtml();
                    ?>
                </form>
            </div>
        </section>
        <section id="rightTab">
            <div id="playlistSelectionTitle">Playlist Selection</div>
                <div class="userPlaylists">
                    Your playlists
                    <form method="post">
                        <button id="sortButton" name="sort">(Sort)</button>
                    </form>
                </div>
            <div id="playlistSelection">
                <?php
                    $x->displayPlaylistsHtml();
                ?>
                <div class="userPlaylists">
                    Liked playlists
                </div>
            </div>

        </section>
        <section id="bottomTab">
            <button id="playBtn">Play</button>
            <button id="stop">Stop</button>
            <input type="range" min="0" max="1" step="0.01" id= "vol">
        </section>
    </section>
    <script>
        let playBtn = document.getElementById("playBtn");
        let pause = document.getElementById("stop");
        let vol = document.getElementById("vol");
        let audio = new Audio("<?php $x->displayAudio()?>");
        let container = document.getElementById("length");

        function playAudio() {
            audio.play();
        }
        function stopAudio() {
            audio.pause();
            audio.currentTime = 0;
        }
        function audioVolume() {
            audio.volume = parseFloat(vol.value);
        }
        playBtn.addEventListener("click", playAudio);
        pause.addEventListener("click",stopAudio);
        vol.addEventListener("input", audioVolume);
    </script>
</body>
</html>