<section id="rightTab">
    <div id="playlistSelectionTitle">Playlist Selection</div>
    <div class="userPlaylists">
        Your playlists
        <?php /*
            <form method="post">
                <button id="sortButton" name="sort">(Sort)</button>
            </form>
        */ ?>
    </div>
    <div id="playlistSelection">
        <?php
            $x->displayPlaylistsHtml();
        ?>
    </div>
</section> 
