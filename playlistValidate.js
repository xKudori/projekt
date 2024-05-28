function validatePlaylistData() {
    const playlistName = document.querySelector("input[name='playlistName']").value.trim();
    const playlistType = document.querySelector("input[name='playlistType']:checked");

    if (!playlistName) {
        alert("Please input the playlist name.");
        return false;
    }

    if (!playlistType) {
        alert("Please select the playlist type.");
        return false;
    }

    return true;
}