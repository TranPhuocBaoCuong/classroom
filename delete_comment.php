<?php
    session_start();
    require_once ('db.php');

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $class_id = $_SESSION['class_id_enter'];
    if (empty($username)) {
        header('Location: login.php');
        exit();
    }

    $status = delete_comment($username);
    if ($status) {
        header('Location: class.php?class_id=' . $class_id);
        exit();

    }else {
        echo "Delete comment fail";
    }
?>