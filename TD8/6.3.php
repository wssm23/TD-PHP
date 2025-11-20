<?php
session_start();
$errors = [];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=dz;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (isset($_POST["register"])) {
    $username = trim($_POST["reg_username"]);
    $password = trim($_POST["reg_password"]);

    if (empty($username)) {
        $errors[] = "Le champ username de l’inscription est vide.";
    }
    if (empty($password)) {
        $errors[] = "Le champ password de l’inscription est vide.";
    }

    if (empty($errors)) {
        $query = $pdo->prepare("SELECT * FROM po WHERE username = ?");
        $query->execute([$username]);
        $user = $query->fetch();

        if ($user) {
            $errors[] = "Ce username est déjà utilisé.";
        }
    }

    if (empty($errors)) {
        $query = $pdo->prepare("INSERT INTO po(username, password) VALUES (?, ?)");
        $query->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        echo "<p style='color:green;'>Inscription réussie !</p>";
    }
}

if (isset($_POST["login"])) {
    $username = trim($_POST["log_username"]);
    $password = trim($_POST["log_password"]);

    if (empty($username)) {
        $errors[] = "Le champ username de la connexion est vide.";
    }
    if (empty($password)) {
        $errors[] = "Le champ password de la connexion est vide.";
    }

    if (empty($errors)) {
        $query = $pdo->prepare("SELECT * FROM po WHERE username = ?");
        $query->execute([$username]);
        $user = $query->fetch();

        if (!$user) {
            $errors[] = "Le username n’existe pas dans la base de données.";
        } else if (!password_verify($password, $user["password"])) {
            $errors[] = "Le mot de passe est invalide.";
        } else {
            $_SESSION["user"] = $user["username"];
            echo "<p style='color:green;'>Connexion réussie !</p>";
        }
    }
}
?>

<h2>Inscription</h2>
<form method="post">
    <input type="text" name="reg_username" placeholder="Username">
    <input type="password" name="reg_password" placeholder="Password">
    <button type="submit" name="register">S'inscrire</button>
</form>

<h2>Connexion</h2>
<form method="post">
    <input type="text" name="log_username" placeholder="Username">
    <input type="password" name="log_password" placeholder="Password">
    <button type="submit" name="login">Se connecter</button>
</form>

<?php
if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<p style='color:red;'>$e</p>";
    }
}
?>