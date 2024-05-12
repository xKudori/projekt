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