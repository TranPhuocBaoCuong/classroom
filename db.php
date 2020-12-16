<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

	define('HOST', 'ec2-52-6-75-198.compute-1.amazonaws.com');
	define('USER', 'bnqwzptbqopsqc');
	define('PASS', '80ccc4247f51051024a5581f664420728b7840f7bf43c681546b1ba511f6f0b6');
	define('DB', 'dc0cbudmq0b3am');

	function open_database() {
		$conn = new mysqli(HOST, USER, PASS, DB);
		if ($conn->connect_error) {
			die("Connect error: " . $conn->connect_error);
		}
		return $conn;
	}

	function login($user, $pass) {
		$sql = "select * from member where username = ?";
		$conn = open_database();

		$stm = $conn->prepare($sql);
		$stm->bind_param('s', $user);

		if (!$stm->execute()) {
			return array('code' => 1, 'error' => 'Cannot execute command');
		}

		$result = $stm->get_result();
		$data = $result->fetch_assoc();

		if ($result->num_rows == 0) {
            return array('code' => 1, 'error' => 'User does not exists');
        }
        $stm->close();
		$hashed_password = $data['password'];
		if (!password_verify($pass, $hashed_password)) {
            return array('code' => 2, 'error' => 'Invalid password');
        }
		else if ($data['activated'] == 0){
            return array('code' => 3, 'error' => 'This account is not activated');
        }
		else return
            array('code' => 0, 'error' => '', 'data' => $data);
	}

	function is_email_exists($email) {
		$sql = 'select username from member where email = ?';

		$conn = open_database();

		$stm = $conn->prepare($sql);
		$stm->bind_param('s', $email);

		if (!$stm->execute()) {
			die("Query error: " . $stm->error);
		}

		$result = $stm->get_result();
        $stm->close();
		if ($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	function register($first, $last, $birthday, $phone, $email, $user, $pass) {

		if (is_email_exists($email)) {
			return array('code' => 1, 'error' => 'Email exists');
		}

		$hash = password_hash($pass, PASSWORD_DEFAULT);
		$rand = random_int(0, 1000);
		$token = md5($user . '+' . $rand);

		$sql = 'insert into member(username, password, hoten, birthday, email, phone, activate_token) values(?,?,?,?,?,?,?)';

		$name = $last . ' ' .$first;

		$conn = open_database();
		$stm = $conn->prepare($sql);
		$stm->bind_param('sssssss', $user, $hash, $name, $birthday, $email, $phone, $token);

		if (!$stm->execute()) {
			return array('code' => 2, 'error' => 'Cannot execute command');
		}

        send_activation_email($email, $token);
        $stm->close();
		return array('code' => 0, 'error' => 'Create account successful');
	}

    function send_activation_email($email, $token) {



    // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'mickey123.bct@gmail.com';                     // SMTP username
            $mail->Password = 'xpnfkrcgrcktypqk';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('mickey123.bct@gmail.com', 'Admin');
            $mail->addAddress($email, 'Người nhận');     // Add a recipient
            /*$mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Khôi phục mật khẩu của bạn';
                $mail->Body = "Click <a href='http://localhost:8081/activation.php?email=$email&token=$token'>vào đây</a> để xác minh tài khoản của bạn";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function send_reset_email($email, $token) {

        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'mickey123.bct@gmail.com';                     // SMTP username
            $mail->Password = 'xpnfkrcgrcktypqk';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('mickey123.bct@gmail.com', 'Admin');
            $mail->addAddress($email, 'Người nhận');     // Add a recipient
            /*$mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Xác minh tài khoản của bạn';
            $mail->Body = "Click <a href='http://localhost:8081/reset_password.php?email=$email&token=$token'>vào đây</a> để khôi phục mật khẩu của bạn";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function active_account($email, $token) {
	    $sql = 'select username from member where email = ? and activate_token = ? and activated = 0';

	    $conn = open_database();
	    $stm = $conn->prepare($sql);

	    $stm->bind_param('ss', $email, $token);

	    if (!$stm->execute()) {
	        return array('code' => 1, 'error' => 'Cannot execute command');
        }
	    $result = $stm->get_result();
	    if ($result->num_rows == 0) {
	        return array('code' => 2, 'error' => 'Email address or token not found');
        }

	    $sql = "update member set activated = 1, activate_token = '' where email = ?";
	    $stm = $conn->prepare($sql);
	    $stm->bind_param('s', $email);
	    if (!$stm->execute()) {
	        return array('code' => 1, 'error' => 'Cannot execute command');
        }
        $stm->close();
	    return array('code' => 0, 'message' => 'Account activated');
    }

    function reset_password($email) {
	    if (!is_email_exists($email)) {
	        return array('code' => 1, 'error' => 'Email does not exists');
        }
	    $token = md5($email . '+' . random_int(1000, 2000));
	    $sql = 'update reset_token set token = ? where email = ?';

	    $conn = open_database();
	    $stm = $conn->prepare($sql);
	    $stm->bind_param('ss', $token, $email);

	    if (!$stm->execute()) {
	        return array('code' => 2, 'error' => 'Cannot execute command');
        }
	    if ($stm->affected_rows == 0) {

	        $exp = time() + 3600*24;
	        $sql = 'insert into reset_token values(?,?,?)';
            $stm = $conn->prepare($sql);
            $stm->bind_param('ssi', $email,$token, $exp);

            if (!$stm->execute()) {
	        return array('code' => 2, 'error' => 'Cannot execute command');
            }
        }

        $success = send_reset_email($email, $token);
	    $stm->close();
	    return array('code' => 0, 'success' => $success);
    }

    function rand_string($length) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen($chars);
        $str = '';
        for($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }

    function delete_class($id) {
	    $sql = "delete c, q from class c join quanly q on c.class_id = q.class_id where c.class_id = ?";
	    $conn = open_database();

	    $stm = $conn->prepare($sql);
	    $stm->bind_param('s', $id);


	    $status = $stm->execute();

	    $stm->close();
	    return $status;
    }

    function is_existed_class($id) {
        $sql = 'select * from class where class_id = ?';

        $conn = open_database();

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $id);

        if (!$stm->execute()) {
            die("Query error: " . $stm->error);
        }

        $result = $stm->get_result();
        $stm->close();
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function check_user_class_exists($username, $class_id) {
        $sql = 'select * from quanly where class_id = ? and username = ?';

        $conn = open_database();

        $stm = $conn->prepare($sql);
        $stm->bind_param('ss', $class_id, $username);

        if (!$stm->execute()) {
            die("Query error: " . $stm->error);
        }

        $result = $stm->get_result();
        $stm->close();
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function join_class($username, $class_id) {
	    if (is_existed_class($class_id) && !check_user_class_exists($username, $class_id)) {
            $sql = 'insert into quanly(username, class_id) values(?, ?)';
            $conn = open_database();

            $stm = $conn->prepare($sql);
            $stm->bind_param('ss', $username, $class_id);

            $status = $stm->execute();

            $stm->close();
            return $status;
        }else {
	        return false;
        }

    }

    function create_class($user, $classname, $subject, $room, $avatar) {
	    $class_id = rand_string(6);

	    $sql = 'insert into class(class_id, class_name, subject, room, avatar) values(?,?,?,?,?)';
        $conn = open_database();
        $stm = $conn->prepare($sql);
        $stm->bind_param('sssss', $class_id, $classname, $subject, $room, $avatar);

        if (!$stm->execute()) {
            return array('code' => 2, 'error' => 'Cannot execute command');
        }

        $sql = 'insert into quanly(username, class_id) values(?,?)';
        $stm = $conn->prepare($sql);
        $stm->bind_param('ss', $user, $class_id);

        if (!$stm->execute()) {
            return array('code' => 2, 'error' => 'Cannot execute command');
        }
        $stm->close();
        return array('code' => 0, 'error' => 'Create class successful');
    }

    function return_list_student($class_id) {
	    $sql = "select q.username , m.hoten, m.email, m.role from quanly q join member m on q.username = m.username where q.class_id = ? and m.role != 'teacher'";

	    $conn = open_database();
        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $class_id);

        if (!$stm->execute()) {
            die("Query error: " . $stm->error);
        }

        $result = $stm->get_result();
        $stm->close();
        return $result;
    }

    function delete_student($username) {
        $sql = "delete from quanly where username = ?";
        $conn = open_database();

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $username);


        $status = $stm->execute();

        $stm->close();
        return $status;
    }

    function select_username_by_email($email) {
        $sql = 'select username from member where email = ?';

        $conn = open_database();

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $email);

        if (!$stm->execute()) {
            die("Query error: " . $stm->error);
        }

        $result = $stm->get_result();
        $username = $result->fetch_assoc()['username'];
        $stm->close();
        return $username;
    }

    function add_student($email, $class_id) {
	    if (is_email_exists($email)) {
	        $username = select_username_by_email($email);
            $sql = 'insert into quanly(username, class_id) values(?, ?)';
            $conn = open_database();

            $stm = $conn->prepare($sql);
            $stm->bind_param('ss', $username, $class_id);


            $status = $stm->execute();
            $stm->close();
            return $status;
        }else {
	        return false;
        }

    }

    function add_comment($comment, $class_id, $user) {
	    $sql = 'insert into clas_comment(class_id, comment, username) values(?,?,?)';

        $conn = open_database();
        $stm = $conn->prepare($sql);
        $stm->bind_param('sss', $class_id, $comment, $user);

        $status = $stm->execute();

        $stm->close();
        return $status;
    }

    function load_comment($class_id) {
	    $sql = 'select * from clas_comment where class_id = ?';
        $conn = open_database();
        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $class_id);

        if (!$stm->execute()) {
            die("Query error: " . $stm->error);
        }

        $result = $stm->get_result();
        $stm->close();
        return $result;
    }

    function delete_comment($username) {
        $sql = "delete from clas_comment where username = ?";
        $conn = open_database();

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $username);


        $status = $stm->execute();

        $stm->close();
        return $status;
    }

    function return_all_member($class_id){
        $sql = "select q.username , m.hoten, m.email, m.role from quanly q join member m on q.username = m.username where q.class_id = ?";

        $conn = open_database();
        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $class_id);

        if (!$stm->execute()) {
            die("Query error: " . $stm->error);
        }

        $result = $stm->get_result();
        $stm->close();
        return $result;
    }

    function change_role($username, $role) {
	    $sql = "update member set role = ? where username = ?";
        $conn = open_database();

        $stm = $conn->prepare($sql);
        $stm->bind_param('ss', $role, $username);


        $status = $stm->execute();

        $stm->close();
        return $status;
    }


?>
