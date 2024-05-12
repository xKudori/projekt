<div id="top">
    <a href="./index.php" id="homeContainer">
        Home
    </a>
    <a href="./account.php?u=<?=$_SESSION["username"]?>" id="accountContainer">
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