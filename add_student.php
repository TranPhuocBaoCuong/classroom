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

    $class_id = filter_input(INPUT_GET, 'class_id', FILTER_SANITIZE_STRING);

    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        if (empty($email)) {
            $error = 'Please enter email';
        }
        else if (filter_var($email, FILTER_SANITIZE_EMAIL) == false) {
            $error = 'Invalid email address';
        }
        else {
            $status = add_student($email, $class_id);
            if ($status == true) {
                $success = 'Add student success';
            }else {
                $error = 'Add student fail';
            }
        }
    }
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <h3 class="text-center text-secondary mt-5 mb-3">Add Student</h3>
                <form method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                    <div class="form-group">
                        <label for="email">Student email</label>
                        <input name="email" id="email" type="text" class="form-control" placeholder="email">
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
                        <button class="btn btn-success px-5">Add</button>
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
