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

    public function song() {
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
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . $song->song_id . "</td>";
            echo "<td>" . $song->song_name . "</td>";
            echo "<td>" . $song->artist . "</td>";
            echo "<td>" . $song->length . "</td>";
            echo "</tr>";
        }  
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
    
    public function playlist_name() 
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

    public function playlists() 
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
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" class=\"click\">" . $p->playlist_name . "</a>" . "</td>";
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
            if (isset($_GET["x"])) {
                header("Location: index.php?x=".$_GET["x"]);
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }

    }
    
?>