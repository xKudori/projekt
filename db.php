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

    private function song() {
        if (isset($_GET["x"])) 
        {
            $sId = $_GET["x"];
            $sql = $this->pdo->prepare("SELECT * FROM songs WHERE playlist_id=$sId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
        } else {
            $sql = $this->pdo->prepare("SELECT * FROM songs WHERE playlist_id=1");
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
        echo "<th>ID</th>";
        echo "<th>Name</th>";
        echo "<th>Artist</th>";
        echo "<th>Length</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
    
        foreach ($songs as $song) {
            global $a;
            $a = $a + 1;
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<button name=\"play\" class=\"play\" value=\"$song->song_name\">". $a ."</button>" ."</td>";
            echo "<td>" . $song->song_name . "</td>";
            echo "<td>" . $song->artist . "</td>";
            echo "<td>" . $song->length . "</td>";
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

    public function displayPlaylistsHtml() 
    {
        $playlist = $this->playlists();
        
        echo "<div class=\"playlistsContainer\">";
        echo "<table class=\"playlistTable\">";
        echo "<thead>";
        echo "</thead>";
        echo "<tbody>";
    
        foreach ($playlist as $p) {
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" class=\"click\">" . $p->playlist_name . "<div class=\"pTypeDisplay\">$p->playlistType Playlist</div>" . "</a>" . "</td>";
            echo "</tr>";
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
        if (isset($_POST["playlistName"]) && isset($_POST["playlistType"])) 
        {
            
            $playlist_name = $_POST["playlistName"];
            $playlistType = $_POST["playlistType"];
            $sql = $this->pdo->prepare("INSERT INTO playlists (playlist_name, playlistType) VALUES (:playlist_name, :playlistType)");
            $sql->bindParam(":playlist_name", $playlist_name, PDO::PARAM_STR);
            $sql->bindParam(":playlistType", $playlistType, PDO::PARAM_STR);
            $sql->execute();
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
            if (isset($_POST["songName"]) && isset($_POST["insertPlaylist"])) 
            {
                $p = $this->getPlaylistType();
                foreach ($p as $b) {
                    $b->playlistType;
                }
                $temp = $b->playlistType;
                if ($temp == "Local") {
                    $songName = $_POST["songName"];
                    $insertPlaylist = $_POST["insertPlaylist"];
                    $playlist_id = $this->getPIDFromPlaylists();
                    foreach($playlist_id as $a) {
                        $a->playlist_id;
                    }
                    
                    $audioFileTmpName = $_FILES["audio-file"]["tmp_name"];


                    $audioFileName = "./audio/".$_FILES["audio-file"]["name"];
                    move_uploaded_file($audioFileTmpName, $audioFileName);
                    $sql = $this->pdo->prepare("INSERT INTO songs (song_name, playlist_name, playlist_id, audio_path) VALUES (:song_name, :playlist_name, :playlist_id, :audio_path)");
                    $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
                    $sql->bindParam(":playlist_name", $insertPlaylist, PDO::PARAM_STR);
                    $sql->bindParam(":playlist_id", $a->playlist_id, PDO::PARAM_INT);
                    $sql->bindParam(":audio_path", $audioFileName, PDO::PARAM_STR);
                    $sql->execute();

            } else {
                echo "<div id=\"ERR\">Error</div>";
            }
        }
        }
    
    private function getLocalPlaylists() 
    {
        $sql = $this->pdo->prepare("SELECT playlist_name, playlistType FROM playlists WHERE playlistType =" . "\""."Local"."\"");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
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
}


    
?>