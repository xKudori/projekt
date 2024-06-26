<?php
    session_start();
    require("./db.php");

    $displayObj = new HTML_Display_Functions("localhost","music_site","root","");
    $dataObj = new SQL_Functions("localhost","music_site","root","");
    
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
            <div class="displaySongs">
            <?php $displayObj->playlistNameDisplayHtml(); 
                        if ((isset($_GET["x"]) && $dataObj->isUserPlaylist($_GET["x"]) == false && $dataObj->isPlaylistLikedByUser($_GET["x"]) == false ) && $dataObj->isPlaylistPublic($_GET["x"]) == true)  {
                $pId = $_GET["x"];
            echo"
            <form method=\"post\">
                <button name=\"likePlaylist\" value=\"$pId\" id=\"like\">Like</button>
            </form>";
            if (isset($_POST["likePlaylist"])) {
            $dataObj->likePlaylist($pId);
            echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
            }

            }
            ?>
                <form action="" method="post">
                    <?php                   
                    $tempVal=1;  
                        $displayObj->songDisplayHtml();
                    ?>
                </form>
            </div>
        </section>
        <?php
            require_once("./site_parts/rightTab.php");
            require_once("./site_parts/bottomTab.html");
        ?>
        

</body>
</html>