<?php
    session_start();
    $filename = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING);
    $class_id = $_SESSION['class_id_enter'];
    $path = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $class_id . '/'. $filename;
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $error = '';
    $email = '';
    $message = 'Enter new file name';
    if (isset($_POST['file'])) {
        $file = $_POST['file'];

        if (empty($file)) {
            $error = 'Please enter new file name';
        }
        else if (filter_var($file, FILTER_SANITIZE_STRING) == false) {
            $error = 'Invalid file name';
        }
        else {
            $path_new = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $class_id . '/' . $file;
            if (pathinfo($path_new, PATHINFO_EXTENSION) === ''){
                $path_new = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $class_id . '/' . $file . '.' . $ext;
            }
            if(rename($path, $path_new)) {
                header('Location: class.php?class_id=' . $class_id);
            }
            else {
                $error = 'Rename fail';
            }
        }
    }
?>
<DOCTYPE html>
    <html lang="en">
    <head>
        <title>Bootstrap Example</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </head>
    <body>
    <?php

    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <h3 class="text-center text-secondary mt-5 mb-3">Nhập tên file mới</h3>
                <form method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                    <div class="form-group">
                        <label for="file">File name</label>
                        <input name="file" id="file" type="text" class="form-control" placeholder="File name">
                    </div>
                    <div class="form-group">
                        <p><?= $message ?></p>
                    </div>
                    <div class="form-group">
                        <?php
                        if (!empty($error)) {
                            echo "<div class='alert alert-danger'>$error</div>";
                        }
                        ?>
                        <button class="btn btn-success px-5">Rename</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    </body>
    </html>
