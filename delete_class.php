<?php
    require_once ('db.php');

    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

    if (empty($id)) {
        header('Location: index.php');
        exit();
    }

    $status = delete_class($id);
    if ($status) {
        header('Location: index.php');
    }else {
        echo "Delete fail";
    }
?>