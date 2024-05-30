<?php

    abstract class Db_Connection {
        public ?PDO $pdo;

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

    }
    class SQL_Functions extends Db_Connection {
        public function checkIfUserExists($username, $email) {
            $sql = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            return $count > 0; 
        }

        public function loginUser($username, $password) {
            $sql = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
            $sql->bindParam(":username", $username, PDO::PARAM_STR);
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

        public function createUserPlaylist($username, $name) {
            if ($name == "User") {
                $type = "User";
                $sql = "INSERT INTO playlists (playlist_name, playlistType, user) VALUES (:username, :user, :user2)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                $stmt->bindParam(":user", $type, PDO::PARAM_STR);
                $stmt->bindParam(":user2", $username, PDO::PARAM_STR);

                $stmt->execute();
            } else if ($name == "Liked") {
                $type = "Liked";
                $u = $username . $type;
                $sql = "INSERT INTO playlists (playlist_name, playlistType, user) VALUES (:username, :user, :user2)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(":username", $u, PDO::PARAM_STR);
                $stmt->bindParam(":user", $type, PDO::PARAM_STR);
                $stmt->bindParam(":user2", $username, PDO::PARAM_STR);

                $stmt->execute();
            }
        }

        public function getSongIdFromPlaylistSongs() {
            if (isset($_GET["x"]) && !isset($_GET["u"])) 
            {
                $pId = $_GET["x"]; 
                $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id=$pId");
                $sql->execute();
                return $sql->fetchAll();
            } else if (!isset($_GET["x"]) && isset($_GET["u"])){
                $pId = $this->getPlaylistIdByName($_GET["u"]); 
                $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id = $pId");
                $sql->execute();
                return $sql->fetchAll();
            } 
        } 
        public function song() {
            if (isset($_GET["x"]) || isset($_GET["u"])) {
                $sIds = $this->getSongIdFromPlaylistSongs();
        
                $songs = array();
        
                foreach ($sIds as $sId) {
                    $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=:sId");
                    $sql->bindParam(":sId", $sId["song_id"], PDO::PARAM_INT);
                    $sql->execute();
                    $songs[] = $sql->fetch();
                }
        
                return $songs;
            } else {
                $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=1");
                $sql->execute();
                return $sql->fetchAll();
            }
        }

        public function userSong() {
            if (isset($_GET["x"])) {
                $sIds = $this->getSongIdFromPlaylistSongs();
                $user = $_SESSION["username"];
                $songs = array();
        
                foreach ($sIds as $sId) {

                    $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=:sId AND user = :user");
                    $sql->bindParam(":sId", $sId->song_id, PDO::PARAM_INT);
                    $sql->bindParam(":user", $user, PDO::PARAM_STR);
                    $sql->execute();
                    $songs[] = $sql->fetch();
                }
        
                return $songs;
            } else {
                $sql = $this->pdo->prepare("SELECT * FROM songs WHERE song_id=1 AND user = :user");
                $sql->bindParam(":user", $user, PDO::PARAM_STR);
                $sql->execute();
                return $sql->fetchAll();
            }
        }

        public function isUserPlaylist($playlistId) {
            $user = $_SESSION["username"];
            $sql = $this->pdo->prepare("SELECT COUNT(*) AS count FROM playlists WHERE playlist_id = :playlistId AND user = :user");
            $sql->bindParam(":playlistId", $playlistId, PDO::PARAM_INT);
            $sql->bindParam(":user", $user, PDO::PARAM_STR);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        }

        public function getUserIdByName($user) {
            $sql = $this->pdo->prepare("SELECT user_id FROM users WHERE username = :user");
            $sql->bindParam(":user", $user, PDO::PARAM_STR);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['user_id'];
        }

        public function removeFromFavorites($playlistId, $userId) {
            $sql = $this->pdo->prepare("DELETE FROM playlist_likes WHERE playlist_id = :playlist_id AND user_id = :user_id");
            $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
            $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
            $sql->execute();
        }

        public function deletePlaylist($playlist) {
            try {
                $this->pdo->beginTransaction();
        

                $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id = :playlist_id");
                $sql->bindParam(":playlist_id", $playlist, PDO::PARAM_INT);
                $sql->execute();
                $songs = $sql->fetchAll(PDO::FETCH_ASSOC);
        

                foreach ($songs as $song) {
                    $this->deleteSongFromOnePlaylistSongs($song['song_id'], $playlist);
                }
        

                $sql = $this->pdo->prepare("DELETE FROM playlists WHERE playlist_id = :playlist_id");
                $sql->bindParam(":playlist_id", $playlist, PDO::PARAM_INT);
                $sql->execute();
        
                $this->pdo->commit();
            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw $e;
            }
        }

        public function renamePlaylist($playlistId, $newName) {
            if (empty($newName)) {
                throw new Exception("Playlist name can't be empty.");
            }
        
            $sql = $this->pdo->prepare("UPDATE playlists SET playlist_name = :new_name WHERE playlist_id = :playlist_id");
            $sql->bindParam(":new_name", $newName, PDO::PARAM_STR);
            $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
        
            if ($sql->execute()) {
                return "Playlist name change succesful";
            } else {
                throw new Exception("There was a problem with changing the playlist name.");
            }
        }
        

        public function likePlaylist($playlistId) {
        
            $user = $this->getUserIdByName($_SESSION["username"]);
            $sqlCheck = $this->pdo->prepare("SELECT COUNT(*) as count FROM playlist_likes WHERE user_id = :user AND playlist_id = :playlistId");
            $sqlCheck->bindParam(":user", $user, PDO::PARAM_STR);
            $sqlCheck->bindParam(":playlistId", $playlistId, PDO::PARAM_INT);
            $sqlCheck->execute();
            $result = $sqlCheck->fetch(PDO::FETCH_ASSOC);
        
            if ($result['count'] > 0) {
                return false; 
            }
        

            $sqlLike = $this->pdo->prepare("INSERT INTO playlist_likes (user_id, playlist_id) VALUES (:user, :playlistId)");
            $sqlLike->bindParam(":user", $user, PDO::PARAM_STR);
            $sqlLike->bindParam(":playlistId", $playlistId, PDO::PARAM_INT);
            $sqlLike->execute();
        
            return true; 
        }

        public function getUserLikedPlaylists() {
            $user = $this->getUserIdByName($_SESSION["username"]);
            $sql = $this->pdo->prepare("SELECT *
                                        FROM playlists
                                        INNER JOIN playlist_likes ON playlists.playlist_id = playlist_likes.playlist_id
                                        WHERE playlist_likes.user_id = :user");
            $sql->bindParam(":user", $user, PDO::PARAM_INT);
            $sql->execute();
            return $sql->fetchAll();
        }

        public function isPlaylistLikedByUser($playlistId) {
            $userId = $this->getUserIdByName($_SESSION["username"]); 
            $sql = $this->pdo->prepare("SELECT COUNT(*) AS count FROM playlist_likes WHERE user_id = :userId AND playlist_id = :playlistId");
            $sql->bindParam(":userId", $userId, PDO::PARAM_INT);
            $sql->bindParam(":playlistId", $playlistId, PDO::PARAM_INT);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0; 
        }

        public function countPlaylistLikes($playlistId) {
            $userId = $this->getUserIdByName($_SESSION["username"]); 
            $sql = $this->pdo->prepare("SELECT COUNT(*) AS count FROM playlist_likes WHERE playlist_id = :playlistId");
            $sql->bindParam(":playlistId", $playlistId, PDO::PARAM_INT);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count']; 
        }

        public function pTypeFromId($pId) {
                $sql = $this->pdo->prepare("SELECT playlistType FROM playlists WHERE playlist_id = $pId");
                $sql->execute();
                return $sql->fetchAll();
        }


        public function deleteUser($username) {
            try {
                $userId = $this->getUserIdByName($username);
                $this->pdo->beginTransaction();
        

                $sql = $this->pdo->prepare("DELETE FROM playlist_songs WHERE playlist_id IN (SELECT playlist_id FROM playlists WHERE user = :user)");
                $sql->bindParam(":user", $username, PDO::PARAM_STR);
                $sql->execute();
        

                $sql = $this->pdo->prepare("DELETE FROM playlist_likes WHERE playlist_id IN (SELECT playlist_id FROM playlists WHERE user = :user)");
                $sql->bindParam(":user", $username, PDO::PARAM_STR);
                $sql->execute();
        

                $sql = $this->pdo->prepare("DELETE FROM playlist_likes WHERE user_id = (SELECT user_id FROM users WHERE username = :user)");
                $sql->bindParam(":user", $username, PDO::PARAM_STR);
                $sql->execute();
        

                $sql = $this->pdo->prepare("DELETE FROM playlist_songs WHERE song_id IN (SELECT song_id FROM songs WHERE user = :user)");
                $sql->bindParam(":user", $username, PDO::PARAM_STR);
                $sql->execute();
        
                $sql = $this->pdo->prepare("DELETE FROM songs WHERE user = :user");
                $sql->bindParam(":user", $username, PDO::PARAM_STR);
                $sql->execute();
        
                $sql = $this->pdo->prepare("DELETE FROM playlists WHERE user = :user");
                $sql->bindParam(":user", $username, PDO::PARAM_STR);
                $sql->execute();
        

                $sql = $this->pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
                $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $sql->execute();
        
                $this->pdo->commit();
        
            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw $e;
            }
        }

        

        public function changeUserName($oldUsername, $newUsername) {
            try {
                $userId = $this->getUserIdByName($oldUsername);
        
                $sql = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :newUsername");
                $sql->bindParam(":newUsername", $newUsername, PDO::PARAM_STR);
                $sql->execute();
                if ($sql->fetchColumn() > 0) {
                    throw new Exception("Username already taken.");
                }
        
                $this->pdo->beginTransaction();
        
                $sql = $this->pdo->prepare("UPDATE users SET username = :newUsername WHERE user_id = :user_id");
                $sql->bindParam(":newUsername", $newUsername, PDO::PARAM_STR);
                $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
                $sql->execute();
        
                $sql = $this->pdo->prepare("UPDATE playlists SET user = :newUsername WHERE user = :oldUsername");
                $sql->bindParam(":newUsername", $newUsername, PDO::PARAM_STR);
                $sql->bindParam(":oldUsername", $oldUsername, PDO::PARAM_STR);
                $sql->execute();
        
                $sql = $this->pdo->prepare("UPDATE songs SET user = :newUsername WHERE user = :oldUsername");
                $sql->bindParam(":newUsername", $newUsername, PDO::PARAM_STR);
                $sql->bindParam(":oldUsername", $oldUsername, PDO::PARAM_STR);
                $sql->execute();
        
                $this->pdo->commit();
        
            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw $e;
            }
        }

        
        public function deleteSong($newSongId) {
            $sql = $this->pdo->prepare("DELETE FROM songs WHERE song_id = (:song_id)");
            $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
            $sql->execute();
        }
        public function deleteSongFromPlaylistSongs($newSongId) {
            $sql = $this->pdo->prepare("DELETE FROM playlist_songs WHERE song_id = (:song_id)");
            $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
            $sql->execute();
        } 
        
        public function deleteSongFromOnePlaylistSongs($newSongId,$removepId) {
            $sql = $this->pdo->prepare("DELETE FROM playlist_songs WHERE song_id = (:song_id) AND playlist_id = (:playlist_id)");
            $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
            $sql->bindparam(":playlist_id", $removepId, PDO::PARAM_INT);
            $sql->execute();
        } 

        public function changeSongName($newSongName, $newSongId) {
            $sql = $this->pdo->prepare("UPDATE songs SET song_name = (:new_song_name) WHERE song_ID = (:song_id)");
            $sql->bindParam(":new_song_name", $newSongName, PDO::PARAM_STR);
            $sql->bindParam(":song_id", $newSongId, PDO::PARAM_INT);
            $sql->execute();
        }

        public function countSong($user) {
            $sql = $this->pdo->prepare("
                SELECT COUNT(ps.song_id) AS song_count
                FROM playlist_songs ps
                JOIN songs s ON ps.song_id = s.song_id
                JOIN playlists p ON ps.playlist_id = p.playlist_id
                WHERE s.user = :user AND p.playlistType = 'User'
            ");
            $sql->bindParam(":user", $user, PDO::PARAM_STR);
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


        public function playlist_name() 
        {
            if ((isset($_GET["x"]) && !isset($_GET["u"]) && !isset($_SESSION["playlist"])) || (isset($_GET["x"]) && !isset($_GET["u"]) && isset($_SESSION["playlist"]))) 
            {
                $pId = $_GET["x"];
                $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=$pId");
                $sql->execute();
                return $sql->fetchAll();
            } else if (!isset($_GET["x"]) && !isset($_GET["u"]) && !isset($_SESSION["playlist"])) {
                $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=0");
                $sql->execute();
                return $sql->fetchAll();
            } else if (!isset($_GET["x"]) && !isset($_GET["u"]) && isset($_SESSION["playlist"])) {
                $pId = $_SESSION["playlist"];
                $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=$pId");
                $sql->execute();
                return $sql->fetchAll();
            } 
        }

        public function playlistNameDisplayHtml() 
        {
            if ((!isset($_GET["u"]) && isset($_GET["x"])) || (isset($_SESSION["playlist"]))) {
                $pname = $this->playlist_name();
                foreach ($pname as $playlistName) {    
                    $playlistName["playlist_name"];    
                }
                $name = $playlistName["playlist_name"];
                $pVar = $this->getPlaylistType($name);
                foreach ($pVar as $p) {
                    $p["playlistType"];
                }
                $pType = $p["playlistType"];
                if ($pType == "Liked") {
                    echo "<div class=\"pName\">Liked Songs</div>";  
                } else {
                foreach ($pname as $playlistName) {    
                    echo "<div class=\"pName\">" . $name . "</div>";     
                }
            }
            } else if (isset($_GET["u"]) && !isset($_GET["x"])){
                echo "<div class=\"pName\">" . $_GET["u"] . "</div>";     
            } else if (!isset($_GET["u"]) || !isset($_GET["x"])) {

            }
        }

        public function playlists() 
        {
            $sql = $this->pdo->prepare("SELECT * FROM playlists");
            $sql->execute();
            return $sql->fetchAll();
        }

        public function userPublicPlaylists() {
            $user = $_GET["u"];
            $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user = (:user) AND playlistType = \"Public\"");
            $sql->bindParam(":user", $user, PDO::PARAM_STR);
            $sql->execute();
            return $sql->fetchAll();
        }

        public function userPlaylist() {
            $user = $_SESSION["username"];
            $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user = (:user) AND playlistType NOT LIKE \"User\" AND playlistType NOT LIKE \"Liked\"");
            $sql->bindParam(":user", $user, PDO::PARAM_STR);
            $sql->execute();
            return $sql->fetchAll();
        }

        public function getUserSongs($songId) {
            
            $user = $_SESSION["username"];
            if (isset($_GET["u"])) {
                $pname = $_GET["u"];
            } else {
                $pname = $this->playlist_name();
                foreach ($pname as $a) {
                    $a["playlist_name"];
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
        return $sql->fetchAll();

        }

        public function sortedPlaylists() {
            $user = $_SESSION["username"];
            $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user LIKE \"$user\" ORDER BY playlistType");
            $sql->execute();
            return $sql->fetchAll();
        }

        public function getUserLikedSongs($user) {
            $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE user LIKE \"$user\" AND playlistType LIKE \"Liked\"");
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["playlist_id"];
        }

    

        public function getLastRecord() 
        {
            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id = 1");
            $sql->execute();
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
        
        public function getPIDFromPlaylists() 
        {
            $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE playlist_name=" . "\"".$_POST["insertPlaylist"]."\"");
            $sql->execute();
            return $sql->fetchAll();
        }

        public function getPlaylistType($pName) 
        {
            $sql = $this->pdo->prepare("SELECT playlistType FROM playlists WHERE playlist_name=\"$pName\"");
            $sql->execute();
            return $sql->fetchAll();
        }

        public function getSearchQueryUsers() {
            if (isset($_GET["query"]) || isset($_POST["query"])) {
                if (isset($_GET["query"])) {
                    $searchQuery = $_GET["query"];
                } else if (isset($_POST["query"])) {
                    $searchQuery = $_POST["query"];
                }
                if ($searchQuery == "admin") {
                    echo "ERR";
                } else {
                    $sql = $this->pdo->prepare("SELECT * FROM users WHERE username = :Uname");
                    $sql->bindParam(":Uname", $searchQuery, PDO::PARAM_STR);
                    $sql->execute();
                    return $sql->fetchAll();
                }
            }
        }

        public function getSearchQuerySongs() {
            if (isset($_GET["query"]) || isset($_POST["query"])) {
                if (isset($_GET["query"])) {
                    $searchQuery = $_GET["query"];
                } else if (isset($_POST["query"])) {
                    $searchQuery = $_POST["query"];
                }
                $sql = "SELECT songs.* 
                        FROM songs 
                        JOIN playlist_songs ON songs.song_id = playlist_songs.song_id 
                        JOIN playlists ON playlist_songs.playlist_id = playlists.playlist_id 
                        WHERE playlists.playlistType = 'User' 
                        AND songs.song_name = :Song";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(":Song", $searchQuery, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        public function getSearchQueryPlaylists() {
            if (isset($_GET["query"]) || isset($_POST["query"])) {
                if (isset($_GET["query"])) {
                    $searchQuery = $_GET["query"];
                } else if (isset($_POST["query"])) {
                    $searchQuery = $_POST["query"];
                }
                $sql = "SELECT * FROM playlists WHERE playlist_name = :Pname AND playlistType = \"Public\"";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(":Pname", $searchQuery, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        public function getSongData() {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["sendSong"])) {
                    $this->validateAndUploadSongLocal();
                } elseif (isset($_POST["sendUserSong"])) {
                    $this->validateAndUploadUserSong();
                }
            }
        }
        
        public function validateAndUploadSongLocal() {
            $songName = $_POST["songName"] ?? '';
            $playlistName = $_POST["insertPlaylist"] ?? '';
            $audioFileName = $_FILES["audio-file"]["name"] ?? '';
            $audioFileTmpName = $_FILES["audio-file"]["tmp_name"] ?? '';
            $imageFileName = $_FILES["image"]["name"] ?? '';
            $imageTmpName = $_FILES["image"]["tmp_name"] ?? '';
            $user = $_SESSION["username"];
        
            if (empty($songName) && empty($playlistName) && empty($audioFileName)) {
                echo "<div class=\"ERR\">Please input the correct options</div>";
            } elseif (empty($songName) && !empty($playlistName) && !empty($audioFileName)) {
                echo "<div class=\"ERR\">Please input a song name</div>";
            } elseif (!empty($songName) && empty($playlistName) && !empty($audioFileName)) {
                echo "<div class=\"ERR\">Error: Please input a playlist.</div>";
            } elseif (!empty($songName) && !empty($playlistName) && empty($audioFileName)) {
                echo "<div class=\"ERR\">Error: Please input a file.</div>";
            } elseif (!empty($songName) && !empty($playlistName) && !empty($audioFileName)) {
                $p = $this->getPlaylistType($playlistName);
                foreach ($p as $b) {
                    $temp = $b["playlistType"];
                }
                if ($temp == "Local") {
                    $audioFilePath = "./audio/" . $audioFileName;
                    move_uploaded_file($audioFileTmpName, $audioFilePath);
                    $imageFilePath = "./images/songImages/" . $imageFileName;
                    move_uploaded_file($imageTmpName, $imageFilePath);
        
                    $sql = $this->pdo->prepare("INSERT INTO songs (song_name, audio_path, image_path, user) VALUES (:song_name, :audio_path, :image_path, :user)");
                    $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
                    $sql->bindParam(":audio_path", $audioFilePath, PDO::PARAM_STR);
                    $sql->bindParam(":image_path", $imageFilePath, PDO::PARAM_STR);
                    $sql->bindParam(":user", $user, PDO::PARAM_STR);
        
                    $sql->execute();
        
                    $songId = $this->pdo->lastInsertId();
                    $playlistId = $this->getPlaylistIdByNameUserVer($playlistName);
                    $this->addSongToPlaylist($songId, $playlistId);
                }
            } else {
                echo "<div class=\"ERR\">Please input the correct options</div>";
            }
        }
        
        public function validateAndUploadUserSong() {
            $songName = $_POST["songName"] ?? '';
            $audioFileName = $_FILES["audio-file"]["name"] ?? '';
            $audioFileTmpName = $_FILES["audio-file"]["tmp_name"] ?? '';
            $imageFileName = $_FILES["image"]["name"] ?? '';
            $imageTmpName = $_FILES["image"]["tmp_name"] ?? '';
            $user = $_SESSION["username"];
        
            if (empty($songName) || empty($audioFileName) || empty($imageFileName)) {
                echo "<div class=\"ERR\">Please input the correct options</div>";
            } else {
                $audioFilePath = "./audio/" . $audioFileName;
                move_uploaded_file($audioFileTmpName, $audioFilePath);
                $imageFilePath = "./images/songImages/" . $imageFileName;
                move_uploaded_file($imageTmpName, $imageFilePath);
        
                $sql = $this->pdo->prepare("INSERT INTO songs (song_name, audio_path, image_path, user) VALUES (:song_name, :audio_path, :image_path, :user)");
                $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
                $sql->bindParam(":audio_path", $audioFilePath, PDO::PARAM_STR);
                $sql->bindParam(":image_path", $imageFilePath, PDO::PARAM_STR);
                $sql->bindParam(":user", $user, PDO::PARAM_STR);
        
                $sql->execute();
        
                $songId = $this->pdo->lastInsertId();
                $playlistId = $this->getPlaylistIdByName($user);
                $this->addSongToPlaylist($songId, $playlistId);
            }
        }

        
        public function getPlaylistIdByName($playlistName) {
            $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE playlist_name = :playlist_name");
            $sql->bindParam(":playlist_name", $playlistName, PDO::PARAM_STR);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['playlist_id'];
        }
        
        public function getPlaylistIdByNameUserVer($playlistName) {
            $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE playlist_name = :playlist_name AND user = :user");
            $sql->bindParam(":playlist_name", $playlistName, PDO::PARAM_STR);
            $sql->bindParam(":user", $_SESSION["username"], PDO::PARAM_STR);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['playlist_id'];
        }

        public function addSongToPlaylist($songId, $playlistId) {
            $sql = $this->pdo->prepare("INSERT INTO playlist_songs (song_id, playlist_id) VALUES (:song_id, :playlist_id)");
            $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
            $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
            $sql->execute();
        }

        public function getLocalPlaylists() 
        {
            $user = $_SESSION["username"];
            $sql = $this->pdo->prepare("SELECT playlist_id,playlist_name, playlistType FROM playlists WHERE playlistType =" . "\""."Local"."\"" . "AND user = \"$user\"");
            $sql->execute();
            return $sql->fetchAll();
        }

        public function getPublicOrPrivatePlaylist($songId) {
            $user = $_SESSION["username"];
            if (isset($_GET["u"])) {
                $pname = $_GET["u"];
            } else {
                $pname = $this->playlist_name();
                foreach ($pname as $a) {
                    $a["playlist_name"];
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
            return $sql->fetchAll();
        }

        public function getNewLocalPlaylists($songId) 
        {
            $user = $_SESSION["username"];
            if (isset($_GET["u"])) {
                $pname = $_GET["u"];
            } else {
                $pname = $this->playlist_name();
                foreach ($pname as $a) {
                    $a["playlist_name"];
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
        return $sql->fetchAll();
        }

        public function insertPlaylistChange() 
        {
            if (isset($_POST["insertIntoPlaylist"])) {
                $sql = $this->pdo->prepare("INSERT INTO playlist_songs (song_id, playlist_id) VALUES (:song_id, :playlist_id)");
                $sql->bindParam(":song_id", $songId, PDO::PARAM_INT);
                $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
                $sql->execute();
            }
        } 


}

class HTML_Display_Functions extends SQL_Functions {
    public function displayLocalPlaylists() 
    {
        $playlist = $this->getLocalPlaylists();
        
        echo "<div class=\"playlistsContainer\">";
        echo "<table class=\"playlistTable\">";
        echo "<thead>";
        echo "</thead>";
        echo "<tbody>";
    
        foreach ($playlist as $p) {
            $playlistName = $p["playlist_name"];
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<input type=\"radio\" name=\"insertPlaylist\" class=\"localTitle\" value=\"$playlistName\">" . $playlistName . "</input>" . "</td>";
            echo "</tr>";
        }
    
        echo "</tbody>";
        echo "</table>";
    
        echo "</div>";
    }

    public function playlistQueryDisplay() {
        $playlists = $this->getSearchQueryPlaylists();
        echo "<table class=\"playlistQueryTable\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Public playlists</th>";
        echo "<th>Made By</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($playlists as $p) {
            $pName = $p["playlist_name"];
            $pId = $p["playlist_id"];
            $pOwner = $p["user"];
            echo "<tr>";
            echo "<td class=\"userTd\"><a href=\"./index.php?x=$pId\">$pName</a></td>";
            echo "<td class=\"userTd\">$pOwner</td>";
            echo "</tr>";

        }
        echo "</tbody>";
        echo "</table>";
    }

    public function displayPublicPlaylists() {
        $publicPlaylist = $this->userPublicPlaylists();
        foreach ($publicPlaylist as $p) {
            $pId = $p["playlist_id"];
            $playlistName = $p["playlist_name"];
            echo "<tr>";
                echo "<td class=\"publicPlaylist\"> <a href=\"index.php?x=$pId\" class=\"click\"> $playlistName </a> </td>";
            echo "</tr>";
        }
    }

    public function userDisplayHtml() {
        $users = $this->getSearchQueryUsers();
        echo "<table class=\"userTable\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th>List of users</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        if (isset($_GET["query"])) {
            $query = $_GET["query"];
        } else if (isset($_POST["query"])) {
            $query = $_POST["query"];
        }
        if ($query != "admin")
        foreach ($users as $u) {
            $username = $u["username"];
            echo "<tr>";
            echo "<td class=\"userTd\"><a href=\"./account.php?u=$username\" hx-push-url=\"account.php?u=$username\" hx-trigger=\"click\" hx-get=\"./htmx/test.php?u=$username\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\">$username</a></td>";
            echo "</tr>";

        }
        echo "</tbody>";
        echo "</table>";
    }

    public function isSongLiked($songId, $username) {
        try {
            $sql = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM playlist_songs ps
                JOIN playlists p ON ps.playlist_id = p.playlist_id
                WHERE ps.song_id = :song_id
                  AND p.user = :username
                  AND p.playlistType = 'Liked'
            ");
            $sql->bindParam(':song_id', $songId, PDO::PARAM_INT);
            $sql->bindParam(':username', $username, PDO::PARAM_STR);
            $sql->execute();
    
            $count = $sql->fetchColumn();
            
            return $count > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }    

    public function songDisplayHtml() 
    { 
        $songs = $this->song();
        $searchedSongs = $this->getSearchQuerySongs();
        $a = 0;

        $tempVal = "";

        $defaultPlaylistParam = 1;
        if (isset($_GET["x"])) {
            $defaultPlaylistParam = sizeof($songs);
        } else if ((isset($_POST["query"]) || isset($_GET["query"]))){
            $defaultPlaylistParam = sizeof($searchedSongs);
        }
        /*
        if ($defaultPlaylistParam == 0) {
            echo "Playlist is empty";
        } else {*/

        if (isset($_GET["x"]) && !isset($_GET["u"])) { 
            $x = $_GET["x"];
            $pType = $this->pTypeFromId($x);
            foreach ($pType as $pT) {
                $pT["playlistType"];
            }
        
            if ($pT["playlistType"] == "Local") {
                $tempVal = "Local";
            } else if ($pT["playlistType"] == "Liked") {
                $tempVal = "Liked";
            } else if ($pT["playlistType"] == "Public"){
                $tempVal = "Public";
            } else if ($pT["playlistType"] == "Private") {
                $tempVal = "Private";
            }
            
        } else if (isset($_GET["u"]) && !isset($_GET["x"])) {
            $x = $_GET["u"];
            $pType = "User";
            $tempVal = "User";
        }
        $memVar = false;
        if ($defaultPlaylistParam == 0) {
            if (($tempVal == "Local" || $tempVal == "Private" ||  $tempVal == "Liked") && $this->isUserPlaylist($_GET["x"]) == false) {
                echo "Access Denied";
                $memVar = true;
            } else {
            echo "Playlist is empty";
            }
        } else {
        echo "<div class=\"songsContainer\">";
        echo "<table class=\"songTable\">"; 
        echo "<thead>";
        echo "<tr>";
        echo "<th></th>";
        echo "<th>Name</th>";
        echo "<th>Artist</th>";
        echo "<th>Length</th>";
        echo "<th class=\"options\">Options</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        }
    if (isset($pType) && !isset($_GET["query"]) && !isset($_POST["query"])) {
            if (($tempVal == "Local" || $tempVal == "Private" || $tempVal == "User" || $tempVal == "Liked") && isset($_GET["x"]) && $this->isUserPlaylist($_GET["x"]) == false) {
                if ($memVar == false) {
                    echo "Access Denied";
                }
            } else {
        foreach ($songs as $song) {
            global $a;
            $a = $a + 1;
            if (!isset($_GET["u"])) {
                $pId = $_GET["x"];
            }
            $songId = $song["song_id"];
            $songName = $song["song_name"];
            $songPath = $song["audio_path"];
            $songImagePath = $song["image_path"];
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . 
            "<button name=\"play\" type = \"button\"class=\"play\" value=\"$songPath\">". $a .
            "<input type=\"hidden\" name=\"jsName\" value=\"$songName\" class=\"jsName\">". 
            "<input type=\"hidden\" name=\"jsImage\" value=\"$songImagePath\" class=\"jsImage\">". 
            "</button>" ."</td>";
            echo "<td>" . $song["song_name"] . "</td>";
            echo "<td>" . $song["user"] . "</td>";
            echo "<td class=\"songLength\" data-audio=\"$songPath\"></td>";
            echo "<script src=\"./JS/audio/songLength.js\"></script>";
            echo "<td>" . "<div class=\"Help\">Option<span class=\"helpText\">"; 
            $localPlaylist = $this->getNewLocalPlaylists($songId);
            $publicOrPrivatePlaylist = $this->getPublicOrPrivatePlaylist($songId);
            echo "<div class=\"playlistsContainer\">";
            echo "<table class=\"playlistTable\">";
            echo "<thead>";
            echo "</thead>";
            echo "<tbody>";
        if ($tempVal == "Local") {
            foreach ($localPlaylist as $p) {
                $playlistName = $p["playlist_name"];
                echo "<tr class=\"s1\">";
                echo "<form method=\"post\">";
                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$playlistName\">$playlistName</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
                echo "</form>";
                echo "</tr>";
            }
        } else if ($tempVal == "Public" || $tempVal == "Private" || $tempVal == "User") {
            echo "<form method=\"post\">";
            if ($tempVal == "Public" || $tempVal == "Private") {
                if ($this->isSongLiked($songId, $_SESSION["username"]) == false) {
                    echo "<button name=\"insertIntoLiked\" class=\"localTitle\">Like Song</button>" . "</td>";
                }
            }
            foreach ($publicOrPrivatePlaylist as $p) {
                $playlistName = $p["playlist_name"];
                echo "<tr class=\"s1\">";

                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$playlistName\">$playlistName</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
                echo "</tr>";
        }
            echo "</form>";
    }       if ($tempVal == "Local") {
                if (isset($_GET["x"])) {
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
                }
            } else if ($tempVal == "User" && ($_GET["u"] == $_SESSION["username"]) || $_SESSION["username"] == "admin") {
                echo "<form method=\"post\">";
                echo "<tr class=\"s1\">";
                echo "<button name=\"deleteSong\" class=\"localTitle\" value=\"$songId\">Delete Song</button>" . "</td>"; 
                echo "</tr>";
                echo "<tr class=\"s1\">";
                echo "<input type=\"text\" name=\"changeSongName\" class=\"localTitle\" placeholder=\"Change Name\"></input>" . "</td>";
                echo "<input type=\"hidden\" name=\"changeSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"PublishedSongs\" value=\"$songId\"></input>";
                echo "</tr>";
            echo "</form>";
            } else if ($tempVal == "User" && $_GET["u"] != $_SESSION["username"]) {
                echo "<form method=\"post\">";
                echo "<tr class=\"s1\">";
                echo "<button name=\"insertIntoLiked\" class=\"localTitle\">Like Song</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"changeSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
                echo "</tr>";
            echo "</form>";
            } else if (($tempVal == "Public" || $tempVal == "Private") && $this->isUserPlaylist($_GET["x"]) == false) {
                echo "<form method=\"post\">";
                echo "<tr class=\"s1\">";
                echo "<input type=\"hidden\" name=\"changeSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"removepId\" value=\"$pId\"></input>";
                echo "</tr>";
            }
            else if ($tempVal == "Liked") {
                echo "<form method=\"post\">";
                echo "<tr class=\"s1\">";
                echo "<button name=\"removeSong\" class=\"localTitle\" value=\"$songId\">Remove From Liked</button>" . "</td>"; 
                echo "<input type=\"hidden\" name=\"changeSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"removepId\" value=\"$pId\"></input>";
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
    } else if (!isset($pType) && (isset($_GET["query"]) || (isset($_POST["query"]) || isset($_POST["songSearchDisplay"]) || isset($_POST["playlistSearchDisplay"])))) {
        if (isset($_POST["query"])) {   
            $query = $_POST["query"];
        } else if (isset($_GET["query"])) {
            $query = $_GET["query"];
        }
        echo $query;
        foreach ($searchedSongs as $song) {
            global $a;
            $a = $a + 1;
            $songId = $song["song_id"];
            $songName = $song["song_name"];
            $songPath = $song["audio_path"];
            $songImagePath = $song["image_path"];
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . 
            "<button name=\"play\" type = \"button\"class=\"play\" value=\"$songPath\">". $a .
            "<input type=\"hidden\" name=\"jsName\" value=\"$songName\" class=\"jsName\">".   
            "<input type=\"hidden\" name=\"jsImage\" value=\"$songImagePath\" class=\"jsImage\">". 
            "</button>" ."</td>";
            echo "<td>" . $songName . "</td>";
            echo "<td>" . $song["user"] . "</td>";
            echo "<td>" . $song["length"] . "</td>";
            echo "<td>" . "<div class=\"Help\">" . "Option" . "<span class=\"helpText\">"; 
            $publicOrPrivatePlaylist = $this->getPublicOrPrivatePlaylist($songId);
            echo "<div class=\"playlistsContainer\">";
            echo "<table class=\"playlistTable\">";
            echo "<thead>";
            echo "</thead>";
            echo "<tbody>";
            echo "<tr class=\"s1\">";
            echo "<form method=\"post\">";
            echo "<button name=\"insertIntoLiked\" class=\"localTitle\">Like Song</button>";
            echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
            echo "</form>";
            echo "</tr>";

            if ($_SESSION["username"] == "admin") {
                echo "<form method=\"post\">";
                echo "<button name=\"deleteSong\" class=\"localTitle\" value=\"$songId\">Delete Song</button>"; 
                echo "</form>";
            }
            foreach ($publicOrPrivatePlaylist as $p) {
                $playlistName = $p["playlist_name"];
                echo "<tr class=\"s1\">";
                echo "<form method=\"post\" >";
                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$playlistName\">$playlistName</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "</form>";
                echo "</tr>";
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
    
        if (((isset($_POST["insertIntoPlaylist"]) && !empty($_POST["insertIntoPlaylist"])) || isset($_POST["insertIntoLiked"])) || isset($_POST["removeSong"]) || isset($_POST["changeSongName"]))  {
        if ((isset($_POST["insertIntoPlaylist"]) && !empty($_POST["insertIntoPlaylist"])) || isset($_POST["insertIntoLiked"])) {
            $songId = $_POST["insertSongId"];
            if (isset($_POST["insertIntoPlaylist"])) {
                $playlistId = $this->getPlaylistIdByNameUserVer($_POST["insertIntoPlaylist"]);
                $this->addSongToPlaylist($songId,$playlistId);
            } else if (isset($_POST["insertIntoLiked"])){
                $likeId = $this->getUserLikedSongs($_SESSION["username"]);
                $this->addSongToPlaylist($songId,$likeId);
            }
            if (isset($_GET["u"])) {
                if (isset($_GET["songs"])) {
                    if (isset($x)) {
                        echo "<script>window.location.href = './account.php?u=$x&songs';</script>"; //header nie dziala :(
                    }
                } else if (isset($_GET["playlists"])) {
                    if (isset($x)) {
                        echo "<script>window.location.href = './account.php?u=$x&playlists';</script>"; //header nie dziala :(
                    }
            } 
            } else if(isset($_GET["query"])) {
                $q = $_GET["query"];
                echo "<script>window.location.href = './search.php?query=$q&songs';</script>"; 
            } else {
                if (isset($pId)) {
                    echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
                }
            }

        }  
        if (isset($_POST["deleteSong"]) && empty($_POST["changeSongName"])) {
            $newSongId = $_POST["deleteSong"];
            $this->deleteSongFromPlaylistSongs($newSongId);
            $this->deleteSong($newSongId);
            if (isset($_GET["u"])) {
                if (isset($_GET["songs"])) {
                    if (isset($x)) {
                        echo "<script>window.location.href = './account.php?u=$x&songs';</script>"; //header nie dziala :(
                    }
                }
            } else if (isset($_GET["playlists"])) {
                if (isset($x)) {
                    echo "<script>window.location.href = './account.php?u=$x&playlists';</script>"; //header nie dziala :(
                }
            } else {
                if (isset($pId)) {
                    echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
                }
            }
        } else if (isset($_POST["removeSong"]) && empty($_POST["changeSongName"])) {
            $newSongId = $_POST["removeSong"];
            $removepId = $_POST["removepId"];
            $this->deleteSongFromOnePlaylistSongs($newSongId,$removepId);
            if (isset($_GET["u"])) {
                if (isset($_GET["songs"])) {
                    if (isset($x)) {
                        echo "<script>window.location.href = './account.php?u=$x&songs';</script>"; //header nie dziala :(
                    }
                }
            } else if (isset($_GET["playlists"])) {
                if (isset($x)) {
                    echo "<script>window.location.href = './account.php?u=$x&playlists';</script>"; //header nie dziala :(
                }
            } else {
                if (isset($pId)) {
                    echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
                }
            }
        }
        if (isset($_POST["changeSongName"]) && !empty($_POST["changeSongName"])) {
            $newSongName = $_POST["changeSongName"];
            $newSongId = $_POST["changeSongId"];
            $this->changeSongName($newSongName, $newSongId);
            if (isset($_GET["u"])) {
                if (isset($_GET["songs"]))
                if (isset($x)) {
                    echo "<script>window.location.href = './account.php?u=$x&songs'</script>;"; //header nie dziala :(
                }
            } else if (isset($_GET["playlists"])) {
                if (isset($x)) {
                    echo "<script>window.location.href = './account.php?u=$x&playlists';</script>"; //header nie dziala :(
                }
            } else {
                if (isset($pId)) {
                    echo "<script>window.location.href = './index.php?x=$pId';</script>"; 
                }
            }
        }
        }
    //}
    }
    public function displayPlaylistsHtml() 
    {
        $playlist = $this->userPlaylist();
        $sortedPlaylist = $this->sortedPlaylists();
        $likedPlaylists = $this->getUserLikedPlaylists();
        $likedSongsId = $this->getUserLikedSongs($_SESSION["username"]);
        $s = $_SESSION["username"];
        if (isset($_GET["x"])) {
        $currentPlaylistId = $_GET["x"];
        }
        else if (isset($_GET["u"])) {
            $u = $_GET["u"];
        } else if (isset($_GET["query"])) {
            $query = $_GET["query"];
        }
 
        echo "<div class=\"playlistsContainer\">";
        echo "<table class=\"playlistTable\">";
        echo "<thead>";
        echo "</thead>";
        echo "<tbody>";


        if (isset($_POST["sort"])) {
        foreach ($sortedPlaylist as $p) {
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" hx-trigger=\"click\" hx-get=\"./htmx/testIndex.php?x=$p->playlist_id\" hx-headers=\"{'X-Session-Data': '$s'}\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" class=\"click\">" . $p->playlist_name . "<div class=\"pTypeDisplay\">$p->playlistType Playlist</div>" . "</a>" . "</td>";
            echo "</tr>";
        }
        } else {              
            echo "<script src=\"./JS/validation/playlistNameChangeValidate.js\"></script>";   
            foreach ($playlist as $p) {
                $playlistId = $p["playlist_id"];
                $playlistName = $p["playlist_name"];
                $playlistType = $p["playlistType"];
                echo "<tr class=\"s1\">";
                echo "<td class=\"songId\">" . "<a href=\"index.php?x=$playlistId\" hx-trigger=\"click\" hx-get=\"./htmx/testIndex.php?x=$playlistId\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" hx-push-url=\"index.php?x=$playlistId\" class=\"click\">" . $playlistName . "<div class=\"pTypeDisplay\">$playlistType Playlist</div>" .
                "</a>". "<form method=\"post\" class=\"nameChange\" onsubmit=\"return validateNameChangeForm(event)\">" .
                "<input type=\"hidden\" name=\"playlist_id\" value=\"$playlistId\">" .
                "<button type=\"button\" name=\"delPlaylist\" class=\"delPlaylist\" onclick=\"deletePlaylist(this)\">Delete</button>" .
                "<input type=\"text\" name=\"renamePlaylist\" class=\"rename\" placeholder=\"Rename\" oninput=\"handleRenameInput(this)\">" .
                "</form>" . "</td>";
                echo "</tr>";
            }
            

            if (isset($_POST['playlist_id'])) {
                $playlistId = $_POST['playlist_id'];
                if ($playlistId == $currentPlaylistId) {
                    $same = true;
                }
                if (isset($_POST['delPlaylist'])) {
                    $this->deletePlaylist($playlistId);
                } else if (isset($_POST['renamePlaylist']) && !empty(trim($_POST['renamePlaylist']))) {
                    $newName = $_POST['renamePlaylist'];
                    $this->renamePlaylist($playlistId, $newName);
                }
                if (isset($_GET["x"])) {
                    if ($same == true) {
                        echo "<script>window.location.href = './index.php';</script>"; 
                    } else {
                        echo "<script>window.location.href = './index.php?x=$currentPlaylistId';</script>"; 
                    }
                } else if (isset($_GET["u"])) {
                    if (isset($_GET["songs"])) {
                        echo "<script>window.location.href = './account.php?u=$u&songs';</script>"; 
                    } else if (isset($_GET["playlist"])) {
                        echo "<script>window.location.href = './account.php?u=$u&playlists';</script>"; 
                    } else {
                    echo "<script>window.location.href = './account.php?u=$u';</script>"; 
                    }
                } else if (isset($_GET["query"])) {
                    echo "<script>window.location.href = './search.php?query=$query';</script>"; 
                } else {
                    echo "<script>window.location.href = './index.php';</script>"; 
                }
            }
    }
        echo "<tr>";
        echo "<td id=\"liked\">Liked playlists</td>";
        echo "</tr>";
        foreach ($likedPlaylists as $p) {
            $pName = $p["playlist_name"];
            $pId = $p["playlist_id"];
            $pType = $p["playlistType"];
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$pId\" hx-replace-url=\"index.php?x=$pId\" hx-trigger=\"click\" hx-get=\"./htmx/testIndex.php?x=$pId\" hx-headers=\"{'X-Session-Data': '$s'}\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" class=\"click\" hx-push-url=\"index.php?x=$pId\">" . $pName . "<div class=\"pTypeDisplay\">$pType Playlist</div>" . 

            "</a>" . "<form method=\"post\">" .
            "<input type=\"hidden\" name=\"playlist_id\" value=\"$pId\">" .
            "<button name=\"remPlaylist\" class=\"remPlaylist\">Remove</button>" .
            "</form>" . "</td>";
            echo "</tr>";
            if (isset($_POST["remPlaylist"])) {
                $this->removeFromFavorites($_POST["playlist_id"], $this->getUserIdByName($_SESSION["username"]));
            if (isset($_GET["x"])) {
                if ($same == true) {
                    echo "<script>window.location.href = './index.php';</script>"; 
                } else {
                    echo "<script>window.location.href = './index.php?x=$playlistId';</script>"; 
                }
            } else if (isset($_GET["u"])) {
                if (isset($_GET["songs"])) {
                    echo "<script>window.location.href = './account.php?u=$u&songs';</script>"; 
                } else if (isset($_GET["playlist"])) {
                    echo "<script>window.location.href = './account.php?u=$u&playlits';</script>"; 
                } else {
                echo "<script>window.location.href = './account.php?u=$u';</script>"; 
                }
            } else if (isset($_GET["query"])) {
                //echo "<script>window.location.href = './search.php?query=$query';</script>"; 
            } else {
                echo "<script>window.location.href = './index.php';</script>"; 
            }
            }
        }
        //if (isset($_GET["x"])) {
            echo "<tr class=\"s1\">";       
            //$pId = $p["playlist_id"];
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$likedSongsId\" hx-trigger=\"click\" hx-get=\"./htmx/testIndex.php?x=$likedSongsId\" hx-headers=\"{'X-Session-Data': '$s'}\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" class=\"click\" hx-push-url=\"index.php?x=$likedSongsId\">" . "Liked Songs" . "<div class=\"pTypeDisplay\"></div>" . "</a>" . "</td>";
            echo "</tr>";
        //}
        echo "</tbody>";
        echo "</table>";
        echo "</div>";



}
}

    
?>