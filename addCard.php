<?php
include('function/cardstorage.php');
include("function/userstorage.php");
include('function/auth.php');
session_start();

function validate(&$data, &$errors)
{

    if (isset($_POST["name"]) && trim($_POST["name"]) !== "") {
        $data["name"] = $_POST["name"];
    } else {
        $errors["name"] = "Név megadása kötelező!";
    }

    if (isset($_POST["type"]) && trim($_POST["type"]) !== "") {
        $data["type"] = $_POST["type"];
    } else {
        $errors["type"] = "Típus megadása kötelező!";
    }

    if (isset($_POST["hp"]) && trim($_POST["hp"]) !== "") {
        if ($_POST["hp"] < 10) {
            $errors["hp"] = "HP nem lehet kisebb, mint 10!";
        }
        $data["hp"] = $_POST["hp"];
    } else {
        $errors["hp"] = "HP megadása kötelező!";
    }

    if (isset($_POST["attack"]) && trim($_POST["attack"]) !== "") {
        if ($_POST["attack"] < 10) {
            $errors["attack"] = "Támadás nem lehet kisebb, mint 10!";
        }
        $data["attack"] = $_POST["attack"];
    } else {
        $errors["attack"] = "Támadás megadása kötelező!";
    }

    if (isset($_POST["defense"]) && trim($_POST["defense"]) !== "") {
        if ($_POST["defense"] < 10) {
            $errors["defense"] = "Védekezés nem lehet kisebb, mint 10!";
        }
        $data["defense"] = $_POST["defense"];
    } else {
        $errors["defense"] = "Védekezés megadása kötelező!";
    }

    if (isset($_POST["price"]) && trim($_POST["price"]) !== "") {
        if ($_POST["price"] < 50) {
            $errors["price"] = "Ár nem lehet kisebb, mint 50!";
        }
        $data["price"] = $_POST["price"];
    } else {
        $errors["price"] = "Ár megadása kötelező!";
    }

    if (isset($_POST["description"]) && trim($_POST["description"]) !== "") {
        $data["description"] = $_POST["description"];
    } else {
        $errors["description"] = "Leírás megadása kötelező!";
    }

    if (isset($_POST["image"]) && trim($_POST["image"]) !== "") {
        $data["image"] = $_POST["image"];
    } else {
        $errors["image"] = "Kép megadása kötelező!";
    }

    return count($errors) === 0;
}

$errors = [];
$data = [];
$userStorage = new UserStorage();
$auth = new Auth($userStorage);
$user = null;

if ($auth->is_authenticated()) {
    $user = $auth->authenticated_user();
    if($user["username"] != "admin"){
        header("Location: index.php");
    }
} else {
    header("Location: logout.php");
}

if (count($_POST) > 0) {
    if (validate($data, $errors)) {
        $cardStorage = new CardStorage();
        foreach ($cardStorage->findAll() as $card) {
            if ($card["pokename"] == $data["pokename"]) {
                $errors["global"] = "Már létezik ilyen nevű Pokémon!";
                break;
            }
        }
        $id = "card" . (count($cardStorage->findAll()));
        $data["owner"] = "admin";
        $data["id"] = count($cardStorage->findAll());


        $cardStorage->add($data, $id);
        header("Location: index.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Új Pokémon</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Főoldal</a></li>
            <li><a href="logout.php">Kijelentkezés</a></li>
        </ul>
    </nav>
    <form action="" method="post">
        <p><?= $errors['global'] ?? "" ?></p>
        <label for="name">Neve: </label>
        <input type="text" name="name" value="<?= $data["name"] ?? "" ?>">
        <small><?= $errors["name"] ?? ""  ?></small>
        <br>

        <label for="type">Tipus: </label>
        <input type="text" name="type" value="<?= $data["type"] ?? "" ?>">
        <small><?= $errors["type"] ?? ""  ?></small>
        <br>

        <label for="hp">HP: </label>
        <input type="number" name="hp" value="<?= $data["hp"] ?? "" ?>" min="10">
        <small><?= $errors["hp"] ?? ""  ?></small>
        <br>

        <label for="attack">Támadás: </label>
        <input type="number" name="attack" value="<?= $data["attack"] ?? "" ?>" min="10">
        <small><?= $errors["attack"] ?? ""  ?></small>
        <br>

        <label for="defense">Védekezés: </label>
        <input type="number" name="defense" value="<?= $data["defense"] ?? "" ?>" min="10">
        <small><?= $errors["defense"] ?? ""  ?></small>
        <br>

        <label for="price">Ára: </label>
        <input type="number" name="price" value="<?= $data["price"] ?? "" ?>" min="50">
        <small><?= $errors["price"] ?? ""  ?></small>
        <br>

        <label for="description">Leírás: </label>
        <textarea name="description" ><?= $data["description"] ?? "" ?></textarea>
        <small><?= $errors["description"] ?? ""  ?></small>
        <br>

        <label for="image">Kép elérési útvonala: </label>
        <input type="text" name="image" value="<?= $data["image"] ?? "" ?>">
        <small><?= $errors["image"] ?? ""  ?></small>
        <br>
        <button type="submit">Felvétel</button>
    </form>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>

</html>