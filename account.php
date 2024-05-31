<?php
    session_start();
    require("./db.php");
    $displayObj = new HTML_Display_Functions("localhost","music_site","root","");
    $dataObj = new SQL_Functions("localhost","music_site","root","");
    
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    $user = $_SESSION['username'];
    $u = $_GET["u"];

    if (isset($_POST["deleteUser"]))  {
        $dataObj->deleteUser($_GET["u"]);
        header("Location: ./account.php?u=admin");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,s initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="./images/misc/moon3.png">
    <title>LunaChord</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/htmx.org@1.7.0/dist/htmx.min.js"></script>
    <script src="./JS/easyTimer/easytimer.js"></script>
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
                <div id="displayPfp">
                    <img src="<?=$dataObj->getUserProfilePicture($u)?>" alt="./images/userIamages/default-user-icon.png" id="userImage">
                </div>
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
                            echo "<button onclick=\"confirmDelete()\">Delete Account</button>";
                        } 
                    } else {
                        if ($_GET["u"] != "admin" && $_SESSION["username"] == "admin") {
                            echo "<form action=\"\" method=\"post\">
                            <button name=\"deleteUser\">Delete User</button>
                            </form>";
                        
                        }
                    }
                    ?>
                    <p id="userInfo">
                    <?php
                    if ($_GET["u"] != "admin") {
                        echo $displayObj->countSong($_GET["u"]);
                        echo $displayObj->countPlaylists($_GET["u"]);
                    }
                    ?>
                    </p>
                </div>
                <div id="displayChoice">
                    <?php 
                    $u = $_GET["u"];
                    if ($u != "admin") {
                        echo "<a class=\"button\" href=\"account.php?u=$u&songs\" hx-push-url=\"account.php?u=$u&songs\" hx-trigger=\"click\" hx-get=\"./htmx/accResult.php?u=$u&songs\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\">Songs</a>";
                        echo "<a class=\"button\" href=\"account.php?u=$u&playlists\" hx-push-url=\"account.php?u=$u&playlists\" hx-trigger=\"click\" hx-get=\"./htmx/accResult.php?u=$u&playlists\" hx-target=\"#resultContainer\" hx-swap=\"innerHTML\">Public Playlists</a>";
                    } else {
                        echo "<a class=\"button\" href=\"logout.php\">Logout</a>";
                    }
                    ?>
                </div>
            </div>
            <div id="scroll">
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
                            $displayObj->displayPublicPlaylists();
                        echo "</tbody>
                    </table>";
                    } else if (isset($_GET["songs"])) {
                        echo "<div id=\"userSongTable\">";
                            $displayObj->songDisplayHtml();
                        echo "</div>";
                    }
            ?>
        </div>
        </div>
        </section>
        <?php
            require_once("./site_parts/rightTab.php");
            require_once("./site_parts/bottomTab.html");
        ?>
        <script>
            function confirmDelete() {
                if (confirm("Are you sure you want to delete your account?")) {
                    window.location.href = "account_del.php";
                }
            }
        </script>
</body>
</html>