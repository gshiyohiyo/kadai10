
<?php

    require_once('utility.php');
    require_once('model.php');

    $pdo = connectDB();

    $view = getDetailModel($pdo);

    disconnectDB($pdo);

    require_once('templates/detailView.php');