<?php
    class Playlists 
    {
        public $playlist_id;
        public $playlist_name;
        public $numOfSongs;
        public $createdBy;
        public $playlist_type;

        public function getPlaylistId() {
            return $this->playlist_id;
        }
    
        public function setPlaylistId($playlist_id) {
            $this->playlist_id = $playlist_id;
        }

        public function getPlaylistName() {
            return $this->playlist_name;
        }

        public function setPlaylistName($playlist_name) {
            $this->playlist_name = $playlist_name;
        }
        
        public function getNumOfSongs() {
            return $this->numOfSongs;
        }

        public function setNumOfSongs($numOfSongs) {
            $this->numOfSongs = $numOfSongs;
        }

        public function getCreatedBy() {
            return $this->createdBy;
        }

        public function setCreatedBy($createdBy) {
            $this->createdBy = $createdBy;
        }

        public function getPlaylistType() {
            return $this->playlist_type;
        }

        public function setPlaylistType($playlist_type) {
            $this->playlist_type = $playlist_type;
        }
    }
    
?>