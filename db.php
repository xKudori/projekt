<?php

    require ("./songs.php");
    require ("./playlists.php");
    class Db_Connection {
    private ?PDO $pdo;

    public $adres;
    public $nazwa;
    public $haslo;
    public $nazwa_bazy;


    public function __construct($adres,$nazwa_bazy,$nazwa,$haslo) {
        $this->pdo = new PDO("mysql:host=$adres;dbname=$nazwa_bazy", $nazwa, $haslo);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    public function __destruct() {
        $this->pdo = null;
    }


    private function getSongIdFromPlaylistSongs() {
        if (isset($_GET["x"])) 
        {
            $pId = $_GET["x"];
            $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id=$pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
        } 
    } 
    private function song() {
        if (isset($_GET["x"])) {
            $sIds = $this->getSongIdFromPlaylistSongs();
    
            $songs = array();
    
            foreach ($sIds as $sId) {
                $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=:sId");
                $sql->bindParam(":sId", $sId->song_id, PDO::PARAM_INT);
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_CLASS, "Songs");
                $songs[] = $sql->fetch();
            }
    
            return $songs;
        } else {
            $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=1");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
        }
    }

    public function songDisplayHtml() 
    {
        $songs = $this->song();
        $a = 0;
        echo "<div class=\"songsContainer\">";
        echo "<table class=\"songTable\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th></th>";
        echo "<th>Name</th>";
        echo "<th>Artist</th>";
        echo "<th>Length</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
    
        foreach ($songs as $song) {
            global $a;
            $a = $a + 1;
            $songId = $song->song_id;
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<button name=\"play\" class=\"play\" value=\"$song->song_name\">". $a .".</button>" ."</td>";
            echo "<td>" . $song->song_name . "</td>";
            echo "<td>" . $song->artist . "</td>";
            echo "<td>" . $song->length . "</td>";
            echo "<td>" . "<div class=\"Help\">" . "..." . "<span class=\"helpText\">"; 
            //echo $this->changeLocalPlaylists(); 
            $playlist = $this->getNewLocalPlaylists();
            
            echo "<div class=\"playlistsContainer\">";
            echo "<table class=\"playlistTable\">";
            echo "<thead>";
            echo "</thead>";
            echo "<tbody>";
        
            foreach ($playlist as $p) {
                //if ($p->playlist_name == )
                echo "<tr class=\"s1\">";
                echo "<form method=\"post\">";
                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">$p->playlist_name</button>" . "</td>";
                //echo "<input type=\"hidden\" name=\"hiddenPlaylistId\" value=\"$p->playlist_id\"></input>";
                //echo "<input type=\"hidden\" name=\"hiddenSongId\" value=\"$song->song_id\"></input>";
                echo "</form>";
                echo "</tr>";
            }
            echo "<tr class=\"s1\">";
            echo "<td>Delete Song</d>"; 
            echo "</tr>";
            echo "<tr class=\"s1\">";
            echo "<td>Change Name</d>"; 
            echo "</tr>";
            if (isset($_POST["insertIntoPlaylist"])) {
                $playlistId = $this->getPlaylistIdByName($_POST["insertIntoPlaylist"]);
                $this->addSongToPlaylist($songId,$playlistId);
            }
            echo "</tbody>";
            echo "</table>";
        
            echo "</div>";
            //insertPlaylistChange();
            echo "</span>" . "</div>". "</td>";
            echo "</tr>";
        }  
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
    
    private function playlist_name() 
    {
        if (isset($_GET["x"])) 
        {
            $pId = $_GET["x"];
            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=$pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
            return $sql->fetchAll();
        } else {
            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=1");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
            return $sql->fetchAll();
        }
    }

    public function playlistNameDisplayHtml() 
    {
        $pname = $this->playlist_name();
        foreach ($pname as $playlistName) {    
            echo "<div class=\"pName\">" . $playlistName->playlist_name . "</div>";     
        }
    }

    private function playlists() 
    {
        $sql = $this->pdo->prepare("SELECT * FROM playlists");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function sortedPlaylists() {
        $sql = $this->pdo->prepare("SELECT * FROM playlists ORDER BY playlistType");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    public function displayPlaylistsHtml() 
    {
        $playlist = $this->playlists();
        $sortedPlaylist = $this->sortedPlaylists();
        echo "<div class=\"playlistsContainer\">";
        echo "<table class=\"playlistTable\">";
        echo "<thead>";
        echo "</thead>";
        echo "<tbody>";

        if (isset($_POST["sort"])) {
        foreach ($sortedPlaylist as $p) {
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" class=\"click\">" . $p->playlist_name . "<div class=\"pTypeDisplay\">$p->playlistType Playlist</div>" . "</a>" . "</td>";
            echo "</tr>";
        }
        } else {
        foreach ($playlist as $p) {
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" class=\"click\">" . $p->playlist_name . "<div class=\"pTypeDisplay\">$p->playlistType Playlist</div>" . "</a>" . "</td>";
            echo "</tr>";
        }
    }
        echo "</tbody>";
        echo "</table>";
    
        echo "</div>";

}
    public function getLastRecord() 
    {
        $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id = 1");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }
    public function getPlaylistData() 
    {   
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Create"])) {
        if ((!isset($_POST["playlistName"]) || empty($_POST["playlistName"])) && !isset($_POST["playlistType"])) 
        {
            echo "<div class=\"ERR\">Please input the correct options</div>";
        } else if ((!isset($_POST["playlistName"]) || empty($_POST["playlistName"])) && isset($_POST["playlistType"])) {

            echo "<div class=\"ERR\">Please input the playlist name</div>";
        } else if (isset($_POST["playlistName"]) && !isset($_POST["playlistType"])) 
        {
            echo "<div class=\"ERR\">Please input the playlist type</div>";
        } else if (isset($_POST["playlistName"]) && isset($_POST["playlistType"])) 
        {
            
            $playlist_name = $_POST["playlistName"];
            $playlistType = $_POST["playlistType"];
            $sql = $this->pdo->prepare("INSERT INTO playlists (playlist_name, playlistType) VALUES (:playlist_name, :playlistType)");
            $sql->bindParam(":playlist_name", $playlist_name, PDO::PARAM_STR);
            $sql->bindParam(":playlistType", $playlistType, PDO::PARAM_STR);
            $sql->execute();
        }
    }
    }
    
    private function getPIDFromPlaylists() 
    {
        $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE playlist_name=" . "\"".$_POST["insertPlaylist"]."\"");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function getPlaylistType() 
    {
        $sql = $this->pdo->prepare("SELECT playlistType FROM playlists WHERE playlist_name=" . "\"".$_POST["insertPlaylist"]."\"");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    public function getSongData() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sendSong"])) {
            if ((!isset($_POST["songName"]) || empty($_POST["songName"])) && !isset($_POST["insertPlaylist"]) && empty($_FILES["audio-file"]["name"])) 
            {
                echo "<div class=\"ERR\">Please input the correct options</div>";
            } else if ((!isset($_POST["songName"]) || empty($_POST["songName"])) && isset($_POST["insertPlaylist"]) && !empty($_FILES["audio-file"]["name"])) 
            {
                echo "<div class=\"ERR\">Please input a song name</div>";
            } else if (isset($_POST["songName"]) && !isset($_POST["insertPlaylist"]) && !empty($_FILES["audio-file"]["name"])) 
            {
                echo "<div class=\"ERR\">Error: Please input a playlist.</div>";
            } else if (isset($_POST["songName"]) && isset($_POST["insertPlaylist"]) && empty($_FILES["audio-file"]["name"])) 
            {
                echo "<div class=\"ERR\">Error: Please input a file.</div>";
            } else if (isset($_POST["songName"]) && isset($_POST["insertPlaylist"]) && !empty($_FILES["audio-file"]["name"])) 
            {
                $p = $this->getPlaylistType();
                foreach ($p as $b) {
                    $b->playlistType;
                }
                $temp = $b->playlistType;
                if ($temp == "Local") {
                    $songName = $_POST["songName"];
                    $audioFileTmpName = $_FILES["audio-file"]["tmp_name"];
                    $audioFileName = "./audio/".$_FILES["audio-file"]["name"];
    
                    $sql = $this->pdo->prepare("INSERT INTO songs (song_name, audio_path) VALUES (:song_name, :audio_path)");
                    $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
                    $sql->bindParam(":audio_path", $audioFileName, PDO::PARAM_STR);
                    $sql->execute();

                    $songId = $this->pdo->lastInsertId();
    
                    $playlistId = $this->getPlaylistIdByName($_POST["insertPlaylist"]);
                    $this->addSongToPlaylist($songId, $playlistId);
                } 
            } else {
                echo "<div class=\"ERR\">Please input the correct options</div>";
            } 
        }
    }


    
    // pobiera playlist_id po nazwie playlisty
    private function getPlaylistIdByName($playlistName) {
        $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE playlist_name = :playlist_name");
        $sql->bindParam(":playlist_name", $playlistName, PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['playlist_id'];
    }
    
    // dodaje piosenkę do playlisty w tabeli playlist_songs
    private function addSongToPlaylist($songId, $playlistId) {
        $sql = $this->pdo->prepare("INSERT INTO playlist_songs (song_id, playlist_id) VALUES (:song_id, :playlist_id)");
        $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
        $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
        $sql->execute();
    }

    // pobiera tylko lokalne playlisty 
    private function getLocalPlaylists() 
    {
        $sql = $this->pdo->prepare("SELECT playlist_id,playlist_name, playlistType FROM playlists WHERE playlistType =" . "\""."Local"."\"");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    // pobiera lokalne playlisty, do których dana piosenka może być insertowana
    private function getNewLocalPlaylists() 
    {
        $pname = $this->playlist_name();
        foreach ($pname as $a) {
            $a->playlist_name;
        }
        $sql = $this->pdo->prepare("SELECT playlist_id,playlist_name, playlistType FROM playlists WHERE playlistType =" . "\""."Local"."\"" . " AND playlist_name != \"$a->playlist_name\"");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

  /*  public function changeLocalPlaylists() 
    {
        $playlist = $this->getLocalPlaylists();
        
        echo "<div class=\"playlistsContainer\">";
        echo "<table class=\"playlistTable\">";
        echo "<thead>";
        echo "</thead>";
        echo "<tbody>";
    
        foreach ($playlist as $p) {
            echo "<tr class=\"s1\">";
            echo "<form method=\"post\">";
            echo "<td class=\"songId\">"; 
            echo "<input type=\"button\" name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\"> </input>" . "</td>";
            echo "</form>";
            echo "</tr>";
        }
    
        echo "</tbody>";
        echo "</table>";
    
        echo "</div>";
        //insertPlaylistChange();
    }
*/
    // private function getSongId() {
    //     $sql = $this->pdo->prepare("SELECT song_id FROM songs WHERE playlistType =" . "\""."Local"."\"");
    //     $sql->execute();
    //     $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
    //     return $sql->fetchAll();
    // }
     private function insertPlaylistChange() 
     {
         if (isset($_POST["insertIntoPlaylist"])) {
            $sql = $this->pdo->prepare("INSERT INTO playlist_songs (song_id, playlist_id) VALUES (:song_id, :playlist_id)");
            $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
            $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
            $sql->execute();
         }
     }

   

    public function displayLocalPlaylists() 
    {
        $playlist = $this->getLocalPlaylists();
        
        echo "<div class=\"playlistsContainer\">";
        echo "<table class=\"playlistTable\">";
        echo "<thead>";
        echo "</thead>";
        echo "<tbody>";
    
        foreach ($playlist as $p) {
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<input type=\"radio\" name=\"insertPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">" . $p->playlist_name . "</input>" . "</td>";
            echo "</tr>";
        }
    
        echo "</tbody>";
        echo "</table>";
    
        echo "</div>";
    }
    private function getAudioFilePathFromDatabase() 
    {
        if (isset($_POST["play"])) {
        $songName = $_POST["play"];
        $sql = $this->pdo->prepare("SELECT audio_path FROM songs WHERE song_name = :song_name");
        $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
        $result = $sql->fetchAll();
        return $result;
        }
    }

    public function displayAudio() {
        if (isset($_POST["play"])) {
        $a = $this->getAudioFilePathFromDatabase();
        foreach ($a as $b) {
            $b->audio_path;
        }
        echo "$b->audio_path";
    }
    }



    public function formDisplay() 
    {

        if ((!isset($_POST["btn1"]) && !isset($_POST["btn2"]) && !isset($_POST["songName"]) && !isset($_POST["insertPlaylist"]) && !isset($_POST["sendSong"])) || isset($_POST["esc"])) 
        {
            echo "<button name=\"btn1\">Upload Song</button>
            <button name=\"btn2\">Upload files to Local Playlist</button>";
        }
        if((!isset($_POST["btn1"])  && isset($_POST["btn2"]) || (isset($_POST["songName"]) || isset($_POST["insertPlaylist"]) || isset($_POST["sendSong"]))) && !isset($_POST["esc"])) 
        {
            echo "<button name=\"esc\">Back</button> <br>";
            echo "<label for=\"audio-file\">Browse: </label>
            <input type=\"file\" name=\"audio-file\" id=\"audio-file\" accept=\".ogg, .flac, .mp3\">
            <br>
            <br>
            <label for=\"songName\">Name:</label>
            <input type=\"text\" name=\"songName\">
            <br>
            <br>
            <label for=\"insertPlaylist\" id=\"playlistSelect\">Select a local playlist to insert the song into</label>
            <br>
            <div class=\"Help\">List of avilable playlists
                <span class=\"helpText\">";
                echo $this->displayLocalPlaylists();
                echo "</span>
            </div>
            <br>
            <br>
            <br>
            <button name=\"sendSong\">Upload File</button>";
        }
        if ((isset($_POST["btn1"]) && !isset($_POST["btn2"])) && !isset($_POST["esc"])) {
            echo "<button name=\"esc\">Back</button> <br>"; 
        }
    }
}


    
?>