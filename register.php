<?php
include('function/userstorage.php');
include('function/auth.php');

function validate(&$data, &$errors)
{
    if (isset($_POST["username"]) && trim($_POST["username"]) !== "") {
        $data["username"] = $_POST["username"];
    } else {
        $errors["username"] = "Felhasználó név megadása kötelező!";
    }

    if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "E-mail megadása kötelező!";
    } else {
        $data["email"] = $_POST['email'];
    }


    if (isset($_POST["password"]) && trim($_POST["password"]) !== "") {
        $data["password"] = $_POST["password"];
    } else {
        $errors["password"] = "Jelszó megadása kötelező!";
    }

    if (isset($_POST["password2"]) && trim($_POST["password2"]) !== "") {
        $data["password2"] = $_POST["password2"];
    } else {
        $errors["password2"] = "Jelszó megadása kötelező!";
    }

    if (isset($_POST["password"]) && isset($_POST["password2"]) && $_POST["password"] !== $_POST["password2"]) {
        $errors["global"] = "A két jelszó nem egyezik! ";
    }

    return count($errors) === 0;
}

$userStorage = new UserStorage();
$auth = new Auth($userStorage);
$errors = [];
$data = [];
if (count($_POST) > 0) {
    if (validate($data, $errors)) {
        if ($auth->user_exists($data['username'])) {
            $errors['global'] = "User already exists. ";
        } else {
            $auth->register($data);
            header("Location: login.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Regisztráció</h1>
    </header>
    <form action="" method="post">
        <p><?= $errors['global'] ?? "" ?></p>
        <label for="username">Felhasználó név:</label>
        <input type="text" name="username" value="<?= $data["username"] ?? "" ?>">
        <small><?= $errors["username"] ?? ""  ?></small>
        <br>

        <label for="email">E-mail cím:</label>
        <input type="email" name="email" value="<?= $data["email"] ?? "" ?>" />
        <small><?= $errors["email"] ?? ""  ?></small>
        <br>

        <label for="password">Jelszó:</label>
        <input type="password" name="password" value="<?= $data["password"] ?? "" ?>">
        <small><?= $errors["password"] ?? ""  ?></small>
        <br>

        <label for="password2">Jelszó megerősítése:</label>
        <input type="password" name="password2" value="<?= $data["password2"] ?? "" ?>">
        <small><?= $errors["password2"] ?? ""  ?></small>
        <br>

        <button type="submit">Regisztráció</button>
    </form>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>

</html>