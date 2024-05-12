<?php
    session_start();
    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
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
            <div id="displayPlaylistName">
                <?php
                    $x->playlistNameDisplayHtml();
                    if (isset($_GET["x"]) && $x->isUserPlaylist($_GET["x"]) == false && $x->isPlaylistLikedByUser($_GET["x"]) == false) {
                        $pId = $_GET["x"];
                echo"
                <form method=\"post\">
                    <button name=\"likePlaylist\" value=\"$pId\">Like</button>
                </form>";
                if (isset($_POST["likePlaylist"])) {
                    $x->likePlaylist($pId);
                }

                }
                ?>
                <?php
                    /*$pId = $_GET["x"]; 
                    $x->likePlaylist($pId);*/
                ?>
            </div>
            <div class="displaySongs">
                <form action="" method="post">
                    <?php                   
                    $tempVal=1;  
                        $x->songDisplayHtml();
                    ?>
                </form>
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