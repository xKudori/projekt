<script src="./JS/validation/songValidate.js"></script>
<?php
    session_start();
    require("../db.php");
    $displayObj = new HTML_Display_Functions("localhost","music_site","root","");
    $dataObj = new SQL_Functions("localhost","music_site","root","");
    

    echo "<a hx-trigger=\"click\" hx-get=\"./htmx/choiceDisplay.php\" hx-target=\"#SongUpload\" hx-swap=\"innerHTML\">Back</a>";
    echo "<form action=\"\" onsubmit=\"return validateUserSongForm()\" method=\"post\" class=\"f2\" enctype=\"multipart/form-data\">";
        echo "
        <br>
        <label for=\"audio-file\">Audio file: </label>
        <input type=\"file\" name=\"audio-file\" id=\"audio-file\" accept=\".ogg, .flac, .mp3\">
        <br>
        <label for=\"image\">Image file: </label>
        <input type=\"file\" name=\"image\" id=\"img\" accept=\".png, .jpg\">
        <br>
        <br>
        <label for=\"songName\">Name:</label>
        <input type=\"text\" name=\"songName\">
        <br>
        <br>
        <button name=\"sendUserSong\">Upload Song</button>";
    echo "</form>";
    
?>