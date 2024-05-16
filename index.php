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
    <link rel="icon" type="image/x-icon" href="moon3.png">
    <title>LunaChord</title>
    <script src="https://unpkg.com/htmx.org@1.7.0/dist/htmx.min.js"></script>
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
            <div class="displaySongs">
            <?php $x->playlistNameDisplayHtml(); ?>
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

</body>
</html>