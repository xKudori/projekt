<?php
        require("../db.php");

        $displayObj = new HTML_Display_Functions("localhost","music_site","root","");
        $dataObj = new SQL_Functions("localhost","music_site","root","");
        
        session_start();        
        $u = $_GET["u"];
        if (isset($_GET["playlists"])) {
            echo "
            <table id=\"userPublicPlaylists\">
            <thead>
                <tr>
                    <th>$u's Public Playlists</th>
                </tr>
            </thead>
            <tbody>";
                $displayObj->displayPublicPlaylists();
                echo "
            </tbody>
        </table>
            ";
            } else if (isset($_GET["songs"])) {
                echo "<div id=\"userSongTable\">";
                $displayObj->songDisplayHtml();
                echo "</div>";
        }
?>

