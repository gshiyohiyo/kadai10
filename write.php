
<?php
    session_start();
    require_once('utility.php');
    loginCheck();

    require_once('model.php');
    $view = getWriteModel($pdo);

    require_once('templates/writeView.php');