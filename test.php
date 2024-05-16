
<?php

session_start();

require("./db.php");

$x = new Db_Connection("localhost","music_site","root","");


if (isset($_GET["x"]) && !isset($_GET["u"]) && !isset($_GET["query"])) {


    require_once("./navbar.php");

    echo "
    <div class=\"displaySongs\">
    ";
     $x->playlistNameDisplayHtml(); 
     echo "
        <form action=\"\" method=\"post\">
        ";
            $tempVal=1;  
                $x->songDisplayHtml();
    echo "
        </form>
    </div>
    ";


} else if (!isset($_GET["x"]) && isset($_GET["u"]) && !isset($_GET["query"])) {
        $u = $_GET["u"];
            require_once("navbar.php");
            echo "
            <div id=\"displayContainer\">
                <div id=\"displayUserName\">
                    <p id=\"accusername\">
                        ";
                            if (isset($_GET["u"])) {
                                echo $_GET["u"];
                            }
                        echo "
                    </p>
                    ";
                        if ($_SESSION["username"] == $_GET["u"]) {
                            echo "<a href=\"logout.php\">(Logout)</a>";
                        }
                    echo "
                    <p id=\"userInfo\">
                    ";
                        echo $x->countSong($_GET["u"]);
                        echo $x->countPlaylists($_GET["u"]);
                        echo "
                    </p>
                </div>
                <div id=\"displayChoice\">";
                    echo "<a class=\"button\" href=\"account.php?u=$u&songs\" hx-trigger=\"click\" hx-get=\"./test2.php?u=$u&songs\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\">Songs</a>";
                    echo "<a class=\"button\" href=\"account.php?u=$u&playlists\" hx-trigger=\"click\" hx-get=\"./test2.php?u=$u&playlists\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\">Public Playlists</a>";
                    echo "
                    
                </div>
            </div>
        ";
        echo "<div id=\"resultContainer\"";
        if (isset($_GET["playlists"])) {
        echo "
        <table id=\"userPublicPlaylists\">
        <thead>
            <tr>
                <th>$u's Public Playlists</th>
            </tr>
        </thead>
        <tbody>";
            if (isset($_GET["playlists"])) {
            $x->displayPublicPlaylists();
            echo "
        </tbody>
    </table>
        ";
    } else if (isset($_GET["songs"])) {
        $x->songDisplayHtml();
    }
    echo "
    </div>
    </div>
    ";
}

}

?>

<script src="./audioPlayer.js">
    </script>