<?php

    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");
    
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
                <form action="" method="post" id="f2">
                    <label for="audio-file">Browse: </label>
                    <input type="file" name="audio-file">
                    
                </form>
            </div>
            <div id="createPlaylistTitle">Create Playlist</div>
            <div id="createPlaylist">
                <form action="" method="post" id="f1">
                    <label for="playlistName">Playlist name: </label>
                    <input type="text" name="playlistName">
                    <br>
                    <br>
                    <label for="cover-art">Cover art: </label>
                    <input type="file" name="cover-art">
                    <br>
                    <br>
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
                                 own imported audio files from your computer. <br>
                                It will not be accessible to anyone and cannot be shared.
                            </span>
                        </div>
                        <input type="radio" name="playlistType" value="Local">
                    </div>
                    <br>
                    <br>
                    <button name="Create">Create</button>
                </form>
            </div>
        </section>    
        <?php

            $x->getPlaylistData();
            
        ?>      
        <section id="middleTab">
            <div id="top">
                <div id="homeContainer">
                    Home
                </div>
                <div id="accountContainer">
                    Account
                </div>
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
                    <?php                     
                        $x->songDisplayHtml();
                    ?>
            </div>
        </section>
        <section id="rightTab">
            <div id="playlistSelectionTitle">Playlist Selection</div>
            <div id="playlistSelection">
                <?php
                $x->displayPlaylistsHtml();
                ?>
            </div>
        </section>
        <section id="bottomTab">
            
        </section>
    </section>
    
</body>
</html>