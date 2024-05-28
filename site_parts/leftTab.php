<section id="leftTab">
    <?php
        if (isset($_GET["x"])) {
            $temp = $_GET["x"];
        }
        if (isset($_GET["u"])) {
            $temp = $_GET["u"];
        }
        if (isset($_GET["query"])) {
            $temp = $_GET["query"];
        }
                        
        //$id=$_GET["x"];

    ?>
            <div id="fInputTitle">Select File</div>
            <div id="fInput">
            <div id="SongUpload">


                <form action="" hx-post="./htmx/songInput.php" hx-trigger="click" hx-target="#SongUpload" hx-swap="innerHTML" method="post" class="f2" enctype="multipart/form-data">
                        <button name="btn1" class="button">Upload Song</button>
                        <?php
                        //echo "<input type=\"hidden\" name=\"id\" value=\"$id\"></input>";
                        ?>
                </form>
                <br>
                <form action="" hx-post="./htmx/localInput.php" hx-trigger="click" hx-target="#SongUpload" hx-swap="innerHTML" method="post" class="f2" enctype="multipart/form-data">
                        <button name="btn2" class="button">Upload files to Local Playlist</button>
                        <?php
                        //echo "<input type=\"hidden\" name=\"id\" value=\"$id\"></input>";
                        ?>
                </form>
            </div>
            </div>
            <?php
                $x->getSongData();
            ?>
            <div id="createPlaylistTitle">Create Playlist</div>
            <div id="createPlaylist">
                <form action="" method="post" id="f1" enctype="multipart/form-data" onsubmit="return validatePlaylistData()">
                    <label for="playlistName">Playlist name: </label>
                    <input type="text" name="playlistName">
                    <br>
                    <br>
                    <label>Playlist type: </label>
                    <br>
                    <div class="type">
                        <label for="Public">Public</label>
                        <div class="questionMark">(?)
                            <span class="helpText">A public playlist will be visible to everyone</span>
                        </div>
                        <input type="radio" name="playlistType" value="Public">
                    </div>
                    <div class="type">
                        <label for="Private">Private</label>
                        <div class="questionMark">(?)
                            <span class="helpText">A private playlist will only be visible to you and can only by accessed by you</span>
                        </div>
                        <input type="radio" name="playlistType" value="Private">
                    </div>
                    <div class="type">
                        <label for="Local">Local</label>
                        <div class="questionMark">(?)
                            <span class="helpText">
                                A local playlist is where you can store your 
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
        <script src="./JS/validation/playlistValidate.js"></script>