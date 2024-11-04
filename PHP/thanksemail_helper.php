<?php

define('PHPMAILER_PATH', dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require dirname(__FILE__) . '/../vendor/autoload.php';
require PHPMAILER_PATH . '/Exception.php';
require PHPMAILER_PATH . '/PHPMailer.php';
require PHPMAILER_PATH . '/SMTP.php';


function sendThankYouEmail($email) {
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

        $mail->Subject = 'Chào mừng bạn đến với MTPE BANK';
        $mail->Body    = "
        <div style='text-align: center;'> 
        <img src='https://bvbank.net.vn/wp-content/uploads/2022/03/Tiet-kiem-va-dau-tu_san-pham_PC-1920x767.jpeg' alt='Organization 1' style='width: 100%;'>
        </div>
        <p>Chào mừng bạn $email đã trở thành một phần của MTPE BANK !</p>
        <p>Cảm ơn bạn đã tin tưởng và lựa chọn sử dụng dịch vụ của chúng tôi, chúng tôi rất lấy làm vinh dự khi được phụ vụ bạn !</p>
        <ul>
            <li>Kính chúc Quý khách nhiều sức khỏe, hạnh phúc và thành công.</li>
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
        error_log("Error sending thank you email: " . $e->getMessage(), 0);
    }
}

?>
