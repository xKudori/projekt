<?php
    session_start();
    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");

    echo "<a hx-trigger=\"click\" hx-get=\"./choiceDisplay.php\" hx-target=\"#SongUpload\" hx-swap=\"innerHTML\" class=\"btn\">Back</a>";

    echo "<form action=\"\" class=\"f2\" enctype=\"multipart/form-data\" method=\"post\">";
        //echo "<button name=\"esc\">Back</button> <br>";
        echo "<label for=\"audio-file\">Browse: </label>
        <input type=\"file\" name=\"audio-file\" id=\"audio-file\" accept=\".ogg, .flac, .mp3\">
        <br>
        <br>
        <label for=\"songName\">Name:</label>
        <input type=\"text\" name=\"songName\">
        <br>
        <br>
        <label for=\"insertPlaylist\" id=\"playlistSelect\">Select a local playlist to insert the song into</label>
        <br>
        <div class=\"Help\">List of avilable playlists
        <span class=\"helpText\">";
            echo $x->displayLocalPlaylists();
            echo "</span>
        </div>
        <br>
        <br>
        <br>
        <button name=\"sendSong\">Upload File</button>";
echo "</form>";
?>