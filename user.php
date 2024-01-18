<?php 
include("function/cardstorage.php");
include("function/userstorage.php");
include("function/auth.php");
session_start();

$cardStorage = new CardStorage();
$cards = $cardStorage->findAll();

$userStorage = new UserStorage();
$auth = new Auth($userStorage);
$user = null;

if ($auth->is_authenticated()) {
    $user = $auth->authenticated_user();
    if($user["username"] == "admin"){
        header("Location: index.php");
    }
} else {
    header("Location: login.php");
}

if (isset($_POST["sellId"])) {
    $id = "card" . $_POST["sellId"];
    $card = $cardStorage->findById($id);
    if ($card["owner"] == $user["username"]) {
        $user["money"] += $card["price"] * 0.9;
        $card["owner"] = "admin";
        $cardStorage->update($id, $card);
        $userStorage->update($user["id"], $user);
    }
    header("Location: user.php");
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
        <h1><a href="index.php">IKémon</a> > Felhasználói részletek</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Főoldal</a></li>
            <li><a href="logout.php">Kijelentkezés</a></li>
        </ul>
    </nav>
    <section>
        <div id="user-info">
            <h2>Felhasználó: <?=$user["username"]?></h2>
            <h2>Email: <?=$user["email"]?></h2>
            <h2>Pénz: <?=$user["money"]?></h2>
        </div>
    </section>
    <div id="content">
        <div id="card-list">
            <?php foreach ($cards as $card) : ?>
                <?php if ($card["owner"] == $user["username"]) : ?>
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

                        <div action="" class="sell">
                            <form method="post">
                                <input type="hidden" name="sellId" value="<?=$card["id"]?>">
                                <input type="submit" value="Sell" id="sell">
                            </form>
                        </div>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>

    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>