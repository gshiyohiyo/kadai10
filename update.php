

<?php

    session_start();
    require_once('utility.php');
    loginCheck();

    if($_SESSION["kanri_flg"] != 1)
    {
        redirect("read.php");
    }

    require_once('model.php');
    $view = getUpdateModel($pdo);

    require_once('templates/updateView.php');
?> 
