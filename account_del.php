<?php
session_start();
require("./db.php");

$x = new Db_Connection("localhost","music_site","root","");
$u = $_POST["username"];

if (isset($_POST["deleteUser"])) {
    $x->deleteUser($u);
    header("Location: ./logout.php");
}


?>