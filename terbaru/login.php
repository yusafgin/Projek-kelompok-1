<?php
session_start();
require 'functions.php';

if (isset($_POST["login"])) {
    global $conn;

    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Dekripsi password dari database
        $decryptedPassword = decryptRSA($row["password"]);

        if ($password === $decryptedPassword) {
            $_SESSION["login"] = true;
            header("Location: index.php");
            exit;
        }
    }

    $error = true;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>halaman login</title>
</head>
<body>
    
    <h1>halaman login</h1>

    <?php if(isset($error)): ?>
        <p style="color : red; font-style: italic">username / password salah</p>
    <?php endif; ?>

    <form action="" method="post">
        <ul>
            <li>
                <label for="username">username :</label>
                <input type="text" name="username" id="username">
            </li>
            <li>
                <label for="password">password :</label>
                <input type="password" name="password" id="password">
            </li>
            <li>
                <button type="submit" name="login">login</button>
            </li>
        </ul>
    </form>

</body>
</html>