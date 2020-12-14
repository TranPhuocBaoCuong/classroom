<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }
    $username = $_SESSION['user'];

    require_once('db.php');
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
    $error = '';
    $success = '';
    $class_id = '';
    if (isset($_POST['class_id'])) {
        $class_id = $_POST['class_id'];

        if (empty($class_id)) {
            $error = 'Please enter class id';
        }
        else if (filter_var($class_id, FILTER_SANITIZE_STRING) == false) {
            $error = 'Invalid class id';
        }
        else {
            $status = join_class($username, $class_id);
            if ($status == true) {
                $success = 'Register success';
            }else {
                $error = 'Register fail';
            }
        }
    }
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <h3 class="text-center text-secondary mt-5 mb-3">Class Register</h3>
                <form method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                    <div class="form-group">
                        <label for="class_id">Class ID</label>
                        <input name="class_id" id="class_id" type="text" class="form-control" placeholder="Class id">
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
                        <button class="btn btn-success px-5">Register</button>
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
