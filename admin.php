<?php
include('function/cardstorage.php');
include("function/userstorage.php");
include('function/auth.php');
session_start();

$errors = [];
$adatok = [];
$data = [];

function validate(&$adatok, &$errors)
{
    if (isset($_POST["type"]) && trim($_POST["type"]) !== "") {
        $adatok["type"] = $_POST["type"];
    } else {
        $errors["type"] = "Típus megadása kötelező!";
    }

    if (isset($_POST["hp"]) && trim($_POST["hp"]) !== "") {
        if ($_POST["hp"] < 10) {
            $errors["hp"] = "HP nem lehet kisebb, mint 10!";
        }
        $adatok["hp"] = $_POST["hp"];
    } else {
        $errors["hp"] = "HP megadása kötelező!";
    }

    if (isset($_POST["attack"]) && trim($_POST["attack"]) !== "") {
        if ($_POST["attack"] < 10) {
            $errors["attack"] = "Támadás nem lehet kisebb, mint 10!";
        }
        $adatok["attack"] = $_POST["attack"];
    } else {
        $errors["attack"] = "Támadás megadása kötelező!";
    }

    if (isset($_POST["defense"]) && trim($_POST["defense"]) !== "") {
        if ($_POST["defense"] < 10) {
            $errors["defense"] = "Védekezés nem lehet kisebb, mint 10!";
        }
        $adatok["defense"] = $_POST["defense"];
    } else {
        $errors["defense"] = "Védekezés megadása kötelező!";
    }

    if (isset($_POST["price"]) && trim($_POST["price"]) !== "") {
        if ($_POST["price"] < 50) {
            $errors["price"] = "Ár nem lehet kisebb, mint 50!";
        }
        $adatok["price"] = $_POST["price"];
    } else {
        $errors["price"] = "Ár megadása kötelező!";
    }

    if (isset($_POST["description"]) && trim($_POST["description"]) !== "") {
        $adatok["description"] = $_POST["description"];
    } else {
        $errors["description"] = "Leírás megadása kötelező!";
    }

    if (isset($_POST["image"]) && trim($_POST["image"]) !== "") {
        $adatok["image"] = $_POST["image"];
    } else {
        $errors["image"] = "Kép megadása kötelező!";
    }

    return count($errors) === 0;
}

$userStorage = new UserStorage();
$auth = new Auth($userStorage);
$user = null;

$cardStorage = new CardStorage();
$cards = $cardStorage->findAll();

if ($auth->is_authenticated()) {
    $user = $auth->authenticated_user();
    if($user["username"] != "admin"){
        header("Location: index.php");
    }
} else {
    header("Location: logout.php");
}

if (count($_POST) > 0) {
    if (validate($adatok, $errors)) {
        $cardStorage = new CardStorage();
        $adatok["id"] = $_GET["kitolt"];
        $adatok["name"] = $cardStorage->findById("card" . $adatok["id"])["name"];
        $adatok["owner"] = "admin";

        $cardStorage->update("card" . $adatok["id"], $adatok);
        header("Location: admin.php");
    }
}
  

if (isset($_GET["kitolt"])) {
    $id = "card" . $_GET["kitolt"];
    $jelenlegi = $cardStorage->findById($id);

    $adatok = [
        "id" => $jelenlegi["id"],
        "name" => $jelenlegi["name"],
        "type" => $jelenlegi["type"],
        "hp" => $jelenlegi["hp"],
        "attack" => $jelenlegi["attack"],
        "defense" => $jelenlegi["defense"],
        "price" => $jelenlegi["price"],
        "description" => $jelenlegi["description"],
        "image" => $jelenlegi["image"]
    ];

}

$ownCards = array_filter($cards, function ($card) use ($user) {
    return $card["owner"] === $user["username"];
});

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/cards.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Módosítás</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Főoldal</a></li>
            <li><a href="logout.php">Kijelentkezés</a></li>
        </ul>
    </nav>

    <div id="content">
        <div id="card-list">
            <?php foreach ($ownCards as $card) : ?>

                <div class="pokemon-card">
                    <div class="image clr-<?=$card["type"]?>">
                        <a href="details.php?id=<?=$card["id"]?>"><img src="<?= $card["image"]?>" alt=""></a>
                    </div>
                    <div class="details">
                        <a href="details.php?id=<?=$card["id"]?>"><h2><?= $card["name"]?></h2></a>
                        <span class="card-type"><span class="icon">🏷 </span><?=$card["type"]?></span>
                        <span class="attributes">
                            <span class="card-hp"><span class="icon">❤ </span><?=$card["hp"]?></span>
                            <span class="card-attack"><span class="icon">⚔ </span><?=$card["attack"]?></span>
                            <span class="card-defense"><span class="icon">🛡 </span><?=$card["defense"]?></span>
                        </span>
                    </div>
                    <div class="buy">
                            <span class="card-price"><span class="icon">💰 </span><?=$card["price"]?></span>
                        </div>

                    <form action="admin.php" method="get">
                        <button id="kitolt">Módosítás</button>
                        <input type="hidden" name="kitolt" value="<?=$card["id"]?>">
                    </form>
                </div>

            <?php endforeach ?>
        </div>
    </div>

    <form action="" method="post">
        <label for="name">Neve: </label>
        <input type="text" name="name" value="<?= $adatok["name"] ?? "" ?>" disabled>
        <small><?= $errors["name"] ?? ""  ?></small>
        <br>

        <label for="type">Tipus: </label>
        <input type="text" name="type" value="<?= $adatok["type"] ?? "" ?>">
        <small><?= $errors["type"] ?? ""  ?></small>
        <br>

        <label for="hp">HP: </label>
        <input type="number" name="hp" value="<?= $adatok["hp"] ?? "" ?>" min="10">
        <small><?= $errors["hp"] ?? ""  ?></small>
        <br>

        <label for="attack">Támadás: </label>
        <input type="number" name="attack" value="<?= $adatok["attack"] ?? "" ?>" min="10">
        <small><?= $errors["attack"] ?? ""  ?></small>
        <br>

        <label for="defense">Védekezés: </label>
        <input type="number" name="defense" value="<?= $adatok["defense"] ?? "" ?>" min="10">
        <small><?= $errors["defense"] ?? ""  ?></small>
        <br>

        <label for="price">Ára: </label>
        <input type="number" name="price" value="<?= $adatok["price"] ?? "" ?>" min="50">
        <small><?= $errors["price"] ?? ""  ?></small>
        <br>

        <label for="description">Leírás: </label>
        <textarea name="description" ><?= $adatok["description"] ?? "" ?></textarea>
        <small><?= $errors["description"] ?? ""  ?></small>
        <br>

        <label for="image">Kép elérési útvonala: </label>
        <input type="text" name="image" value="<?= $adatok["image"] ?? "" ?>">
        <small><?= $errors["image"] ?? ""  ?></small>
        <br>
        <button type="submit">Mentés</button>
    </form>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>

</html>