<?php
    session_start();
    require("./db.php");
    $x = new Db_Connection("localhost","music_site","root","");
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    /*
    session_destroy();
    header("Location: ./login.php");
    */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style-account.css">
    
    <title>Document</title>
</head>
<body>    
        <section id="middleTab">
            <div id="top">
                <a href="./index.php" id="homeContainer">
                    Home
                </a>
                <a href="./account.php" id="accountContainer">
                    Account
                </a>
                <div id="searchContainer">
                    Search
                </div>
            </div> 
        </section>

        </section>
        <section id="bottomTab">
            <button id="playBtn">Play</button>
            <button id="stop">Stop</button>
            <input type="range" min="0" max="1" step="0.01" id= "vol">
        </section>
    </section>
    <script>
        let playBtn = document.getElementById("playBtn");
        let pause = document.getElementById("stop");
        let vol = document.getElementById("vol");
        let audio = new Audio("<?php $x->displayAudio()?>");
        let container = document.getElementById("length");

        function playAudio() {
            audio.play();
        }
        function stopAudio() {
            audio.pause();
            audio.currentTime = 0;
        }
        function audioVolume() {
            audio.volume = parseFloat(vol.value);
        }
        playBtn.addEventListener("click", playAudio);
        pause.addEventListener("click",stopAudio);
        vol.addEventListener("input", audioVolume);
    </script>
</body>
</html>
</body>
</html>