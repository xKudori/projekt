-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2024 at 08:38 PM
-- Wersja serwera: 10.4.28-MariaDB
-- Wersja PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `music_site`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `playlists`
--

CREATE TABLE `playlists` (
  `playlist_id` int(11) NOT NULL,
  `playlist_name` varchar(255) NOT NULL,
  `numOfSongs` int(11) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `playlistType` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlists`
--

INSERT INTO `playlists` (`playlist_id`, `playlist_name`, `numOfSongs`, `createdBy`, `playlistType`) VALUES
(1, 'Example_Playlist_Name', 23, 'fdbdf', 'Public'),
(2, 'Example_playlist2', 3, 'user2', 'Private'),
(37, 'Test_Playlist', 0, '', 'Public'),
(64, 'nazwa', 0, '', 'Local'),
(65, 'grdcf', 0, '', 'Local'),
(66, 'hcvgt', 0, '', 'Private'),
(74, ' nhij', 0, '', 'Local'),
(75, 'hj,b', 0, '', 'Local'),
(76, 'ghj,b', 0, '', 'Local'),
(77, 'thfgv', 0, '', 'Local');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `songs`
--

CREATE TABLE `songs` (
  `song_id` int(11) NOT NULL,
  `song_name` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `length` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `playlist_name` varchar(255) NOT NULL,
  `audio_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`song_id`, `song_name`, `artist`, `length`, `playlist_id`, `playlist_name`, `audio_path`) VALUES
(1, 'exampleName', 'exampleArtist', 233, 0, '', ''),
(2, 'songName4', 'artistName4', 45, 2, '', ''),
(3, 'exampleSong4', 'exampleArtist4', 128, 2, '', ''),
(4, 'exampleName5', 'exampleArtist5', 452, 1, '', ''),
(59, 'test', '', 0, 64, 'nazwa', './audio/midwest emo.mp3'),
(60, 'testSong', '', 0, 74, ' nhij', './audio/midwest emo.mp3'),
(61, 'test', '', 0, 77, 'thfgv', './audio/midwest emo.mp3'),
(62, 'piano', '', 0, 64, 'nazwa', './audio/calkiem dobre piano.mp3'),
(63, 'test', '', 0, 64, 'nazwa', './audio/calkiem dobre piano.mp3'),
(64, 'yghjv', '', 0, 64, 'nazwa', './audio/gitara sample.mp3'),
(65, 'dhcfgt', '', 0, 77, 'thfgv', './audio/piano demo.mp3');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`playlist_id`);

--
-- Indeksy dla tabeli `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`song_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `playlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `song_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
