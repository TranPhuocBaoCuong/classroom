<?php
    session_start();
    $filename = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING);
    $class_id = $_SESSION['class_id_enter'];
    $path = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $class_id . '/'. $filename;

    if (unlink($path)) {
        header('Location: class.php?class_id=' . $class_id);
    }else {
        echo "Delete file fail";
    }
?>
