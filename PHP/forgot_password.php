<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quên Mật Khẩu</title>
<link rel="icon" type="image/png" href="../IMAGES/Logo/logo 1.png">
<link rel="stylesheet" href="../CSS/sign.css">
</head>
<body>

<div class="container">
    <a href="index.php"><img src="../IMAGES/Logo/logo 1.png" class="center" width="100" height="auto"></a>
    <h2>Quên Mật Khẩu</h2> 


                <?php
                // Bắt đầu hoặc tiếp tục một phiên
                session_start();

                include 'connect.php';

                use PHPMailer\PHPMailer\PHPMailer;
                use PHPMailer\PHPMailer\Exception;

                define('PHPMAILER_PATH', dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src');

                require dirname(__FILE__) . '/../vendor/autoload.php';
                require PHPMAILER_PATH . '/Exception.php';
                require PHPMAILER_PATH . '/PHPMailer.php';
                require PHPMAILER_PATH . '/SMTP.php';

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $loginName = mysqli_real_escape_string($conn, $_POST['loginName']);
                    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
                    $email = mysqli_real_escape_string($conn, $_POST['email']);
                    $accountNumber = mysqli_real_escape_string($conn, $_POST['accountNumber']);

                    $sql = "SELECT customer.id, customerLogin.loginName FROM customer 
                            INNER JOIN customerLogin ON customer.loginName = customerLogin.loginName
                            INNER JOIN customerAccount ON customer.id = customerAccount.id 
                            WHERE customerLogin.loginName='$loginName' AND customer.phoneNumber='$phoneNumber' 
                            AND customer.email='$email' AND customerAccount.accountNumber='$accountNumber'";
                    $result = mysqli_query($conn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $loginName = $row['loginName'];

                        // Gửi email chứa mật khẩu mới đến người dùng
                        $new_password = generateRandomString(8); // Tạo mật khẩu mới ngẫu nhiên
                        $hashed_password = md5($new_password); // Sử dụng MD5 để mã hóa mật khẩu mới

                        $update_sql = "UPDATE customerLogin SET password='$hashed_password' WHERE loginName='$loginName'";
                        if (mysqli_query($conn, $update_sql)) {
                            // Gửi email chứa mật khẩu mới đến người dùng
                            sendPasswordResetEmail($email, $new_password);
                            echo "<div style='color: #00CC33;' class='success'>
                                        <span class='closebtn'>×</span>  
                                        Mật khẩu mới đã được gửi đến email của bạn. <br>Vui lòng kiểm tra hộp thư đến của bạn !
                                    </div> ";
                        } else {
                            echo "<div class='alert'>
                                    <span class='closebtn'>&times;</span>  
                                    Đã xảy ra lỗi. Vui lòng thử lại sau.
                                </div>";
                        }
                    } else {
                        echo "<div class='alert'>
                                <span class='closebtn'>&times;</span>  
                                Thông tin không chính xác. Vui lòng kiểm tra lại.
                            </div>";
                    }
                }

                mysqli_close($conn);

                function generateRandomString($length = 8) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $randomString = '';
                    for ($i = 0; $i < $length; $i++) {
                        $randomString .= $characters[rand(0, strlen($characters) - 1)];
                    }
                    return $randomString;
                }

                // Hàm gửi email khôi phục mật khẩu
                function sendPasswordResetEmail($email, $new_password) {
                    global $mail;

                    $mail = new PHPMailer(true);

                    try {
                        $mail->isHTML(true);

                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'minibanking.project1@gmail.com';
                        $mail->Password   = 'ovtyaozbxzzohcic';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('minibanking.project1@gmail.com', 'MTPE BANK');
                        $mail->addAddress($email);
                        $mail->CharSet = 'UTF-8';

                        $mail->Subject = 'Khôi phục mật khẩu MTPE BANK';
                        $mail->Body    = "
                        <div style='text-align: center;'> 
                        <img src='https://bvbank.net.vn/wp-content/uploads/2022/03/Tai-khoan-va-dich-vu_Thumbnail-san-pham_PC-1920x768.jpg' alt='Organization 1' style='width: 100%;'>
                        </div>
                        <p>Mật khẩu mới của bạn là: <strong>$new_password</strong></p>
                        <p>Vui lòng đăng nhập và thay đổi mật khẩu sau khi đăng nhập thành công.</p>
                        <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng liên hệ với chúng tôi ngay lập tức.</p>
                        <ul>
                            <li>Trong trường hợp cần hỗ trợ, Quý khách có thể sử dụng <b>Live Chat</b> trên ứng dụng hoặc liên hệ với chúng tôi qua <b>Facebook</b> hoặc gửi yêu cầu đến địa chỉ minibanking.project1@gmail.com để nhận được sự hỗ trợ nhanh nhất từ MTPE BANK </li>
                        </ul>
                        <p>Trân trọng,</p>
                        <p><strong>MTPE BANK Team</strong></p>
                        <div style='text-align: center;'>
                            <img src='https://bvbank.net.vn/wp-content/uploads/2024/03/Website_Banner-desktop-1920x640.png' alt='Organization 2' style='width: 100%;'>
                        </div> ";

                        $mail->send();
                    } catch (Exception $e) {
                        // Xử lý ngoại lệ khi gửi email thất bại
                        // Ví dụ: log lỗi, báo lỗi cho người quản trị, hoặc thực hiện hành động khác
                        error_log("Error sending password reset email: " . $e->getMessage(), 0);
                    }
                }
                ?>



    <form id="forgotPasswordForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="input-group">
            <input type="text" name="id" placeholder="Xác Nhận Danh Tính vui lòng Nhập Số CCCD" required>
        </div>
        <div class="input-group">
            <input type="text" name="phoneNumber" placeholder="Nhập Số điện thoại bạn đăng ký dịch vụ" required>
        </div>
        <div class="input-group">
            <input type="text" name="email" placeholder="Nhập Email bạn đăng ký dịch vụ" required>
        </div>
        <div class="input-group">
            <input type="text" name="loginName" placeholder="Tên đăng nhập của bạn" required>
        </div>
        <div class="input-group">
            <input type="text" name="accountNumber" placeholder="Số Tài khoản của bạn" required>
        </div>
        <button type="submit"><b>GỬI</b></button>

        <div class="signin-link" >
            <br>Tôi Đã Nhớ Ra Tài Khoản <a href="signin.php" > <span style="color:#0099CC;"><b>ĐĂNG NHẬP NGAY</b></span></a> !
        </div>
    </form>
</div>

</body>
</html>