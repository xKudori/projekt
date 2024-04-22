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

    public function checkIfUserExists($username, $email) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0; 
    }

    public function loginUser($username, $password, $email) {
        $sql = $this->pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $sql->bindParam(":username", $username, PDO::PARAM_STR);
        $sql->bindParam(":email", $email, PDO::PARAM_STR);
        $sql->execute();
    
        $user = $sql->fetch(PDO::FETCH_ASSOC);
    
        if ($user ==  true) {
            if (password_verify($password, $user['password'])) {
                return true; 
            }
        } else {
            return false; 
        }
    }

    public function registerUser($username, $password, $email) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function createUserPlaylist($username) {
        $type = "User";
        $sql = "INSERT INTO playlists (playlist_name, playlistType, user) VALUES (:username, :user, :user2)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":user", $type, PDO::PARAM_STR);
        $stmt->bindParam(":user2", $username, PDO::PARAM_STR);

        $stmt->execute();
    }
    private function getSongIdFromPlaylistSongs() {
        if (isset($_GET["x"]) && !isset($_GET["t"])) 
        {
            $pId = $_GET["x"]; 
            $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id=$pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
        } else if (isset($_GET["x"]) && isset($_GET["t"])){
            $pId = $this->getPlaylistIdByName($_GET["x"]); 
            $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id = $pId");
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

    private function userSong() {
        if (isset($_GET["x"])) {
            $sIds = $this->getSongIdFromPlaylistSongs();
            $user = $_SESSION["username"];
            $songs = array();
    
            foreach ($sIds as $sId) {

                $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=:sId AND user = :user");
                $sql->bindParam(":sId", $sId->song_id, PDO::PARAM_INT);
                $sql->bindParam(":user", $user, PDO::PARAM_STR);
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_CLASS, "Songs");
                $songs[] = $sql->fetch();
            }
    
            return $songs;
        } else {
            $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=1 AND user = :user");
            $sql->bindParam(":user", $user, PDO::PARAM_STR);
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
        }
    }

    private function pTypeFromId($pId) {
            $sql = $this->pdo->prepare("SELECT playlistType FROM playlists WHERE playlist_id = $pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
    }

    public function songDisplayHtml() 
    { 
        $songs = $this->song();
        $a = 0;
        $user = $_SESSION["username"];
        if (isset($_GET["x"])) {
            $x = $_GET["x"];
            if ($x != $user) {
                $pType = $this->pTypeFromId($x);
                foreach ($pType as $pT) {
                    $pT->playlistType;
                }
        
                if ($pT->playlistType == "Local") {
                    $tempVal = "Local";
                } else {
                    $tempVal = "Public/Private";
                }    
            } else {
                $pType = "User";
                $tempVal = "User";
            }
        }

        echo "<div class=\"songsContainer\">";
        echo "<table class=\"songTable\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th></th>";
        echo "<th>Name</th>";
        echo "<th>Artist</th>";
        echo "<th>Length</th>";
        echo "<th></th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
    if (isset($pType)) {
        foreach ($songs as $song) {
            global $a;
            $a = $a + 1;
            $pId = $_GET["x"];
            $songId = $song->song_id;
            $songName = $song->song_name;
            $songPath = $song->audio_path;
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<button name=\"play\" type = \"button\"class=\"play\" value=\"$songPath\">". $a .".</button>" ."</td>";
            echo "<td>" . $song->song_name . "</td>";
            echo "<td>" . $song->artist . "</td>";
            echo "<td>" . $song->length . "</td>";
            echo "<td>" . "<div class=\"Help\">" . "..." . "<span class=\"helpText\">"; 
            $localPlaylist = $this->getNewLocalPlaylists($songId);
            $publicOrPrivatePlaylist = $this->getPublicOrPrivatePlaylist($songId);
            $userPlaylist = $this->getUserSongs($songId);
            echo "<div class=\"playlistsContainer\">";
            echo "<table class=\"playlistTable\">";
            echo "<thead>";
            echo "</thead>";
            echo "<tbody>";
        if ($tempVal == "Local") {
            foreach ($localPlaylist as $p) {

                echo "<tr class=\"s1\">";
                echo "<form method=\"post\">";
                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">$p->playlist_name</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
                echo "</form>";
                echo "</tr>";
            }
        } else if ($tempVal == "Public/Private") {
            foreach ($publicOrPrivatePlaylist as $p) {

                echo "<tr class=\"s1\">";
                echo "<form method=\"post\">";
                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">$p->playlist_name</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
                echo "</form>";
                echo "</tr>";
        }
    } else if ($tempVal == "User") {
        foreach ($userPlaylist as $p) {

            echo "<tr class=\"s1\">";
            echo "<form method=\"post\">";
            echo "<td class=\"songId\">"; 
            echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">$p->playlist_name</button>" . "</td>";
            echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
            echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
            echo "</form>";
            echo "</tr>";
            
    }
    }
            if ($tempVal == "Local" || $tempVal == "User") {
            echo "<form method=\"post\">";
                echo "<tr class=\"s1\">";
                echo "<button name=\"deleteSong\" class=\"localTitle\" value=\"$songId\">Delete Song</button>" . "</td>"; 
                echo "</tr>";
                echo "<tr class=\"s1\">";
                echo "<button name=\"removeSong\" class=\"localTitle\" value=\"$songId\">Remove From Playlist</button>" . "</td>"; 
                echo "<input type=\"hidden\" name=\"removepId\" value=\"$pId\"></input>";
                echo "</tr>";
                echo "<tr class=\"s1\">";
                echo "<input type=\"text\" name=\"changeSongName\" class=\"localTitle\" placeholder=\"Change Name\"></input>" . "</td>";
                echo "<input type=\"hidden\" name=\"changeSongId\" value=\"$songId\"></input>";
                echo "</tr>";
            echo "</form>";
            } else {
                echo "<form method=\"post\">";
                echo "<tr class=\"s1\">";
                echo "<button name=\"removeSong\" class=\"localTitle\" value=\"$songId\">Remove From Playlist</button>" . "</td>"; 
                echo "<input type=\"hidden\" name=\"changeSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"removepId\" value=\"$pId\"></input>";
                echo "</tr>";
            echo "</form>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</span>" . "</div>". "</td>";
            echo "</tr>";
        }  
    }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    
        if (isset($_POST["insertIntoPlaylist"]) || !empty($_POST["insertIntoPlaylist"]) || isset($_POST["removeSong"]) || isset($_POST["changeSongName"]))  {
        if (isset($_POST["insertIntoPlaylist"]) && !empty($_POST["insertIntoPlaylist"])) {
            $songId = $_POST["insertSongId"];
            $playlistId = $this->getPlaylistIdByName($_POST["insertIntoPlaylist"]);
            $this->addSongToPlaylist($songId,$playlistId);
            if (isset($_GET["t"])) {
                echo "<script>window.location.href = './account.php?x=$x&t=true'</script>"; //header nie dziala :(
            } else {
                echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
            }

        } if (isset($_POST["deleteSong"]) && empty($_POST["changeSongName"])) {
            $newSongId = $_POST["deleteSong"];
            $this->deleteSongFromPlaylistSongs($newSongId);
            $this->deleteSong($newSongId);
            if (isset($_GET["t"])) {
                echo "<script>window.location.href = './account.php?x=$x&t=true'</script>"; 
            } else {
                echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
            }
        } else if (isset($_POST["removeSong"]) && empty($_POST["changeSongName"])) {
            $newSongId = $_POST["removeSong"];
            $removepId = $_POST["removepId"];
            $this->deleteSongFromOnePlaylistSongs($newSongId,$removepId);
            if (isset($_GET["t"])) {
                echo "<script>window.location.href = './account.php?x=$x&t=true'</script>";
            } else {
                echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
            }
        }
        if (isset($_POST["changeSongName"]) && !empty($_POST["changeSongName"])) {
            $newSongName = $_POST["changeSongName"];
            $newSongId = $_POST["changeSongId"];
            $this->changeSongName($newSongName, $newSongId);
            if (isset($_GET["t"])) {
                echo "<script>window.location.href = './account.php?x=$x&t=true'</script>"; 
            } else {
                echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
            }
        }
        }
    }

    private function deleteSong($newSongId) {
        $sql = $this->pdo->prepare("DELETE FROM songs WHERE song_id = (:song_id)");
        $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
        $sql->execute();
    }
    private function deleteSongFromPlaylistSongs($newSongId) {
        $sql = $this->pdo->prepare("DELETE FROM playlist_songs WHERE song_id = (:song_id)");
        $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
        $sql->execute();
    } 
    
    private function deleteSongFromOnePlaylistSongs($newSongId,$removepId) {
        $sql = $this->pdo->prepare("DELETE FROM playlist_songs WHERE song_id = (:song_id) AND playlist_id = (:playlist_id)");
        $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
        $sql->bindparam(":playlist_id", $removepId, PDO::PARAM_INT);
        $sql->execute();
    } 

    private function changeSongName($newSongName, $newSongId) {
        $sql = $this->pdo->prepare("UPDATE songs SET song_name = (:new_song_name) WHERE song_ID = (:song_id)");
        $sql->bindParam(":new_song_name", $newSongName, PDO::PARAM_STR);
        $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
        $sql->execute();
    }

    public function countSong($user) {
        $sql = $this->pdo->prepare("SELECT COUNT(song_name) AS song_count FROM songs WHERE user = \"$user\"");
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return "<p>Songs Uploaded: " . $result['song_count'] . "</p>";
    }
    public function countPlaylists($user) {
        $sql = $this->pdo->prepare("SELECT COUNT(playlist_name) AS playlist_count FROM playlists WHERE user = \"$user\" AND playlistType = \"Public\"");
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return "<p>Playlists Created: " . $result['playlist_count'] . "</p>";
    }

    // Zobaczyc co mozna zrobic z playlist

    private function playlist_name() 
    {
        if (isset($_GET["x"]) && !isset($_GET["t"])) 
        {
            $pId = $_GET["x"];
            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=$pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
            return $sql->fetchAll();
        } else if (!isset($_GET["x"]) && !isset($_GET["t"])) {

            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=1");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
            return $sql->fetchAll();
        }
    }

    public function playlistNameDisplayHtml() 
    {
        if (!isset($_GET["t"])) {
            $pname = $this->playlist_name();
            foreach ($pname as $playlistName) {    
                echo "<div class=\"pName\">" . $playlistName->playlist_name . "</div>";     
            }
        } else {
            echo "<div class=\"pName\">" . $_GET["x"] . "</div>";     
        }
    }

    private function playlists() 
    {
        $sql = $this->pdo->prepare("SELECT * FROM playlists");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function userPublicPlaylists() {
        $user = $_SESSION["username"];
        $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user = (:user) AND playlistType = \"Public\"");
        $sql->bindParam(":user", $user, PDO::PARAM_STR);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function userPlaylist() {
        $user = $_SESSION["username"];
        $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user = (:user) AND playlistType NOT LIKE \"User\"");
        $sql->bindParam(":user", $user, PDO::PARAM_STR);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function getUserSongs($songId) {
        
        $user = $_SESSION["username"];
        if (isset($_GET["t"])) {
            $pname = $_GET["x"];
        } else {
            $pname = $this->playlist_name();
            foreach ($pname as $a) {
                $a->playlist_name;
            }
        }
        $sql = $this->pdo->prepare("
        SELECT playlist_id, playlist_name, playlistType 
        FROM playlists 
        WHERE playlistType = 'User' AND user = '$user'
        AND playlist_id NOT IN (
            SELECT playlist_id 
            FROM playlist_songs 
            WHERE song_id = :song_id
        )
    ");
    $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
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
        $playlist = $this->userPlaylist();
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
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" class=\"click\">" . $p->playlist_name . "<div class=\"pTypeDisplay\">$p->playlistType Playlist</div>" . "</a>" . "<button>Delete playlist</button>" ."</td>";
            echo "</tr>";
        }
    }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

}

    public function displayPublicPlaylists() {
        $publicPlaylist = $this->userPublicPlaylists();
        foreach ($publicPlaylist as $p) {
            echo "<tr>";
                echo "<td class=\"publicPlaylist\"> <a href=\"index.php?x=$p->playlist_id\" class=\"click\"> $p->playlist_name </a> </td>";
            echo "</tr>";
        }
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
            $user = $_SESSION["username"];
            $sql = $this->pdo->prepare("INSERT INTO playlists (playlist_name, playlistType, user) VALUES (:playlist_name, :playlistType, :user)");
            $sql->bindParam(":playlist_name", $playlist_name, PDO::PARAM_STR);
            $sql->bindParam(":playlistType", $playlistType, PDO::PARAM_STR);
            $sql->bindParam(":user", $user, PDO::PARAM_STR);
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
                echo "<div class=\"ERR\"\>Please input the correct options</div>";
            } else if ((!isset($_POST["songName"]) || empty($_POST["songName"])) && isset($_POST["insertPlaylist"]) && !empty($_FILES["audio-file"]["name"])) 
            {
                echo "<div class=\"ERR\"\>Please input a song name</div>";
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
                    $user = $_SESSION["username"];
                    move_uploaded_file($audioFileTmpName,$audioFileName);
                    $sql = $this->pdo->prepare("INSERT INTO songs (song_name, audio_path, user) VALUES (:song_name, :audio_path, :user)");
                    $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
                    $sql->bindParam(":audio_path", $audioFileName, PDO::PARAM_STR);
                    $sql->bindParam(":user", $user, PDO::PARAM_STR);

                    $sql->execute();

                    $songId = $this->pdo->lastInsertId();

                    $playlistId = $this->getPlaylistIdByName($_POST["insertPlaylist"]);
                    $this->addSongToPlaylist($songId, $playlistId);
                } 
            } else {
                echo "<div class=\"ERR\">Please input the correct options</div>";
            } 
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sendUserSong"])) {
            $songName = $_POST["songName"];
            $audioFileTmpName = $_FILES["audio-file"]["tmp_name"];
            $audioFileName = "./audio/".$_FILES["audio-file"]["name"];
            $user = $_SESSION["username"];
            move_uploaded_file($audioFileTmpName,$audioFileName);
            $sql = $this->pdo->prepare("INSERT INTO songs (song_name, audio_path, user) VALUES (:song_name, :audio_path, :user)");
            $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
            $sql->bindParam(":audio_path", $audioFileName, PDO::PARAM_STR);
            $sql->bindParam(":user", $user, PDO::PARAM_STR);

            $sql->execute();

            $songId = $this->pdo->lastInsertId();

            $playlistId = $this->getPlaylistIdByName($user);

            $this->addSongToPlaylist($songId, $playlistId);

        }
    }


    
    private function getPlaylistIdByName($playlistName) {
        $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE playlist_name = :playlist_name");
        $sql->bindParam(":playlist_name", $playlistName, PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['playlist_id'];
    }
    
    private function addSongToPlaylist($songId, $playlistId) {
        $sql = $this->pdo->prepare("INSERT INTO playlist_songs (song_id, playlist_id) VALUES (:song_id, :playlist_id)");
        $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
        $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
        $sql->execute();
    }

    private function getLocalPlaylists() 
    {
        $user = $_SESSION["username"];
        $sql = $this->pdo->prepare("SELECT playlist_id,playlist_name, playlistType FROM playlists WHERE playlistType =" . "\""."Local"."\"" . "AND user = \"$user\"");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function getPublicOrPrivatePlaylist($songId) {
        $user = $_SESSION["username"];
        if (isset($_GET["t"])) {
            $pname = $_GET["x"];
        } else {
            $pname = $this->playlist_name();
            foreach ($pname as $a) {
                $a->playlist_name;
            }
        }

        $sql = $this->pdo->prepare("
        SELECT playlist_id, playlist_name, playlistType 
        FROM playlists 
        WHERE (playlistType = 'Public' OR playlistType = 'Private') AND user = '$user'
        AND playlist_id NOT IN (
            SELECT playlist_id 
            FROM playlist_songs 
            WHERE song_id = :song_id
        ) 
    ");
        $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function getNewLocalPlaylists($songId) 
    {
        $user = $_SESSION["username"];
        if (isset($_GET["t"])) {
            $pname = $_GET["x"];
        } else {
            $pname = $this->playlist_name();
            foreach ($pname as $a) {
                $a->playlist_name;
            }
        }


        $sql = $this->pdo->prepare("
        SELECT playlist_id, playlist_name, playlistType 
        FROM playlists 
        WHERE playlistType = 'Local' AND user = '$user'
        AND playlist_id NOT IN (
            SELECT playlist_id 
            FROM playlist_songs 
            WHERE song_id = :song_id
        )
    ");
    $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
    $sql->execute();
    $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
    return $sql->fetchAll();
    }

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
            echo "<label for=\"audio-file\">Browse: </label>
            <input type=\"file\" name=\"audio-file\" id=\"audio-file\" accept=\".ogg, .flac, .mp3\">
            <br>
            <br>
            <label for=\"songName\">Name:</label>
            <input type=\"text\" name=\"songName\">
            <br>
            <br>
            <button name=\"sendUserSong\">Upload Song</button>";
        }
    }
}


    
?>