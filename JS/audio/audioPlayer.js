var audio = null;
var timer = null;
var lastKnownTime = "00:00"; 

let playBtns = document.querySelectorAll(".play");
let currentIndex = -1;
let pauseBtn = document.getElementById("pauseBtn");
let seekSlider = document.getElementById("seekSlider");
let vol = document.getElementById("vol");
let previous = document.getElementById("previous");
let next = document.getElementById("next");
let songNameElement = document.getElementById("songName");
let songImageElement = document.getElementById("songImage");

if (audio) {
    audio.pause();
    audio = null;
}

if (timer) {
    timer.stop();
    timer = null;
}

audio = new Audio();
timer = new easytimer.Timer();

function playSong(index) {
    if (currentIndex !== -1 && currentIndex !== index) {
        audio.pause();
        timer.stop();
        timer.reset();
    }

    playBtns = document.querySelectorAll(".play");
    let filePath = playBtns[index].value;
    audio.src = filePath;
    audio.play();
    currentIndex = index;

    let songName = playBtns[index].querySelector(".jsName").value;
    let songImagePath = playBtns[index].querySelector(".jsImage").value;

    songNameElement.textContent = "Currently playing: " + songName;
    songImageElement.src = songImagePath;
    songImageElement.style.display = "block";

    timer.stop();
    timer.start({ precision: 'secondTenths', startValues: { seconds: audio.currentTime } });
}

function playNextSong() {
    currentIndex++;
    if (currentIndex < playBtns.length) {
        playSong(currentIndex);
    } else {
        audio.pause();
        currentIndex = -1;
    }
}

function playPreviousSong() {
    if (currentIndex > 0) {
        currentIndex--;
        playSong(currentIndex);
    }
}

audio.addEventListener("ended", function() {
    playNextSong();
    timer.stop();
    timer.reset();
});

function pauseAudio() {
    audio.pause();
    timer.pause();
    lastKnownTime = document.getElementById('timer').textContent; 
    console.log("Paused audio. Last known time:", lastKnownTime);
}

function audioVolume() {
    audio.volume = parseFloat(vol.value);
}

function updateSeekSlider() {
    if (!seekSlider.dragging) {
        let newPosition = (audio.currentTime / audio.duration) * 100;
        seekSlider.value = newPosition;
    }
}

function seekAudio() {
    let newPosition = audio.duration * (seekSlider.value / 100);
    audio.currentTime = newPosition;
    lastKnownTime = formatTime(newPosition); 
    document.getElementById('timer').textContent = lastKnownTime; 
    console.log("Seek audio. New position:", newPosition, "Formatted:", lastKnownTime);


    if (!audio.paused) {
        timer.stop();
        timer.start({ precision: 'secondTenths', startValues: { seconds: newPosition } });
    } else {
        timer.stop();
    }
}

function resumeAudio() {
    audio.play();
    timer.start({ precision: 'secondTenths', startValues: { seconds: audio.currentTime } });
    console.log("Resumed audio. Current time:", audio.currentTime);
}

function formatTime(seconds) {
    let minutes = Math.floor(seconds / 60);
    let remainingSeconds = Math.floor(seconds % 60);
    return minutes.toString().padStart(2, '0') + ':' + remainingSeconds.toString().padStart(2, '0');
}

timer.addEventListener('secondsUpdated', function(e) {
    document.getElementById('timer').textContent = timer.getTimeValues().toString(['minutes', 'seconds']);
});

timer.addEventListener('started', function(e) {
    document.getElementById('timer').textContent = timer.getTimeValues().toString(['minutes', 'seconds']);
});

timer.addEventListener('reset', function(e) {
    let timerElement = document.getElementById('timer');
    if (!timerElement.textContent || timerElement.textContent.trim() === "") {
        timerElement.textContent = lastKnownTime; 
        console.log("Timer reset. Setting last known time:", lastKnownTime);
    } else {
        timerElement.textContent = timer.getTimeValues().toString(['minutes', 'seconds']);
    }
});

document.getElementById('playBtn').addEventListener('click', function() {
    if (audio.paused) {
        if (audio.src) {
            resumeAudio();
        } else {
            playSong(0);
        }
    }
});

pauseBtn.addEventListener('click', pauseAudio);

seekSlider.addEventListener("input", seekAudio);
seekSlider.addEventListener("mousedown", function() { seekSlider.dragging = true; });
seekSlider.addEventListener("mouseup", function() { seekSlider.dragging = false; seekAudio(); });
vol.addEventListener("input", audioVolume);
audio.addEventListener("timeupdate", updateSeekSlider);
next.addEventListener("click", playNextSong);
previous.addEventListener("dblclick", playPreviousSong);

playBtns.forEach(function(playBtn, index) {
    playBtn.addEventListener("click", function() {
        playSong(index);
    });
});

document.addEventListener("htmx:afterSwap", (event) => {
    let playBtns = document.querySelectorAll(".play");
    playBtns.forEach(function(playBtn, index) {
        playBtn.addEventListener("click", function() {
            playSong(index);
        });
    });
});
