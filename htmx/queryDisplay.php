<?php
    session_start();
    require("../db.php");
    $displayObj = new HTML_Display_Functions("localhost","music_site","root","");
    $dataObj = new SQL_Functions("localhost","music_site","root","");
    
            if (isset($_POST["songSearchDisplay"])) {
                $displayObj->songDisplayHtml();
            } 
            if (isset($_POST["playlistSearchDisplay"])) {
                $displayObj->playlistQueryDisplay();
            } 
            if (isset($_POST["userSearchDisplay"])) {
                $displayObj->userDisplayHtml();
            }
            ?>