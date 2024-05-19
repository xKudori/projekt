<?php

session_start();

require("./db.php");

$x = new Db_Connection("localhost","music_site","root","");

require_once("./navbar.php");

$x->playlistNameDisplayHtml();

if (isset($_GET["x"]) && $x->isUserPlaylist($_GET["x"]) == false && $x->isPlaylistLikedByUser($_GET["x"]) == false) {
    $pId = $_GET["x"];
echo"
<form method=\"post\">
<button name=\"likePlaylist\" value=\"$pId\">Like</button>
</form>";
if (isset($_POST["likePlaylist"])) {
$x->likePlaylist($pId);
echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
}

}

$x->songDisplayHtml();

?>

<script src="./audioPlayer.js">
    </script>