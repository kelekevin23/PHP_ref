<?php 
include("function/cardstorage.php");

$cardStorage = new CardStorage();

$cardID = "card" . $_GET["id"];
$card = $cardStorage->findById($cardID);

if ($card == null) {
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Beadando</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/details.css">
</head>

<body class="image clr-<?=$card["type"]?>">
    <header>
        <h1><a href="index.php">IKémon</a> > <?= $card["name"]?></h1>
    </header>
    <div id="content">
        <div id="details">
            <div class="image clr-<?=$card["type"]?>">
                <img src="<?= $card["image"]?>" alt="">
            </div>
            <div class="info">
                <div class="description">
                    <p><?= $card["description"]?></p>
                </div>
                <span class="card-type"><span class="icon">🏷</span> Type: <?=$card["type"]?></span>
                <div class="attributes">
                    <div class="card-hp"><span class="icon">❤</span> Health: <?=$card["hp"]?></div>
                    <div class="card-attack"><span class="icon">⚔</span> Attack: <?=$card["attack"]?></div>
                    <div class="card-defense"><span class="icon">🛡</span> Defense: <?=$card["defense"]?></div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>