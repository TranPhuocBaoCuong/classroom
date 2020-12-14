<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
    require_once('db.php');
    $sql = 'select role from member where username = ?';
    $conn = open_database();
    $stm = $conn->prepare($sql);

    $stm->bind_param('s', $_SESSION['user']);
    if (!$stm->execute()) {
        die('Cannot execute command');
    }
    $result = $stm->get_result();
    $role = $result->fetch_assoc()['role'];
    $_SESSION['role'] = $role;
?>
<DOCTYPE html>
<html lang="en">
    <head>
        <title>Danh sách thành viên</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="main.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-md bg-dark navbar-dark">

            <a class="navbar-brand" href="index.php">Classroom</a>
            <form class="form-inline" action="index.php" method="post">
                <input class="form-control mr-sm-2" type="text" placeholder="Nhập thông tin tìm kiếm" name="search">
                <button class="btn btn-success" type="submit">Search</button>
            </form>

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
                    if ($role === 'teacher' || $role === 'admin') {
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
        <ul class="list-group">
            <li class="list-group-item active px-5 mt-1">Member list</li>
            <?php
                $class_id = filter_input(INPUT_GET, 'class_id', FILTER_SANITIZE_STRING);
                $_SESSION['class_id_member'] = $class_id;
                if ($_SESSION['role'] === 'teacher'){
                    $result = return_list_student($class_id);
                }
                if ($_SESSION['role'] === 'admin') {
                    $result = return_all_member($class_id);
                }
                $i = 1;
                while ($data = $result->fetch_assoc()) {
                    ?>
                        <li class="list-group-item px-5 py-3">
                            <div>
                                <p><?=$i . '. ' . $data['hoten'] . ' (email: ' . $data['email'] .', role: ' . $data['role'] . ')'?></p>
                                <button data-id="<?= $data['username'] ?>" data-name="<?= $data['hoten'] ?>" type="button" class="btn btn-danger delete_student">Remove</button>
                                <?php
                                    if ($data['role'] === 'teacher') {
                                        ?>
                                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Change role
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a class="dropdown-item" href="change_role.php?username=<?= $data['username'] . '&role=admin' ?>">Change to admin</a>
                                                <a class="dropdown-item" href="change_role.php?username=<?= $data['username'] . '&role=student' ?>">Change to student</a>
                                            </div>
                                        <?php
                                    }
                                    if ($data['role'] === 'student') {
                                        ?>
                                        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Change role
                                        </a>

                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="change_role.php?username=<?= $data['username'] . '&role=admin' ?>">Change to admin</a>
                                            <a class="dropdown-item" href="change_role.php?username=<?= $data['username'] . '&role=teacher' ?>">Change to teacher</a>
                                        </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </li>
                    <?php
                    $i = $i + 1;
                }
            ?>

        </ul>
        <div class="modal" id="myModal_student">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 id="modal-dialog-header-student" class="modal-title">Modal Heading</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        Are you sure to delete this student?
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <form action="delete_student.php" method="post">
                            <input id="delete-form-id-1" type="hidden" name="username" value="">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>

                    </div>

                </div>
            </div>
        </div>

    </body>
</html>
