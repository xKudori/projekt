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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/htmx.org@1.7.0/dist/htmx.min.js"></script>
    <script src="./easyTimer/easytimer.js"></script>
</head>
<body>
    <section id="main">
        <?php
            require_once("./site_parts/leftTab.php");
        ?>      
        <section id="middleTab">
        <?php
            require_once("./site_parts/navbar.php");
        ?>
            <div id="displayContainer">
                <div id="displayUserName">
                    <p id="accusername">
                        <?php
                            if (isset($_GET["u"])) {
                                if ($_GET["u"] == "admin") {
                                echo "Welcome to the Admin Panel";
                                } else {
                                    echo $_GET["u"];
                                }
                            } 
                        
                        ?>
                    </p>
                    <?php
                        if ($_SESSION["username"] == $_GET["u"]) {
                            if ($_GET["u"] != "admin" && $_SESSION["username"] != "admin") {
                            echo "<button><a href=\"logout.php\" class=\"logout\">Logout</a></button>";
                            echo "<form action=\"\" method=\"post\">
                                <button name=\"deleteUser\">Delete Account</button>
                                <br>
                                </form>";
                                echo "<form action=\"\" method=\"post\">
                                <label for=\"newName\">Change Name: </label>
                                <input type=\"text\" name=\"newName\">
                                </form>";
                            if (isset($_POST["deleteUser"]) && empty($_POST["newName"])) {
                                $x->deleteUser($_GET["u"]);
                            } else if (isset($_POST["newName"]) & empty($_POST["deleteUser"])) {
                                $x->changeUserName($_SESSION["username"], $_POST["newName"]);
                                $_SESSION["username"] = $_POST["newName"];
                            }
                        } 
                    } else {
                        if ($_GET["u"] != "admin" && $_SESSION["username"] == "admin") {
                            echo "<form action=\"\" method=\"post\">
                                <button name=\"deleteUser\">Delete User</button>
                            </form>";

                            if (isset($_POST["deleteUser"])) {
                                $x->deleteUser($_GET["u"]);
                                echo "<script>window.location.href = './account.php?u=admin;</script>";
                            }
                        }
                    }
                    ?>
                    <p id="userInfo">
                    <?php
                    if ($_GET["u"] != "admin") {
                        echo $x->countSong($_GET["u"]);
                        echo $x->countPlaylists($_GET["u"]);
                    }
                    ?>
                    </p>
                </div>
                <div id="displayChoice">
                    <?php /*echo "form method=\"post\">
                        <button class=\"btn\" name=\"PublishedSongs\">Songs</button>
                        <button class=\"btn\" name=\"PublicPlaylists\">Public Playlists</button>
                    </form>";*/
                    $u = $_GET["u"];
                    if ($u != "admin") {
                        echo "<a class=\"button\" href=\"account.php?u=$u&songs\" hx-push-url=\"account.php?u=$u&songs\" hx-trigger=\"click\" hx-get=\"./htmx/testAcc.php?u=$u&songs\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\">Songs</a>";
                        echo "<a class=\"button\" href=\"account.php?u=$u&playlists\" hx-push-url=\"account.php?u=$u&playlists\" hx-trigger=\"click\" hx-get=\"./htmx/testAcc.php?u=$u&playlists\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\">Public Playlists</a>";
                    } else {
                        echo "<a class=\"button\" href=\"logout.php\">Logout</a>";
                    }
                    ?>
                </div>
            </div>

            <div id="resultContainer">
                <?php
                    if (isset($_GET["playlists"])) {
                        $u = $_GET["u"];
                        echo "<table id=\"userPublicPlaylists\">
                        <thead>
                            <tr>
                                <th id=\"headerTitle\">$u's Public Playlists</th>
                            </tr>
                        </thead>
                        <tbody>";
                            $x->displayPublicPlaylists();
                        echo "</tbody>
                    </table>";
                    } else if (isset($_GET["songs"])) {
                        echo "<div id=\"userSongTable\">";
                            $x->songDisplayHtml();
                        echo "</div>";
                    }
            ?>
        </div>
        </section>
        <?php
            require_once("./site_parts/rightTab.php");
            require_once("./site_parts/bottomTab.html");
        ?>

</body>
</html>