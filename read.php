

<?php
    session_start();
    require_once('utility.php');
    loginCheck();

    require_once('model.php');

    $menu = getReadMenu();

    $pdo = connectDB();

    $view = getReadModel($pdo);

    disconnectDB($pdo);

    require_once('templates/readView.php');
?>