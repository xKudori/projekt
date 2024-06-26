<script src="./JS/validation/songValidate.js"></script>
<?php
    session_start();
    require("../db.php");
    $displayObj = new HTML_Display_Functions("localhost","music_site","root","");
    
    echo "<a hx-trigger=\"click\" hx-get=\"./htmx/choiceDisplay.php\" hx-target=\"#SongUpload\" hx-swap=\"innerHTML\">Back</a>";
        

    echo "<form action=\"\" class=\"f2\" enctype=\"multipart/form-data\" method=\"post\" onsubmit=\"return validateForm()\">";
    //echo "<button name=\"esc\">Back</button> <br>";
        echo "<label for=\"audio-file\">Audio file: </label>
        <input type=\"file\" name=\"audio-file\" id=\"audio-file\" accept=\".ogg, .flac, .mp3\">
        <br>
        <label for=\"image\">Image file: </label>
        <input type=\"file\" name=\"image\" id=\"img\" accept=\".png, .jpg\">
        <br>
        <br>
        <label for=\"songName\">Name:</label>
        <input type=\"text\" name=\"songName\">
        <br>
        <label for=\"insertPlaylist\" id=\"playlistSelect\">Select a playlist to insert the song into</label>
        <br>
        <div class=\"Help\">List of avilable playlists
        <span class=\"helpText\">";
            echo $displayObj->displayLocalPlaylists();
            echo "</span>
        </div>
        <br>
        <button name=\"sendSong\">Upload File</button>";
echo "</form>";
?>