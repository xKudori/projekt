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
        <section id="leftTab">
            <div id="fInputTitle">Select File</div>
            <div id="fInput">
                <form action="" method="post" id="f2" enctype="multipart/form-data">
                    <?php 
                        $x->formDisplay(); 
                    ?>
                </form>
            </div>
            <?php
                $x->getSongData();
            ?>
            <div id="createPlaylistTitle">Create Playlist</div>
            <div id="createPlaylist">
                <form action="" method="post" id="f1" enctype="multipart/form-data">
                    <label for="playlistName">Playlist name: </label>
                    <input type="text" name="playlistName">
                    <br>
                    <br>
                    <!--<label for="cover-art">Cover art: </label>
                    <input type="file" name="cover-art">-
                    <br>
                    <br>-->
                    <label>Playlist type: </label>
                    <br>
                    <div class="type">
                        <label for="Public">Public</label>
                        <div class="Help">(?)
                            <span class="helpText">A public playlist will be visible to everyone</span>
                        </div>
                        <input type="radio" name="playlistType" value="Public">
                    </div>
                    <div class="type">
                        <label for="Private">Private</label>
                        <div class="Help">(?)
                            <span class="helpText">A private playlist will only be visible to you and can only by accessed by you</span>
                        </div>
                        <input type="radio" name="playlistType" value="Private">
                    </div>
                    <div class="type">
                        <label for="Local">Local</label>
                        <div class="Help">(?)
                            <span class="helpText">
                                A local playlist is where you can store your <br>
                                 own imported audio files from your device. <br>
                                It will not be accessible to anyone and cannot be shared.
                            </span>
                        </div>
                        <input type="radio" name="playlistType" value="Local">
                    </div>
                    <br>
                    <button name="Create">Create</button>
                </form>
                <?php
                    $x->getPlaylistData();
                ?> 
            </div>
        </section>         
        <section id="middleTab">
            <div id="top">
                <div id="homeContainer">
                    Home
                </div>
                <a href="./account.php?u=<?=$_SESSION["username"]?>" id="accountContainer">
                    Account
                </a>
                <div id="searchContainer">
                <form method="post">
                        <label for="searchQuery">Search</label>
                        <input type="text" name="searchQuery">
                    </form>
                    <?php
                        if (isset($_POST["searchQuery"])) {
                            $s = $_POST["searchQuery"];
                            echo "<script>window.location.href = './search.php?query=$s';</script>"; 
                        }
                    ?>
                </div>
            </div> 
            <div id="displayPlaylistName">
                <?php
                    $x->playlistNameDisplayHtml();
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
        <section id="rightTab">
            <div id="playlistSelectionTitle">Playlist Selection</div>
                <div class="userPlaylists">
                    Your playlists
                    <form method="post">
                        <button id="sortButton" name="sort">(Sort)</button>
                    </form>
                </div>
            <div id="playlistSelection">
                <?php
                    $x->displayPlaylistsHtml();
                ?>
                <div class="userPlaylists">
                    Liked playlists
                </div>
            </div>

        </section>
        <section id="bottomTab">
            <button id="previous">&#9666;</button>
            <button id="playBtn">&#9658;</button>
            <button id="pauseBtn">&#10074;&#10074;</button>
            <button id="next">&#9656;</button>
            <h2 id="timer">00:00</h2>
            <input type="range" min="0" max="100" step="1" id="seekSlider" value="0">
            <h2>  &#128266;  </h2>
            <input type="range" min="0" max="1" step="0.01" id="vol" value="0.2">
        </section>
    <script>
        let playBtns = document.querySelectorAll(".play");
        let pauseBtn = document.getElementById("pauseBtn");
        let seekSlider = document.getElementById("seekSlider");
        let vol = document.getElementById("vol");
        let audio = new Audio();
        let currentIndex = -1;
        let previous = document.getElementById("previous");
        let next = document.getElementById("next");

 
        function playSong(index) {
            let filePath = playBtns[index].value;
            audio.src = filePath;
            audio.play();
            currentIndex = index;
            startTimerOnPlay();
        }

        function playNextSong() {
            currentIndex++;
            if (currentIndex < playBtns.length) {
                playSong(currentIndex);
            } else {
                audio.pause();
            }
        }

        function playPreviousSong() {
            currentIndex--;
            playSong(currentIndex);
        }

        audio.addEventListener("ended", function() {
            playNextSong();
        });


        function getCurrentIndex() {
            for (let i = 0; i < playBtns.length; i++) {
                if (audio.src === playBtns[i].value) {
                    return i;
                }
            }
            return -1; 
        }

        function pauseAudio() {
            audio.pause();
        }
        function audioVolume() {
            audio.volume = parseFloat(vol.value);
        }
        function updateSeekSlider(){
            let newPosition = (audio.currentTime / audio.duration) * 100;
            seekSlider.value = newPosition;
        }
        function seekAudio() {
            let newPosition = audio.duration * (seekSlider.value / 100);
            audio.currentTime = newPosition;
        }

        function startTimer(duration) {
            var timer = 0, minutes, seconds;
            var interval = setInterval(function () {
            minutes = Math.floor(timer / 60);
            seconds = timer % 60;

            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            document.getElementById('timer').textContent = minutes + ':' + seconds;

            timer++;

            if (timer > duration) {
                clearInterval(interval);
            }
        }, 1000);
        }

        function startTimerOnPlay() {
            var duration = audio.duration;
            startTimer(duration);
        }

        function stopTimer() {
            clearInterval(timerInterval);
        }

        audio.addEventListener('play', startTimerOnPlay);

        pauseBtn.addEventListener('click', stopTimer);

        seekSlider.addEventListener("input", seekAudio)
        playBtns.forEach(function(playBtn, index) {
            playBtn.addEventListener("click", function() {
                playSong(index);
            });
        });
        
        pauseBtn.addEventListener("click",pauseAudio);
        vol.addEventListener("input", audioVolume);
        audio.addEventListener("timeupdate", updateSeekSlider);
        next.addEventListener("click", function() {
            playNextSong();
        });
        previous.addEventListener("click",function() {
            audio.currentTime = 0;
        })
        previous.addEventListener("dblclick",function() {
            playPreviousSong();
        })
    </script>
</body>
</html>