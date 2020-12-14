<?php
    session_start();
    require_once ('db.php');

    $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_GET, 'role', FILTER_SANITIZE_STRING);

    $class_id = $_SESSION['class_id_member'];

    if (empty($username) || empty($role)) {
        header("Location: member.php?class_id=$class_id");
        exit();
    }

    $status = change_role($username, $role);
    if ($status) {
        header("Location: member.php?class_id=$class_id");
        exit();

    }else {
        echo "Change role fail";
    }
?>