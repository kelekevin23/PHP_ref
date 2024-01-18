<?php 
include("function/cardstorage.php");
include("function/userstorage.php");
session_start();

$cardStorage = new CardStorage();
$cards = $cardStorage->findAll();

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$cardsPerPage = 9;

$offset = ($page - 1) * $cardsPerPage;
$visibleCards = array_slice($cards, $offset, $cardsPerPage);

$user = null;
if (isset($_SESSION["user"])) {
    $user = $_SESSION["user"];
}


$categories = array_unique(array_map(function ($card) {
    return $card["type"];
}, $cards));

if (count($_GET) > 0) {
    if (isset($_GET["filter"]) && trim($_GET["filter"]) !== "") {
        $filter = $_GET["filter"];
        $visibleCards = array_filter($cards, function ($card) use ($filter) {
            return $card["type"] === $filter;
        });
    }
}

if ($user != null){   
    $ownedCards = array_filter($cards, function ($card) use ($user) {
        return $card["owner"] === $user["username"];
    });
}

$errors = [];
$userStorage = new UserStorage();

if (isset($_POST["buyId"])) {
    $id = "card" . $_POST["buyId"];
    $card = $cardStorage->findById($id);
    
    if (count($ownedCards) == 5){
        $errors["user"] = "Nem vehetsz több kártyát!";
    } else {
        if ($user["money"] < $card["price"]){
            $errors["user"] = "Nincs elég pénzed a vásárláshoz!";
        } else {    
            if ($card["owner"] == "admin") {
                $user["money"] -= $card["price"];
                $card["owner"] = $user["username"];
                $cardStorage->update($id, $card);
                $userStorage->update($user["id"], $user);
                $_SESSION["user"] = $user;
            }
            header("Location: index.php");
        }
    }
}


if (isset($_POST["random"])) {
    $rand = "card" . rand(0, count($cards) - 1);
    $randomCard = $cards[$rand];

    if (count($ownedCards) == 5){
        $errors["user"] = "Nem vehetsz több kártyát!";
    } else {
          
        $jo = 0;
        while ($jo == 0){
            $rand = "card" . rand(0, count($cards) - 1);
            $randomCard = $cards[$rand];
            if ($randomCard["owner"] == "admin"){
                $jo = 1;
            }
        }

        if ($user["money"] < $randomCard["price"]){
            $errors["user"] = "Nincs elég pénzed a vásárláshoz!";
        } else {
            $user["money"] -= $randomCard["price"];
            $randomCard["owner"] = $user["username"];
            $cardStorage->update("card" . $randomCard["id"], $randomCard);
            $userStorage->update($user["id"], $user);

            $_SESSION["user"] = $user;
            
            header("Location: index.php");
        }    
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Beadando</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/cards.css">

    
    
</head>
<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Főoldal</h1>
    </header>
    <nav>
        <ul>
            <?php if ($user) : ?>
                <?php if ($user["username"] == "admin") : ?>
                    <li><a href="addCard.php">Új hozzáadása</a></li>
                    <li><a href="admin.php">Adatmódosítás</a></li>
                    <li><a href="logout.php">Kijelentkezés</a></li>
                <?php else : ?>
                    <li><a href="user.php"><?= $user["username"] ?></a></li>
                    <li><a href="">Pénz: <?= $user["money"] ?></a></li>
                    <li><a href="logout.php">Kijelentkezés</a></li>
                <?php endif ?>
            <?php else : ?>
                <li><a href="login.php">Bejelentkezés</a></li>
                <li><a href="register.php">Regisztráció</a></li>
            <?php endif ?>
        </ul>
    </nav>
    <div id="content">
        <div id="filter">
            <form action="index.php" method="get">
                <label for="filter">Kategóriák:</label>
                <select name="filter" id="filter">
                    <option value="">Összes</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category ?>"><?= $category ?></option>
                    <?php endforeach ?>
                </select>
                <button type="submit">Szűrés</button>
            </form>
        </div>

        
        <small><?= $errors["user"] ?? ""  ?></small>

        <?php if ($user) : ?>
            <?php if ($user["username"] != "admin") : ?>
                <form action="" method="post">
                    <button id="random">Random vásárlás</button>
                    <input type="hidden" name="random" value="">
                </form>
            <?php endif ?>
        <?php endif ?>
        

        <div id="card-list">
            <?php foreach ($visibleCards as $card) : ?>
                <div class="pokemon-card">
                    <div id="owner">
                        <?php if ($card["owner"] !== "admin") : ?>
                            <h2>Tulajdonos: <?=$card["owner"]?></h2>
                        <?php else : ?>
                            <h2>Tulajdonos: Nincs</h2>
                        <?php endif ?>
                    </div>
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
                    <div id="vasarlas">    
                        <div class="buy">
                            <span class="card-price"><span class="icon">💰 </span><?=$card["price"]?></span>
                        </div>

                        <?php if ($user != null && $user["role"] != "admin" && $user["username"] != $card["owner"]) : ?>
                            <form action="" method="post">
                                <button id="buyCard">Vásárlás</button>
                                <input type="hidden" name="buyId" value="<?=$card["id"]?>">
                                <input type="hidden" name="buyPrice" value="<?=$card["price"]?>">
                            </form>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <div id="pages">
            <?php
                if (count($visibleCards) > 8)
                {
                    $totalPages = ceil(count($cards) / $cardsPerPage);
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo '<a href="index.php?page=' . $i . '">' . $i . '</a>';
                    } 
                } else {
                    $filter = isset($_GET["filter"]) ? $_GET["filter"] : "";
                    if ($filter == ""){
                        $totalPages = ceil(count($cards) / $cardsPerPage);
                        for ($i = 1; $i <= $totalPages; $i++) {
                            echo '<a href="index.php?page=' . $i . '">' . $i . '</a>';
                        }
                    }
                     
                }
            ?>
        </div>
    </div>

    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>