<?php
include("cardstorage.php");

session_start();

$auth = new Auth(new UserStorage());

if ($auth->is_authenticated()) {
    $user = $auth->authenticated_user();
} else {
    header("Location: login.php");
}

if (count($_GET) > 0) {
    if (isset($_GET["id"]) && trim($_GET["id"]) !== '') {

        $id = $_GET["id"];

        $cardStorage = new CardStorage();
        $cardStorage->delete($id);
    }
}

header("Location: index.php");
exit();
