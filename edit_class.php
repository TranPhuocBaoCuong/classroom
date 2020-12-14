<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
    require_once 'db.php';

    $sql = 'select role from member where username = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);

    $stm->bind_param('s', $_SESSION['user']);
    if (!$stm->execute()) {
        die('Cannot execute command');
    }
    $result = $stm->get_result();
    $role = $result->fetch_assoc()['role'];
    if ($role === 'student') {
        header('Location: index.php');
        exit();
    }
?>
<DOCTYPE html>
<html lang="en">
    <head>
        <title>Edit class</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </head>
<body>
    <?php

        $error = '';
        $post_error = '';
        $success = '';
        $class_name = '';
        $subject = '';
        $room = '';
        $display_class_id = filter_input(INPUT_GET, 'class_id',FILTER_SANITIZE_STRING);
        $display_class_name = filter_input(INPUT_GET, 'class_name',FILTER_SANITIZE_STRING);
        $display_class_subject = filter_input(INPUT_GET, 'subject',FILTER_SANITIZE_STRING);
        $display_class_room = filter_input(INPUT_GET, 'room',FILTER_SANITIZE_STRING);

        if (isset($_GET['class_id'])) {
            $class_id = $_GET['class_id'];
            $class_name = $_GET['class_name'];
            $subject = $_GET['subject'];
            $room = $_GET['room'];

            if (strlen($class_id) != 6) {
                $error = 'Invalid class id';
            }
            else {
                if (isset($_POST['class_name']) && isset($_POST['subject']) && isset($_POST['room'])) {

                    $class_name = $_POST['class_name'];
                    $subject = $_POST['subject'];
                    $room = $_POST['room'];

                    if (empty($class_name)) {
                        $post_error = 'Please enter class name';
                    }
                    else if (empty($subject)) {
                        $post_error = 'Please enter class subject';
                    }
                    else if (empty($room)) {
                        $post_error = 'Please enter room';
                    }
                    else {
                        $sql = 'update class set class_name = ?, subject = ?, room = ? where class_id = ?';

                        $conn = open_database();
                        $stm = $conn->prepare($sql);
                        $stm->bind_param('ssss', $class_name, $subject, $room, $class_id);

                        if (!$stm->execute()) {
                            $post_error = 'Cannot excute commnand';
                        }

                        $success = 'Class has been edited';
                    }
                }
            }
        }else {
            $error = 'Invalid class information';
        }

        ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <h3 class="text-center text-secondary mt-5 mb-3">Edit Class</h3>
                    <?php
                    if (!empty($error)){
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                    else {
                        ?>
                        <form novalidate method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                            <div class="form-group">
                                <label for="class_id">Class id</label>
                                <input readonly value="<?= $display_class_id ?>" name="class_id" id="class_id" type="text" class="form-control" placeholder="Class id">
                            </div>
                            <div class="form-group">
                                <label for="class_name">Class name</label>
                                <input  value="<?= $class_name?>" name="class_name" required class="form-control" type="text" placeholder="Class name" id="classname">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input value="<?= $subject?>" name="subject" required class="form-control" type="text" placeholder="Subject" id="subject">
                            </div>
                            <div class="form-group">
                                <label for="room">Room</label>
                                <input value="<?= $room?>" name="room" required class="form-control" type="text" placeholder="Room" id="room">
                            </div>
                            <div class="form-group">
                                <?php
                                if (!empty($post_error)){
                                    echo "<div class='alert alert-danger'>$post_error</div>";
                                }
                                if (!empty($success)) {
                                    echo "<div class='alert alert-success'>$success</div>";
                                }
                                ?>
                                <button class="btn btn-success px-5">Complete edit</button>
                            </div>
                            <div class="form-group">
                                <p>Return to the <a href="index.php">Class</a> now.</p>
                            </div>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
