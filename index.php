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
    $_SESSION['role'] = $role;
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
</head>
<body>

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

    <div class="container">
        <?php
            if ($role === 'student' || $role === 'teacher') {
                if (isset($_POST['search'])) {
                    $search = $_POST['search'];
                    $sql = 'select class.class_id, class.class_name, class.room, class.avatar, class.subject
                    from class join quanly on class.class_id = quanly.class_id 
                    where quanly.username = ? and (class.class_id = ? or class.room = ? or class.subject = ?)';
                    $stm = $conn->prepare($sql);

                    $stm->bind_param('ssss', $_SESSION['user'], $search, $search, $search);

                }else {
                    $sql = 'select class.class_id, class.class_name, class.room, class.avatar, class.subject
                    from class join quanly on class.class_id = quanly.class_id 
                    where quanly.username = ?';
                    $stm = $conn->prepare($sql);

                    $stm->bind_param('s', $_SESSION['user']);
                }
            }else {
                if (isset($_POST['search'])) {
                    $search = $_POST['search'];
                    $sql = 'select class.class_id, class.class_name, class.room, class.avatar, class.subject
                    from class where class.class_id = ? or class.room = ? or class.subject = ?';
                    $stm = $conn->prepare($sql);

                    $stm->bind_param('sss', $search, $search, $search);

                }else {
                    $sql = 'select class.class_id, class.class_name, class.room, class.avatar, class.subject
                    from class where 1';
                    $stm = $conn->prepare($sql);

                }
            }


            if (!$stm->execute()) {
                die('Cannot execute command');
            }
            $result = $stm->get_result();
            if ($result->num_rows == 0) {
                echo "Không tìm thấy dữ liệu";
            }else {
                echo '<div class="row">';
                while ($data = $result->fetch_assoc()){
                    $class_id = $data['class_id'];
                    $class_name = $data['class_name'];
                    $subject = $data['subject'];
                    $class_room = $data['room'];
                    $class_avatar = $data['avatar'];
                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-1 mt-5">
                            <div class="card">
                                <img class="card-img-top" src="images/<?= $class_avatar ?>" alt="Card image">
                                <div class="card-img-overlay">
                                    <h4 class="card-title"><?= $class_name ?></h4>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $subject ?></h5>
                                    <p class="card-text mt-5"><?= 'Class code: ' . $class_id ?></p>
                                    <p class="card-text mb-2"><?= 'Room: ' . $class_room?></p>
                                </div>
                                <?php
                                    if ($role === 'teacher' || $role === 'admin'){
                                        ?>
                                            <div class="dropright show card-footer">
                                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Manage class
                                                </a>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <a class="dropdown-item" href="class.php?username=<?= $_SESSION['user'] . '&class_id=' . $class_id ?>">Enter class</a>
                                                    <a class="dropdown-item" href="add_student.php?class_id=<?= $class_id ?>">Add student</a>
                                                    <a class="dropdown-item" href="member.php?class_id=<?= $class_id ?>">View member</a>
                                                    <a class="dropdown-item" href="edit_class.php?class_id=<?= $class_id . '&class_name=' . $class_name . '&subject=' . $subject . '&room=' . $class_room ?>">Edit class</a>
                                                </div>

                                                <button data-id="<?= $class_id ?>" data-name="<?= $class_name ?>" type="button" class="btn btn-danger delete">Delete</button>
                                            </div>
                                        <?php
                                    }
                                    if ($role === 'student') {
                                        ?>
                                            <div class="card-footer">
                                                <a href="class.php?username=<?= $_SESSION['user'] . '&class_id=' . $class_id ?>" class="btn btn-outline-primary stretched-link">Enter class</a>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    <?php
                }
                echo '</div>';
            }
        ?>


    </div>

    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 id="modal-dialog-header" class="modal-title">Modal Heading</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    Are you sure to delete this class?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <form action="delete_class.php" method="post">
                        <input id="delete-form-id" type="hidden" name="id" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>

                </div>

            </div>
        </div>
    </div>


</body>
</html>