function validateForm() {
    const audioFile = document.getElementById("audio-file").files;
    const imageFile = document.getElementById("img").files;
    const songName = document.querySelector("input[name='songName']").value.trim();
    const playlistRadios = document.querySelectorAll("input[name='insertPlaylist']");

    

    if (audioFile.length === 0 || imageFile.length === 0) {
        alert("Please select both audio and image files.");
        return false;
    }


    if (!songName) {
        alert("Please enter the song name.");
        return false;
    }


    let playlistSelected = false;
    for (const radio of playlistRadios) {
        if (radio.checked) {
            playlistSelected = true;
            break;
        }
    }
    if (!playlistSelected) {
        alert("Please select a playlist.");
        return false;
    }

    return true;
}
