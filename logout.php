<?php
    include('function/userstorage.php');
    include('function/auth.php');

    session_start();

    $userStorage = new UserStorage();
    $auth = new Auth($userStorage);

    $auth->logout();

    header("Location: index.php");
    exit();
?>