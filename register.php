<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
<h1 class="logtext">Register</h1>
    <form action="regHandler.php" class="fl" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <br>
        <button value="Register" class="send">Register</button>
    </form>
    <a href="login.php" class="loginf">Login to an existing account</a>
    <script src="mouseTracker.js">
    </script>
</body>
</html>