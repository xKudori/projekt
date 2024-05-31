function validateUserSongForm() {
    const audioFile = document.getElementById("audio-file").files;
    const songName = document.querySelector("input[name='songName']").value.trim();

    if (audioFile.length === 0) {
        alert("Please select audio file.");
        return false;
    }

    if (!songName) {
        alert("Please enter the song name.");
        return false;
    }

    return true;
}