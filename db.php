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

    private function getSongIdFromPlaylistSongs() {
        if (isset($_GET["x"]) && !isset($_GET["u"])) 
        {
            $pId = $_GET["x"]; 
            $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id=$pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
        } else if (!isset($_GET["x"]) && isset($_GET["u"])){
            $pId = $this->getPlaylistIdByName($_GET["u"]); 
            $sql = $this->pdo->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id = $pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
            return $sql->fetchAll();
        } 
    } 
    private function song() {
        if (isset($_GET["x"]) || isset($_GET["u"])) {
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

    public function isUserPlaylist($playlistId) {
        $user = $_SESSION["username"];
        $sql = $this->pdo->prepare("SELECT COUNT(*) AS count FROM playlists WHERE playlist_id = :playlistId AND user = :user");
        $sql->bindParam(":playlistId", $playlistId, PDO::PARAM_INT);
        $sql->bindParam(":user", $user, PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    private function getUserIdByName($user) {
        $sql = $this->pdo->prepare("SELECT user_id FROM users WHERE username = :user");
        $sql->bindParam(":user", $user, PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['user_id'];
    }

    private function removeFromFavorites($playlistId, $userId) {
        $sql = $this->pdo->prepare("DELETE FROM playlist_likes WHERE playlist_id = :playlist_id AND user_id = :user_id");
        $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
        $sql->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $sql->execute();
    }

    private function deletePlaylist($playlist) {
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

    private function renamePlaylist($playlistId, $newName) {
        if (empty($newName)) {
            throw new Exception("Nazwa playlisty nie może być pusta.");
        }
    
        $sql = $this->pdo->prepare("UPDATE playlists SET playlist_name = :new_name WHERE playlist_id = :playlist_id");
        $sql->bindParam(":new_name", $newName, PDO::PARAM_STR);
        $sql->bindParam(":playlist_id", $playlistId, PDO::PARAM_INT);
    
        if ($sql->execute()) {
            return "Nazwa playlisty została zmieniona.";
        } else {
            throw new Exception("Wystąpił problem podczas zmiany nazwy playlisty.");
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

    private function pTypeFromId($pId) {
            $sql = $this->pdo->prepare("SELECT playlistType FROM playlists WHERE playlist_id = $pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Songs");
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
    
            /*
            if ($_GET["u"] != "admin") {
                echo "<script>window.location.href = './login.php';</script>";
            }
            */
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
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
            echo "<td class=\"userTd\"><a href=\"./account.php?u=$username\" hx-push-url=\"account.php?u=$username\" hx-trigger=\"click\" hx-get=\"./test.php?u=$username\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\">$username</a></td>";
            echo "</tr>";

        }
        echo "</tbody>";
        echo "</table>";
    }



    public function songDisplayHtml() 
    { 
        $songs = $this->song();
        $searchedSongs = $this->getSearchQuerySongs();
        $a = 0;

        if (isset($_GET["x"]) && !isset($_GET["u"])) { 
            $x = $_GET["x"];
            $pType = $this->pTypeFromId($x);
            foreach ($pType as $pT) {
                $pT->playlistType;
            }
        
            if ($pT->playlistType == "Local") {
                $tempVal = "Local";
            } else {
                $tempVal = "Public/Private";
            } 
            
        } else if (isset($_GET["u"]) && !isset($_GET["x"])) {
            $x = $_GET["u"];
            $pType = "User";
            $tempVal = "User";
        }
        $defaultPlaylistParam = 1;
        if (!isset($_POST["query"])) {
            $defaultPlaylistParam = sizeof($songs);
        }
        if ($defaultPlaylistParam == 0) {
            echo "Playlist is empty";
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
    if (isset($pType) && !isset($_GET["query"])) {
        foreach ($songs as $song) {
            global $a;
            $a = $a + 1;
            if (!isset($_GET["u"])) {
                $pId = $_GET["x"];
            }
            $songId = $song->song_id;
            $songName = $song->song_name;
            $songPath = $song->audio_path;
            $songImagePath = $song->image_path;
            echo "<tr class=\"s1\">";
            echo "<td class=\"songId\">" . 
            "<button name=\"play\" type = \"button\"class=\"play\" value=\"$songPath\">". $a .
            "<input type=\"hidden\" name=\"jsName\" value=\"$songName\" class=\"jsName\">". 
            "<input type=\"hidden\" name=\"jsImage\" value=\"$songImagePath\" class=\"jsImage\">". 
            "</button>" ."</td>";
            echo "<td>" . $song->song_name . "</td>";
            echo "<td>" . $song->user . "</td>";
            echo "<td class=\"songLength\" data-audio=\"$songPath\"></td>";
            echo "<script src=\"./songLength.js\"></script>";
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

                echo "<tr class=\"s1\">";
                echo "<form method=\"post\">";
                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">$p->playlist_name</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
                echo "</form>";
                echo "</tr>";
            }
        } else if ($tempVal == "Public/Private" || $tempVal == "User") {
            foreach ($publicOrPrivatePlaylist as $p) {

                echo "<tr class=\"s1\">";
                echo "<form method=\"post\">";
                echo "<td class=\"songId\">"; 
                if ($tempVal == "Public/Private") {
                    echo "<button name=\"insertIntoLiked\" class=\"localTitle\">Like Song</button>" . "</td>";
                }
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">$p->playlist_name</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
                echo "</form>";
                echo "</tr>";
        }
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
            } else if ($tempVal == "Public/Private" && $this->isUserPlaylist($_GET["x"]) == false) {
                echo "<form method=\"post\">";
                echo "<tr class=\"s1\">";
                echo "<input type=\"hidden\" name=\"changeSongId\" value=\"$songId\"></input>";
                echo "<input type=\"hidden\" name=\"removepId\" value=\"$pId\"></input>";
                echo "</tr>";
            }
            else {
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
    } else if (!isset($pType) && (isset($_GET["query"]) || isset($_POST["query"]))) {
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
            echo "<button name=\"insertIntoLiked\" class=\"localTitle\">Like Song</button>" . "</td>";
            echo "</form>";
            echo "</tr>";

            if ($_SESSION["username"] == "admin") {
                echo "<form method=\"post\">";
                echo "<button name=\"deleteSong\" class=\"localTitle\" value=\"$songId\">Delete Song</button>"; 
                echo "</form>";
            }
            foreach ($publicOrPrivatePlaylist as $p) {

                echo "<tr class=\"s1\">";
                echo "<form method=\"post\">";
                echo "<td class=\"songId\">"; 
                echo "<button name=\"insertIntoPlaylist\" class=\"localTitle\" value=\"$p->playlist_name\">$p->playlist_name</button>" . "</td>";
                echo "<input type=\"hidden\" name=\"insertSongId\" value=\"$songId\"></input>";
                //echo "<input type=\"hidden\" name=\"tempVal\" value=\"$tempVal\"></input>";
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
                $likeId = $this->getUserLikedSongs();
                $this->addSongToPlaylist($songId,$likeId);
            }
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


    private function playlist_name() 
    {
        if ((isset($_GET["x"]) && !isset($_GET["u"]) && !isset($_SESSION["playlist"])) || (isset($_GET["x"]) && !isset($_GET["u"]) && isset($_SESSION["playlist"]))) 
        {
            $pId = $_GET["x"];
            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=$pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
            return $sql->fetchAll();
        } else if (!isset($_GET["x"]) && !isset($_GET["u"]) && !isset($_SESSION["playlist"])) {
            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=0");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
            return $sql->fetchAll();
        } else if (!isset($_GET["x"]) && !isset($_GET["u"]) && isset($_SESSION["playlist"])) {
            $pId = $_SESSION["playlist"];
            $sql = $this->pdo->prepare("SELECT playlist_name FROM playlists WHERE playlist_id=$pId");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
            return $sql->fetchAll();
        } 
    }

    public function playlistNameDisplayHtml() 
    {
        if ((!isset($_GET["u"]) && isset($_GET["x"])) || (isset($_SESSION["playlist"]))) {
            $pname = $this->playlist_name();
            foreach ($pname as $playlistName) {    
                $playlistName->playlist_name;    
            }
            $name = $playlistName->playlist_name;
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

    private function playlists() 
    {
        $sql = $this->pdo->prepare("SELECT * FROM playlists");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function userPublicPlaylists() {
        $user = $_GET["u"];
        $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user = (:user) AND playlistType = \"Public\"");
        $sql->bindParam(":user", $user, PDO::PARAM_STR);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function userPlaylist() {
        $user = $_SESSION["username"];
        $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user = (:user) AND playlistType NOT LIKE \"User\" AND playlistType NOT LIKE \"Liked\"");
        $sql->bindParam(":user", $user, PDO::PARAM_STR);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    private function getUserSongs($songId) {
        
        $user = $_SESSION["username"];
        if (isset($_GET["u"])) {
            $pname = $_GET["u"];
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
        $user = $_SESSION["username"];
        $sql = $this->pdo->prepare("SELECT * FROM playlists WHERE user LIKE \"$user\" ORDER BY playlistType");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_CLASS,"Playlists");
        return $sql->fetchAll();
    }

    public function getUserLikedSongs() {
        $user = $_SESSION["username"];
        $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE user LIKE \"$user\" AND playlistType LIKE \"Liked\"");
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["playlist_id"];
    }

    public function displayPlaylistsHtml() 
    {
        $playlist = $this->userPlaylist();
        $sortedPlaylist = $this->sortedPlaylists();
        $likedPlaylists = $this->getUserLikedPlaylists();
        $likedSongsId = $this->getUserLikedSongs();
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
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" hx-trigger=\"click\" hx-get=\"./testIndex.php?x=$p->playlist_id\" hx-headers=\"{'X-Session-Data': '$s'}\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" class=\"click\">" . $p->playlist_name . "<div class=\"pTypeDisplay\">$p->playlistType Playlist</div>" . "</a>" . "</td>";
            echo "</tr>";
        }
        } else {
            foreach ($playlist as $p) {
                echo "<tr class=\"s1\">";
                echo "<td class=\"songId\">" . "<a href=\"index.php?x=$p->playlist_id\" hx-trigger=\"click\" hx-get=\"./testIndex.php?x=$p->playlist_id\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" hx-push-url=\"index.php?x=$p->playlist_id\" class=\"click\">" . $p->playlist_name . "<div class=\"pTypeDisplay\">$p->playlistType Playlist</div>" .
                "</a>". "<form method=\"post\">" .
                "<input type=\"hidden\" name=\"playlist_id\" value=\"$p->playlist_id\">" .
                "<button name=\"delPlaylist\" class=\"delPlaylist\">Delete</button>" .
                "<input type=\"text\" name=\"renamePlaylist\" class=\"rename\" placeholder=\"Rename\">" .
                "</form>" . "</td>";
                echo "</tr>";
            }
            

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $playlistId = $_POST['playlist_id'];
                if ($playlistId == $currentPlaylistId) {
                    $same = true;
                }
                if (isset($_POST['delPlaylist']) && empty($_POST['renamePlaylist'])) {
                    $this->deletePlaylist($playlistId);
                } else if (isset($_POST['renamePlaylist']) && !empty($_POST['renamePlaylist'])) {
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
                        echo "<script>window.location.href = './account.php?u=$u&playlits';</script>"; 
                    } else {
                    echo "<script>window.location.href = './account.php?u=$u';</script>"; 
                    }
                } else if (isset($_GET["query"])) {
                    echo "<script>window.location.href = './searcj.php?query=$query';</script>"; 
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
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$pId\" hx-replace-url=\"index.php?x=$pId\" hx-trigger=\"click\" hx-get=\"./testIndex.php?x=$pId\" hx-headers=\"{'X-Session-Data': '$s'}\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" class=\"click\" hx-push-url=\"index.php?x=$pId\">" . $pName . "<div class=\"pTypeDisplay\">$pType Playlist</div>" . 

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
                echo "<script>window.location.href = './searcj.php?query=$query';</script>"; 
            } else {
                echo "<script>window.location.href = './index.php';</script>"; 
            }
            }
        }
        //if (isset($_GET["x"])) {
            echo "<tr class=\"s1\">";       
            //$pId = $p["playlist_id"];
            echo "<td class=\"songId\">" . "<a href=\"index.php?x=$likedSongsId\" hx-trigger=\"click\" hx-get=\"./testIndex.php?x=$likedSongsId\" hx-headers=\"{'X-Session-Data': '$s'}\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\" class=\"click\" hx-push-url=\"index.php?x=$likedSongsId\">" . "Liked Songs" . "<div class=\"pTypeDisplay\"></div>" . "</a>" . "</td>";
            echo "</tr>";
        //}
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

    private function getPlaylistType($pName) 
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
                $pName = $_POST["insertPlaylist"];
                $p = $this->getPlaylistType($pName);
                foreach ($p as $b) {
                    $b["playlistType"];
                }
                    $temp = $b["playlistType"];
                if ($temp == "Local") {
                    $songName = $_POST["songName"];
                    $audioFileTmpName = $_FILES["audio-file"]["tmp_name"];
                    $audioFileName = "./audio/".$_FILES["audio-file"]["name"];
                    move_uploaded_file($audioFileTmpName,$audioFileName);
                    
                    $imageTmpName = $_FILES["image"]["tmp_name"];
                    $imageFileName = "./images/songImages/".$_FILES["image"]["name"];
                    move_uploaded_file($imageTmpName, $imageFileName);
                    
                    $user = $_SESSION["username"];

                    $sql = $this->pdo->prepare("INSERT INTO songs (song_name, audio_path, image_path, user) VALUES (:song_name, :audio_path, :image_path, :user)");
                    $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
                    $sql->bindParam(":audio_path", $audioFileName, PDO::PARAM_STR);
                    $sql->bindParam(":image_path", $imageFileName, PDO::PARAM_STR);
                    $sql->bindParam(":user", $user, PDO::PARAM_STR);

                    $sql->execute();

                    $songId = $this->pdo->lastInsertId();

                    $playlistId = $this->getPlaylistIdByNameUserVer($_POST["insertPlaylist"]);
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
            $imageTmpName = $_FILES["image"]["tmp_name"];
            $imageFileName = "./images/songImages/".$_FILES["image"]["name"];
            move_uploaded_file($imageTmpName, $imageFileName);
                    
            $user = $_SESSION["username"];

            $sql = $this->pdo->prepare("INSERT INTO songs (song_name, audio_path, image_path, user) VALUES (:song_name, :audio_path, :image_path, :user)");
            $sql->bindParam(":song_name", $songName, PDO::PARAM_STR);
            $sql->bindParam(":audio_path", $audioFileName, PDO::PARAM_STR);
            $sql->bindParam(":image_path", $imageFileName, PDO::PARAM_STR);
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
    
    private function getPlaylistIdByNameUserVer($playlistName) {
        $sql = $this->pdo->prepare("SELECT playlist_id FROM playlists WHERE playlist_name = :playlist_name AND user = :user");
        $sql->bindParam(":playlist_name", $playlistName, PDO::PARAM_STR);
        $sql->bindParam(":user", $_SESSION["username"], PDO::PARAM_STR);
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
        if (isset($_GET["u"])) {
            $pname = $_GET["u"];
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
        if (isset($_GET["u"])) {
            $pname = $_GET["u"];
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
        {/*
            echo "<button name=\"btn1\" class=\"button\">Upload Song</button>
            <br>
            <button name=\"btn2\" class=\"button\">Upload files to Local Playlist</button>";
        */}
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