<?php

echo "<div id=\"SongUpload\">
<form action=\"\" hx-post=\"songInput.php\" hx-trigger=\"click\" hx-target=\"#SongUpload\" hx-swap=\"innerHTML\" method=\"post\" class=\"f2\" enctype=\"multipart/form-data\">
        <button name=\"btn1\" class=\"button\">Upload Song</button>
</form>
<br>
<form action=\"\" hx-post=\"localInput.php\" hx-trigger=\"click\" hx-target=\"#SongUpload\" hx-swap=\"innerHTML\" method=\"post\" class=\"f2\" enctype=\"multipart/form-data\">
        <button name=\"btn2\" class=\"button\">Upload files to Local Playlist</button>
</form>
</div>";

?>