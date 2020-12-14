<?php
    session_start();
    $filename = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING);

    $class_id = $_SESSION['class_id_enter'];
    $filename = basename($filename);
    $path = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $class_id . '/'. $filename;

    $filetype = filetype($path);



    header("Content-Type: " . $filetype);

    header("Content-Length: " . filesize($filename));

    header("Content-Disposition: attachment; filename=" . $filename);

    readfile($path);
?>