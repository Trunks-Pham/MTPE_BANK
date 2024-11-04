<?php 
session_start();

$randomNumber = rand(100000, 999999);
$otp = $randomNumber; 

define('PHPMAILER_PATH', dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require dirname(__FILE__) . '/../vendor/autoload.php';
require PHPMAILER_PATH . '/Exception.php';
require PHPMAILER_PATH . '/PHPMailer.php';
require PHPMAILER_PATH . '/SMTP.php';


$to = isset($_POST['email']) ? $_POST['email'] : '';
$from = "minibanking.project1@gmail.com";
$subject = "=?UTF-8?B?" . base64_encode("MTPE BANK - Ngân Hàng Phát Triển Công Nghệ Số") . "?=";

$name = isset($_POST['name']) ? $_POST['name'] : '';

$body = "
    <div style='text-align: center;'> 
    <img src='https://bvbank.net.vn/wp-content/uploads/2022/03/Tiet-kiem-va-dau-tu_san-pham_PC-1920x767.jpeg' alt='Organization 1' style='width: 100%;'>
    </div>
    <p>Xin Chào <b>" .$name. "</b>,</p>
    <p>Mã xác minh của bạn là: <b>" . $otp . "</b></p>
    <p>Cảm ơn bạn đã tin tưởng và đăng ký sử dụng dịch vụ của chúng tôi, chúng tôi rất lấy làm vinh dự khi được phụ vụ bạn !</p>
    <ul>
        <li>Kính chúc Quý khách nhiều sức khỏe, hạnh phúc và thành công.</li>
        <li>Trong trường hợp cần hỗ trợ, Quý khách có thể sử dụng <b>Live Chat</b> trên ứng dụng hoặc liên hệ với chúng tôi qua <b>Facebook</b> hoặc gửi yêu cầu đến địa chỉ minibanking.project1@gmail.com để nhận được sự hỗ trợ nhanh nhất từ MTPE BANK </li>
    </ul>
    <p>Trân trọng,</p>
    <p><strong>MTPE BANK Team</strong></p>
    <div style='text-align: center;'>
        <img src='https://bvbank.net.vn/wp-content/uploads/2024/03/Website_Banner-desktop-1920x640.png' alt='Organization 2' style='width: 100%;'>
    </div>
";



$mail = new PHPMailer();

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'minibanking.project1@gmail.com';
    $mail->Password   = 'ovtyaozbxzzohcic';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($from, 'MTPE BANK');
    $mail->addAddress($to);

    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->IsHTML(true);

    $mail->send();
    echo json_encode(array("otp" => $otp));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Có lỗi xảy ra khi gửi mã OTP qua email: " . $e->getMessage()));
}

?>