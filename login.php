<?php
include('function/userstorage.php');
include('function/auth.php');
session_start();

function validate(&$data, &$errors)
{
    if (isset($_POST["username"]) && trim($_POST["username"]) !== "") {
        $data["username"] = $_POST["username"];
    } else {
        $errors["username"] = "Felhasználó név megadása kötelező!";
    }

    if (isset($_POST["password"]) && trim($_POST["password"]) !== "") {
        $data["password"] = $_POST["password"];
    } else {
        $errors["password"] = "Jelszó megadása kötelező!";
    }

    return count($errors) === 0;
}

$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$errors = [];
$data = $_SESSION["user"] ?? [];

if (count($_POST) > 0) {
    if (validate($data, $errors)) {
        if ($auth->user_exists($data["username"])) {
            $user = $auth->authenticate($data["username"], $data["password"]);
            if ($user == NULL) {
                $errors["global"] = "Felhasználóhoz tartozó jelszó helytelen!";
            }   
            else {
                $auth->login($user);
                header("Location: index.php");
            }
        } 
        else {
            $errors["global"] = "Nincs a megadott névhez tartozó felhasználó!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Bejelentkezés</h1>
    </header>
    <?php if (!$auth->is_authenticated()) : ?>
        <p><?= $errors["global"] ?? "" ?></p>
        <form action="" method="post">
            <label for="username">Felhasználó név:</label>
            <input type="text" name="username" id="username" value="<?= $data["username"] ?? "" ?>">
            <small><?= $errors["username"] ?? ""  ?></small>
            <br>
            <label for="password">Jelszó:</label>
            <input type="password" name="password" id="password" value="<?= $data["password"] ?? "" ?>">
            <small><?= $errors["password"] ?? ""  ?></small>
            <br>
            <button type="submit">Bejelentkezés</button>
        </form>
    <?php endif ?>
    <form action="register.php" method="post">    
        <button type="submit">Regisztráció</button>
    </form>

    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>

</html>