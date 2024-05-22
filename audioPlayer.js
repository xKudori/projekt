if (!window.playBtns) {
    let play = document.getElementById("playBtn");
    let playBtns = document.querySelectorAll(".play");
    let audioNames = document.querySelectorAll(".jsName");
    let pauseBtn = document.getElementById("pauseBtn");
    let seekSlider = document.getElementById("seekSlider");
    let vol = document.getElementById("vol");
    let audio = new Audio();
    let currentIndex = -1;
    let previous = document.getElementById("previous");
    let next = document.getElementById("next");
    var timer = new easytimer.Timer();
    //var timer = new Timer();

    let songNameElement = document.getElementById("songName");
    let songImageElement = document.getElementById("songImage");


    document.addEventListener("DOMContentLoaded", function() {
        const volIcon = document.getElementById("volIcon");
        const volSlider = document.getElementById("vol");
    
        volIcon.addEventListener("click", function() {
            if (volSlider.style.visibility === "hidden" || volSlider.style.visibility === "") {
                volSlider.style.visibility = "visible";
            } else {
                volSlider.style.visibility = "hidden";
            }
        });
    });

    function updatePlayBtns() {
        playBtns = document.querySelectorAll(".play");
    }

    function playSong(index) {
        let filePath = playBtns[index].value;
        audio.src = filePath;
        audio.play();
        currentIndex = index;
        
        let songName = playBtns[index].querySelector(".jsName").value;
        let songImagePath = playBtns[index].querySelector(".jsImage").value;

        songNameElement.textContent = songName;
        songImageElement.src = songImagePath;
        songImageElement.style.display = "block";
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
        timer.reset();
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

    function resumeAudio() {
        audio.play();
        timer.start();
    }
    

    timer.addEventListener('secondsUpdated', function (e) {
        $('#bottomTab #timer').html(timer.getTimeValues().toString(['minutes', 'seconds']));
    });

    timer.addEventListener('started', function (e) {
        $('#bottomTab #timer').html(timer.getTimeValues().toString('minutes', 'seconds'));
    });

    timer.addEventListener('reset', function (e) {
        $('#bottomTab #timer').html(timer.getTimeValues().toString(['minutes', 'seconds']));
    });

    $('#bottomTab #playBtn').click(function () {
        timer.start();
    });

    $('#bottomTab #pauseBtn').click(function () {
        timer.pause();
    });

    $('#bottomTab #previous').click(function () {
        timer.reset();
    });

    $('#bottomTab #playBtn').click(function() {
        if (audio.paused) {
            if (audio.src) {
                resumeAudio();
            } else {
                playSong(0);
            }
        }
    });


    seekSlider.addEventListener("input", seekAudio)
    playBtns.forEach(function(playBtn, index) {
        updatePlayBtns();
        playBtn.addEventListener("click", function() {
            playSong(index);
                timer.reset();
                timer.start();
        });
    });

    function showHelp(element) {
        element.classList.add("visible");
    }
    
    function hideHelp(element) {
        element.classList.remove("visible");
    }

    pauseBtn.addEventListener("click",pauseAudio);
    vol.addEventListener("input", audioVolume);
    audio.addEventListener("timeupdate", updateSeekSlider);
    next.addEventListener("click", function() {
        playNextSong();
    });
    previous.addEventListener("dblclick",function() {
        playPreviousSong();
    })
}
