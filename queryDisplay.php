<?php
    session_start();
    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");
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