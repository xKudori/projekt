
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
                            if ($_GET["u"] == "admin") {
                            echo "Welcome to the Admin Panel";
                            } else {
                                echo $_GET["u"];
                            }
                        } 

                        echo "
                    </p>
                    ";
                    if ($_SESSION["username"] == $_GET["u"]) {
                        if ($_GET["u"] != "admin" && $_SESSION["username"] != "admin") {
                        echo "<button><a href=\"logout.php\" class=\"logout\">Logout</a></button>";
                        echo "<form action=\"\" method=\"post\">
                            <button name=\"deleteUser\">Delete Account</button>
                        </form>";
                        if (isset($_POST["deleteUser"])) {
                            $x->deleteUser($_GET["u"]);
                        }
                    } 
                } else {
                    if ($_GET["u"] != "admin" && $_SESSION["username"] == "admin") {
                        echo "<form action=\"\" method=\"post\">
                            <button name=\"deleteUser\" hx-trigger=\"click\" hx-post=\"./test.php?u=$u\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\"\">Delete User</button>
                        </form>";

                        if (isset($_POST["deleteUser"])) {
                            $x->deleteUser($_GET["u"]);
                            echo "<script>window.location.href = './index.php;</script>";
                        }
                    }
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

                if ($u != "admin") {
                    echo "<a class=\"button\" href=\"account.php?u=$u&songs hx-trigger=\"click\" hx-get=\"./testAcc.php?u=$u&songs\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\"\">Songs</a>";
                    echo "<a class=\"button\" href=\"account.php?u=$u&playlists\" hx-trigger=\"click\" hx-get=\"./testAcc.php?u=$u&playlists\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\"\">Public Playlists</a>";
                } else {
                    echo "<a class=\"button\" href=\"logout.php\">Logout</a>";
                }
                    echo "
                </div>
            </div>
        ";
        echo "<div id=\"resultContainer\"";

    echo "
    </div>
    ";

}

?>

<script src="./audioPlayer.js">
    </script>