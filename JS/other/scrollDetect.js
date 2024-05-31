var scrollElement = document.getElementById('middleTab');
var songScrollElement = document.querySelector('.displaySongs');


if (!scrollElement && songScrollElement) {
    scrollElement = songScrollElement;
} else if (!scrollElement) {
    scrollElement = document.getElementById('middleTab');
}


if (scrollElement) {
    scrollElement.addEventListener('scroll', function() {
        var scrollDistance = scrollElement.scrollTop;
        var translateY = scrollDistance * -1; 
        console.log("triggered");
        document.documentElement.style.setProperty('--translateY', translateY + 'px');
    });
}
