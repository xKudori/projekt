<?php
session_start();
require("./db.php");

$x = new Db_Connection("localhost","music_site","root","");
$u = $_GET["u"];
if (isset($_GET["playlists"])) {
echo "
<table id=\"userPublicPlaylists\">
<thead>
    <tr>
        <th>$u's Public Playlists</th>
    </tr>
</thead>
<tbody>";
    $x->displayPublicPlaylists();
    echo "
</tbody>
</table>
";
} else if (isset($_GET["songs"])) {
    $x->songDisplayHtml();
}
?>

<script src="./audioPlayer.js">
    </script>