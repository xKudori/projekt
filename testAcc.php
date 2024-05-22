<?php
        require("./db.php");

        $x = new Db_Connection("localhost","music_site","root","");

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
                $x->displayPublicPlaylists();
                echo "
            </tbody>
        </table>
            ";
            } else if (isset($_GET["songs"])) {
                echo "<div id=\"userSongTable\">";
                $x->songDisplayHtml();
                echo "</div>";
        }
?>

<script src="./audioPlayer.js">
    </script>