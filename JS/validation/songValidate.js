function validateForm() {
    const audioFile = document.getElementById("audio-file").files;
    const imageFile = document.getElementById("img").files;
    const songName = document.querySelector("input[name='songName']").value.trim();

    if (audioFile.length === 0 || imageFile.length === 0) {
        alert("Please select both audio and image files.");
        return false;
    }

    if (!songName) {
        alert("Please enter the song name.");
        return false;
    }

    return true;
}