<?php
    class Songs {
    public $song_id;
    public $song_name;
    public $artist;
    public $length;

    public function getSongId() {
        return $this->song_id;
    }

    public function setSongId($song_id) {
        $this->song_id = $song_id;
    }

    public function getSongName() {
        return $this->song_name;
    }

    public function setName($song_name) {
        $this->song_name = $song_name;
    }

    public function getArtist() {
        return $this->artist;
    }

    public function setArtist($artist) {
        $this->artist = $artist;
    }
    public function getLength() {
        return $this->length;
    }

    public function setLength($length) {
        $this->length = $length;
    }
    }
?>