<div id="top">
    <?php
        $userLikedId = $x->getUserLikedSongs();
    echo "
    <a href=\"./index.php?x=$userLikedId\" id=\"homeContainer\" hx-push-url=\"./index.php?x=$userLikedId\" hx-trigger=\"click\" hx-get=\"./htmx/test.php?x=$userLikedId\" hx-target=\"#middleTab\" hx-swap=\"innerHTML\">
        Home
    </a>
    ";
    ?>
    <a href="./account.php?u=<?=$_SESSION["username"]?>" hx-push-url="account.php?u=<?=$_SESSION["username"]?>" hx-trigger="click" hx-get="./htmx/test.php?u=<?=$_SESSION["username"]?>" hx-target="#middleTab" hx-swap="innerHTML" id="accountContainer">
        Account
    </a>
    <div id="searchContainer">
        <form method="post">
            <label for="searchQuery">Search</label>
            <input type="text" name="searchQuery">
        </form>
        <?php
            if (isset($_POST["searchQuery"])) {
            $s = $_POST["searchQuery"];
            echo "<script>window.location.href = './search.php?query=$s';</script>"; 
        }
        ?>
    </div>
</div> 