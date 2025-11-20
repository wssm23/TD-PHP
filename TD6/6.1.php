<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

if (!isset($_SESSION['username'])) {
    echo '
<form method="POST">
<label for="username">Username :</label>
<input type="text" id="username" name="username" required>
<button type="submit">Envoyer</button>
</form>
    ';
} else {
    echo "Bonjour " . htmlspecialchars($_SESSION['username']) . " !";
}
?>
