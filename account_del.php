<?php
session_start();
require("./db.php");

$dataObj = new SQL_Functions("localhost","music_site","root","");
$u = $_SESSION["username"];

$dataObj->deleteUser($u);
header("Location: ./logout.php");

unset($_SESSION["username"]);

?>