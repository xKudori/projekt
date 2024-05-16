<?php
    session_start();
    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    $user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="moon3.png">
    <title>LunaChord</title>
</head>
<body>
    <section id="main">
        <?php
            require_once("leftTab.php");
        ?>      
        <section id="middleTab">
        <?php
            require_once("navbar.php");
        ?>
            <div id="displayContainer">
                <div id="displayUserName">
                    <p id="accusername">
                        <?php
                            if (isset($_GET["u"])) {
                                echo $_GET["u"];
                            }
                        ?>
                    </p>
                    <?php
                        if ($_SESSION["username"] == $_GET["u"]) {
                            echo "<a href=\"logout.php\">(Logout)</a>";
                        }
                    ?>
                    <p id="userInfo">
                    <?php
                        echo $x->countSong($_GET["u"]);
                        echo $x->countPlaylists($_GET["u"]);
                    ?>
                    </p>
                </div>
                <div id="displayChoice">
                    <?php /*echo "form method=\"post\">
                        <button class=\"btn\" name=\"PublishedSongs\">Songs</button>
                        <button class=\"btn\" name=\"PublicPlaylists\">Public Playlists</button>
                    </form>";*/
                    $u = $_GET["u"];
                    $u = $_GET["u"];
                    echo "<a class=\"button\" href=\"account.php?u=$u&songs\">Songs</a>";
                    echo "<a class=\"button\" href=\"account.php?u=$u&playlists\">Public Playlists</a>";
                    ?>
                </div>
            </div>
            <div id="displayTables">
                <?php
                    if (isset($_GET["playlists"])) {
                        $u = $_GET["u"];
                        echo "<table id=\"userPublicPlaylists\">
                        <thead>
                            <tr>
                                <th>$u's Public Playlists</th>
                            </tr>
                        </thead>
                        <tbody>";
                            $x->displayPublicPlaylists();
                        echo "</tbody>
                    </table>";
                    } else if (isset($_GET["songs"])) {
                        $x->songDisplayHtml();
                    }
            ?>
        </div>
        </section>
        <?php
            require_once("rightTab.php");
            require_once("bottomTab.html");
        ?>
    <script src="./audioPlayer.js">
    
    </script>
</body>
</html>