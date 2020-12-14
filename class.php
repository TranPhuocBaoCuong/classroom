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


?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Classroom</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="main.js"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

    <body>

    <nav class="navbar navbar-expand-md bg-dark navbar-dark">

        <a class="navbar-brand" href="index.php">Classroom</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Hello, <?= $_SESSION['user'] ?></a>
                </li>
                <?php
                if ($role === 'student') {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="join_class.php">Join class</a>
                    </li>
                    <?php
                }
                if ($role === 'teacher') {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="create_class.php">Create class</a>
                    </li>
                    <?php
                }
                ?>

                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php
            $class_id = filter_input(INPUT_GET, 'class_id', FILTER_SANITIZE_STRING);
            $_SESSION['class_id_enter'] = $class_id;
            $sql = 'select * from class where class_id = ?';
            $conn = open_database();
            $stm = $conn->prepare($sql);
            $stm->bind_param('s', $class_id);

            if (!$stm->execute()) {
                die('Cannot execute command');
            }
            $result = $stm->get_result();
            if ($result->num_rows == 0) {
                echo "No data";
            }
            else {
                $data = $result->fetch_assoc();
                $avatar = $data['avatar'];
                $class_name = $data['class_name'];
                ?>

                    <div class="row">
                        <div class="mt-3 mx-auto">
                            <h1 class="text-banner"><?= $class_name ?></h1>
                            <img src="/images/<?= $avatar ?>">
                        </div>
                    </div>

                <?php
            }
            if (isset($_POST['comment']) && isset($_POST['username'])) {
                $user = $_POST['username'];
                $comment = $_POST['comment'];
                $status = add_comment($comment, $class_id, $user);
                if ($status == false) {
                    $error = 'Post comment fail';
                }else {
                    header('Position: class.php?class_id=' . $class_id);
                }
            }
            $error1 = '';
            $success = '';
            if (isset($_POST['up']) && isset($_FILES['fileUpload'])) {
                if ($_FILES['fileUpload']['error'] > 0) {
                    $error1 = 'Upload file lỗi';
                }else {
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    $upload_dir = $root . '/upload';
                    $files = scandir($upload_dir);
                    $flag = 0;
                    foreach ($files as $upload) {
                        if ($upload === $class_id) {
                            $flag = 1;
                            if(move_uploaded_file($_FILES['fileUpload']['tmp_name'], 'upload/'. $class_id .'/'. $_FILES['fileUpload']['name'])) {
                                $success = 'Upload file thành công';
                            }else {
                                $error1 = 'Upload failed';
                            }
                        }
                    }
                    if ($flag == 0) {
                        if(mkdir($upload_dir . '/' . $class_id,0777, true)) {
                            if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], 'upload/' . $class_id . '/' . $_FILES['fileUpload']['name'])) {
                                $success = 'Upload file thành công';
                            } else {
                                $error1 = 'Upload file lỗi';
                            }
                        }else {
                            $error1 = 'Upload error';
                        }
                    }

                }
            }

        ?>
        <h5 class="mx-5 mt-3">Class comment</h5>
        <form method="post" action="">
            <div class="form-group px-5">
                <textarea name="comment" class="form-control" rows="2" id="comment"></textarea>
            </div>
            <div class="form-group px-5">
                    <?php
                        if (!empty($error)) {
                            echo "<div class='alert alert-danger'>$error</div>";
                        }
                    ?>
                <input type="hidden" name="username" value="<?= $_SESSION['user'] ?>">
                <button type="submit" class="btn btn-primary px-5 ml-auto">Post</button>
            </div>
        </form>

        <?php
            $class_comment = load_comment($class_id);

            while ($dulieu = $class_comment->fetch_assoc()) {
                $line_comment = $dulieu['comment'];
                $user_comment = $dulieu['username'];
                ?>

                    <div class="media border p-3 mx-5">
                        <div class="media-body">
                            <h4><?= $user_comment ?></h4>
                            <p><?= $line_comment ?></p>
                        </div>
                        <?php

                            if ($user_comment == $_SESSION['user']) {
                                ?>

                                    <button data-id="<?= $user_comment ?>" data-name="<?= $user_comment ?>" class="btn btn-success delete-comment" type="button">Xóa comment</button>

                                <?php
                            }

                        ?>
                    </div>

                <?php
            }
        ?>
        <?php
        if ($_SESSION['role'] === 'teacher' or $_SESSION['role'] === 'admin'){
            ?>
                <h5 class="mx-5 mt-3">Upload file</h5>
                <form action="" method="post" enctype="multipart/form-data" class="px-5">
                    <input type="file" id="myFile" name="fileUpload" value="">
                    <?php
                    if (!empty($error1)) {
                        echo "<div class='alert alert-danger mt-3'>$error1</div>";
                    }
                    if (!empty($success)) {
                        echo "<div class='alert alert-success mt-3'>$success</div>";
                    }
                    ?>
                    <div class="form-group mt-3">
                        <button type="submit" name="up" value="Upload" class="btn btn-primary px-3">Upload</button>
                    </div>
                </form>
            <?php
        }
        ?>


        <div class="item-contain">
            <?php
                $root = $_SERVER['DOCUMENT_ROOT'];
                $upload_dir = $root . '/upload';
                $files = scandir($upload_dir);
                foreach ($files as $file) {
                    if ($file === $class_id) {
                        $path = $upload_dir . '/' . $class_id;
                        $files1 = scandir($path);

                        foreach ($files1 as $file) {
                            if (substr($file, 0,1) === '.'){
                                continue;
                            }
                            $path1 = $path . '/' . $file;
                            $ext = pathinfo($path1, PATHINFO_EXTENSION);
                            $size = filesize($path1);
                            if ($size > 1000000) {
                                $size = round($size/1000000.0, 1) . ' MB';
                            }else if ($size > 1000) {
                                $size = round($size/1000.0, 1) . ' KB';
                            }else {
                                $size = $size . ' Bytes';
                            }
                            if ($ext=='jpg' || $ext=='gif' || $ext=='jpeg' || $ext=='tiff' || $ext=='bmp') {
                                $icon = '../images/ficture.png';
                            }elseif ($ext == 'c' || $ext == 'py' || $ext == 'java' || $ext == 'js' || $ext == 'css' || $ext == 'php'  ) {
                                # code...
                                $icon = '../images/code.png';
                            }elseif ($ext == 'mp4') {
                                # code...
                                $icon = '../images/video.png';
                            }elseif ($ext == 'mp3') {
                                # code...
                                $icon = '../images/music.png';
                            }elseif ($ext == 'doc' || $ext == 'docx' ) {
                                # code...
                                $icon = '../images/word.png';
                            }elseif ($ext == 'pdf') {
                                # code...
                                $icon = '../images/pdf.png';
                            }elseif ($ext == 'ppt' || $ext == 'pptx') {
                                # code...
                                $icon = '../images/pp.png';
                            }elseif ($ext == 'xls' || $ext == 'xlsx') {
                                # code...
                                $icon = '../images/excel.png';
                            }elseif ($ext == 'zip' || $ext == 'rar') {
                                # code...
                                $icon = '../images/excel.png';
                            }elseif ($ext == 'txt' || $ext == 'sql') {
                                # code...
                                $icon = '../images/text.png';
                            }else{
                                # code...
                                $icon = '../images/file.png';
                            }
                            ?>

                            <hr class="unliner">
                            <div class="row ml-5 px-5">
                                <a class="item" href="download.php?file=<?= $file ?>">
                                    <img src="<?=$icon?>" id="img-file-name">
                                    <div class="item-info">
                                        <div class="item-name"><b>Name:</b> <br><?=$file?></div>
                                        <div class="item-size"><b>Size:</b> <?=$size?></div>
                                        <div class="item-type"><b>Type:</b> <?=$ext?></div>
                                    </div>
                                </a>
                                <?php
                                    if ($_SESSION['role'] === 'teacher' or $_SESSION['role'] === 'admin'){
                                        ?>
                                            <div class="dropdown ml-auto mr-5">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Manage file
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="delete_file.php?file=<?= $file ?>">Delete</a>
                                                    <a class="dropdown-item" href="rename_file.php?file=<?= $file ?>">Rename</a>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                ?>

                            </div>

                            <?php
                        }
                    }
                }
            ?>
        </div>
    </div>
    <div class="modal" id="myModal_comment">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 id="modal-dialog-header-2" class="modal-title">Modal Heading</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    Are you sure to delete this Comment?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <form action="delete_comment.php" method="post">
                        <input id="delete-form-id-2" type="hidden" name="username" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>

                </div>

            </div>
        </div>
    </div>

    </body>
</html>