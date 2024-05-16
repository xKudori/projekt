<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="moon3.png">
    <title>LunaChord</title>
</head>
<body>
<h1 class="logtext">Register</h1>
    <form action="regHandler.php" class="fl" method="post">
        <label for="username">Username:</label>
        <br>
        <input type="text" id="username" name="username" required class="loginFormInput">
        <br>
        <label for="password">Password:</label>
        <br>
        <input type="password" id="password" name="password" required class="loginFormInput">
        <br>
        <label for="email">Email:</label>
        <br>
        <input type="email" id="email" name="email" required class="loginFormInput">
        <br>
        <br>
        <button value="Register" class="send">Register</button>
    </form>
    <a href="login.php" class="loginf">Login to an existing account</a>
    <div class="logo">
        <img src="moon.png" alt="moon" class="logo">
        <p class="luna">LunaChord</p>
    </div>
    <script src="mouseTracker.js">
    </script>
</body>
</html>