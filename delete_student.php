<?php
    session_start();
    require_once ('db.php');

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);

    $class_id = $_SESSION['class_id_member'];
    if (empty($username)) {
        header("Location: member.php?class_id=$class_id");
        exit();
    }

    $status = delete_student($username);
    if ($status) {
        header("Location: member.php?class_id=$class_id");
        exit();

    }else {
        echo "Delete fail";
    }
?>