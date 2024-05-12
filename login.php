<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
<h1 class="logtext"> Login into <br> your account</h1>
    <form action="loginHandler.php" class="fl" method="post">
        <label for="Uname" id="Uname">Username</label>
        <input type="text" name="Uname">
        <br>
        <label for="email">Email</label>
        <input type="text" name="email">
        <br>
        <label for="password">Password</label> 
        <input type="password" name="password">
        <br>
        <br>
        <button name="send" class="send">Send</button>
    </form>
    <a href="register.php" class="loginf">Register a new account</a>
    <script src="mouseTracker.js">
    </script>
</body>
</html>