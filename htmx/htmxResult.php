
<?php

session_start();

require("../db.php");

$displayObj = new HTML_Display_Functions("localhost","music_site","root","");
$dataObj = new SQL_Functions("localhost","music_site","root","");


if (isset($_GET["x"]) && !isset($_GET["u"]) && !isset($_GET["query"])) {


    require_once("../site_parts/navbar.php");

    echo "
    <div class=\"displaySongs\">
    ";
     $displayObj->playlistNameDisplayHtml(); 
     echo "
        <form action=\"\" method=\"post\">
        ";
            $tempVal=1;  
                $displayObj->songDisplayHtml();
    echo "
        </form>
    </div>
    ";


} else if (!isset($_GET["x"]) && isset($_GET["u"]) && !isset($_GET["query"])) {
        $u = $_GET["u"];
            require_once("../site_parts/navbar.php");
            echo "
            <div id=\"displayContainer\">
            <div id=\"displayPfp\">
            <img src=".$displayObj->getUserProfilePicture($u)." alt=\"\" id=\"userImage\">
        </div>
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
                            echo "<button onclick=\"confirmDelete()\">Delete Account</button>";
                    } 
                } else {
                    if ($_GET["u"] != "admin" && $_SESSION["username"] == "admin") {
                        echo "<form action=\"\" method=\"post\">
                            <button name=\"deleteUser\">Delete User</button>
                        </form>";

                        if (isset($_POST["deleteUser"]))  {
                            $dataObj->deleteUser($_GET["u"]);
                            $currentUser = $_SESSION["username"];
                            echo "<script>window.location.href = './account.php?u=$currentUser;</script>";
                            }
                    }
                }
                    echo "
                    <p id=\"userInfo\">
                    ";
                    if ($_GET["u"] != "admin") {
                        echo $displayObj->countSong($_GET["u"]);
                        echo $displayObj->countPlaylists($_GET["u"]);
                    }
                        echo "
                    </p>
                </div>
                <div id=\"displayChoice\">";

                if ($u != "admin") {
                    echo "<a class=\"button\" href=\"account.php?u=$u&songs\" hx-push-url=\"account.php?u=$u&songs\" hx-trigger=\"click\" hx-get=\"./htmx/accResult.php?u=$u&songs\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\"\">Songs</a>";
                    echo "<a class=\"button\" href=\"account.php?u=$u&playlists\" hx-push-url=\"account.php?u=$u&playlists\" hx-trigger=\"click\" hx-get=\"./htmx/accResult.php?u=$u&playlists\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\"\">Public Playlists</a>";
                } else {
                    echo "<a class=\"button\" href=\"logout.php\">Logout</a>";
                }
                    echo "
                </div>
            </div>
        ";
        echo "<div id=\"resultContainer\">";

    echo "
    </div>
    ";

}



?>


<script>
    function confirmDelete() {
        if (confirm("Are you sure you want to delete your account?")) {
            window.location.href = "account_del.php";
            }
        }
</script>
