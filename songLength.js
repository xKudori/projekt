document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.songLength').forEach(function(element) {
        var audioPath = element.getAttribute('data-audio');
        var audio = new Audio(audioPath);
        audio.addEventListener('loadedmetadata', function() {
            var duration = audio.duration;
            var minutes = Math.floor(duration / 60);
            var seconds = Math.floor(duration % 60).toString().padStart(2, '0');
            element.textContent = minutes + ':' + seconds;
        });
    });
});