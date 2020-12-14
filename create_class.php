<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    require_once('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bootstrap 4 Vertical Form Layout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

</head>
<body>
<?php
    $error = '';
    $success = '';
    $classname = '';
    $subject = '';
    $room = '';


    if (isset($_POST['classname']) && isset($_POST['subject']) && isset($_POST['room']) && isset($_FILES['image']))
    {
        $classname = $_POST['classname'];
        $subject = $_POST['subject'];
        $room = $_POST['room'];
        $image = $_FILES['image'];

        if (empty($classname)) {
            $error = 'Please enter classname';
        }
        else if (empty($subject)) {
            $error = 'Please enter subject';
        }
        else if (empty($room)) {
            $error = 'Please enter class room';
        }
        else if ($image['error'] != 0) {
            $error = 'Class image upload error';
        }
        else {
            $target_dir = 'images/';
            $target_file = $target_dir . basename($image['name']);
            $alow_upload = true;
            $image_ex = pathinfo($target_file, PATHINFO_EXTENSION);
            $max_size = 800000;
            $alow_types = array('jpg', 'png', 'jpeg');

            $check = getimagesize($image['tmp_name']);
            if ($check != false) {
                $alow_upload = true;
            }else {
                $error = 'Please upload image file';
                $alow_upload = false;
            }

            if (file_exists($target_file)) {
                $alow_upload = false;
                $error = 'Image file exists';
            }

            if ($image['size'] > $max_size) {
                $error = 'Image size too big';
                $alow_upload = false;
            }

            if (!in_array($image_ex, $alow_types)) {
                $error = 'File type error';
                $alow_upload = false;
            }

            if ($alow_upload) {
                if (move_uploaded_file($image['tmp_name'], $target_file)) {
                    $result = create_class($_SESSION['user'], $classname, $subject, $room, $image['name']);
                    if ($result['code'] == 0){
                        $success = 'Create class success';
                    }else {
                        $error = 'An error occured. Please try again later';
                    }
                }
            }
        }
    }
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8 border my-5 p-4 rounded mx-3">
            <h3 class="text-center text-secondary mt-2 mb-3 mb-3">Create a new class</h3>
            <form method="post" action="" novalidate enctype="multipart/form-data">

                <div class="form-group">
                    <label for="classname">Class name</label>
                    <input value="<?= $classname ?>" name="classname" required class="form-control" type="text" placeholder="Class name" id="classname">
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input value="<?= $subject ?>" name="subject" required class="form-control" type="text" placeholder="Subject" id="subject">
                </div>

                <div class="form-group">
                    <label for="room">Room</label>
                    <input value="<?= $room ?>" name="room" required class="form-control" type="text" placeholder="Room" id="room">
                </div>

                <div class="form-group">
                    <label for="image">Choose class image file:</label>
                    <input name="image" required class="form-control" type="file" id="image">
                </div>

                <div class="form-group">
                    <?php
                    if (!empty($error)) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                    if (!empty($success)) {
                        echo "<div class='alert alert-success'>$success</div>";
                    }
                    ?>
                    <button type="submit" name="submit" class="btn btn-success px-5 mt-3 mr-2">Create</button>
                    <button type="reset" class="btn btn-outline-success px-5 mt-3">Reset</button>
                </div>
                <div class="form-group">
                    <p>Return to the <a href="index.php">Class</a> now.</p>
                </div>
            </form>

        </div>
    </div>

</div>
</body>
</html>

