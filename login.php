<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="./images/misc/moon3.png">
    <title>LunaChord</title>
</head>
<body>
<h1 class="logtext"> Login into <br> your account</h1>
    <form action="./handlers/loginHandler.php" class="fl" method="post">
        <label for="Uname" id="Uname">Username</label>
        <br>
        <input type="text" name="Uname" class="loginFormInput">
        <br>
        <label for="password">Password</label> 
        <br>
        <input type="password" name="password" class="loginFormInput">
        <br>
        <br>
        <button name="send" class="send">Send</button>
    </form>
    <a href="register.php" class="loginf">Register a new account</a>
    <div class="logo">
        <img src="./images/misc/moon.png" alt="moon" class="logo">
        <p class="luna">LunaChord</p>
    </div>
        <script src="./JS/other/mouseTracker.js">
    </script>
    <script src="./JS/validation/loginValidate.js"></script>
</body>
</html>