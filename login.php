<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
<h1 class="logtext"> Login into your account</h1>
    <form action="loginHandler.php" class="fl" method="post">
        <label for="Uname">Username</label>
        <input type="text" name="Uname">
        <label for="email">Email</label>
        <input type="text" name="email">
        <label for="password">Password</label> 
        <input type="password" name="password">
        <button name="send">Send</button>
    </form>
    <a href="register.php" class="loginf">Register a new account</a>
</body>
</html>