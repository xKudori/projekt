<?php

session_start();

require("../db.php");

$displayObj = new HTML_Display_Functions("localhost","music_site","root","");
$dataObj = new SQL_Functions("localhost","music_site","root","");

    require_once("../site_parts/navbar.php");

    echo "<div id=\"songDisplayContainer\">";

    $displayObj->playlistNameDisplayHtml();

    if (isset($_GET["x"]) && $dataObj->isUserPlaylist($_GET["x"]) == false && $dataObj->isPlaylistLikedByUser($_GET["x"]) == false) {
        $pId = $_GET["x"];
    echo"
    <form method=\"post\">
    <button name=\"likePlaylist\" value=\"$pId\" id=\"like\">Like</button>
    </form>";
    if (isset($_POST["likePlaylist"])) {
    $dataObj->likePlaylist($pId);
    echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
    }

    }

    $displayObj->songDisplayHtml();


    echo "</div>";

    if(isset($_GET["swapDisplay"])) {
        $displayObj->songDisplayHtml();
    }
?>

