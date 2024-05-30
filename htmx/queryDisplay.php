<?php
    session_start();
    require("../db.php");
    $x = new HTML_Display_Functions("localhost","music_site","root","");
    $y = new SQL_Functions("localhost","music_site","root","");
    
            if (isset($_POST["songSearchDisplay"])) {
                $x->songDisplayHtml();
            } 
            if (isset($_POST["playlistSearchDisplay"])) {
                $x->playlistQueryDisplay();
            } 
            if (isset($_POST["userSearchDisplay"])) {
                $x->userDisplayHtml();
            }
            ?>