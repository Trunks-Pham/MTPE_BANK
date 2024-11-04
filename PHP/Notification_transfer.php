<?php
session_start();

define('PHPMAILER_PATH', dirname(__FILE__) . '/../vendor/phpmailer/phpmailer/src');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require dirname(__FILE__) . '/../vendor/autoload.php';
require PHPMAILER_PATH . '/Exception.php';
require PHPMAILER_PATH . '/PHPMailer.php';
require PHPMAILER_PATH . '/SMTP.php';

// Đặt múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Hàm gửi email
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'minibanking.project1@gmail.com';
        $mail->Password   = 'ovtyaozbxzzohcic';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8'; // Đảm bảo sử dụng UTF-8

        $mail->setFrom('minibanking.project1@gmail.com', 'MTPE BANK');
        $mail->addAddress($to);

        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->IsHTML(true);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

//Thông báo cho người chuyển tiền
if (isset($_SESSION['loggedInLoginName'])) {
    $loggedInLoginName = $_SESSION['loggedInLoginName']; 
    $senderName = $_SESSION['name'];
    $senderEmail = $_SESSION['email'];
    $amount = $_SESSION['amount'];
    $senderNewBalance = $_SESSION['senderNewBalance'];
    $transactionContentSender = $_SESSION['transactionContentSender'];
    
    $senderNewBalanceFormatted = number_format($senderNewBalance, 0, ',', '.') . " VNĐ";
    $amountFormatted = number_format($amount, 0, ',', '.') . " VNĐ";

    $sender_message = "
        <b>$senderName</b> thân mến,<br><br>
        Tài khoản của quý khách vừa bị trừ <b>$amountFormatted VND</b> vào " . date("d/m/Y H:i:s") . ". <br><br><br>Số dư hiện tại: <b>$senderNewBalanceFormatted VND</b>.<br><br>
        Mô tả: <i>$transactionContentSender</i>.<br><br>
        Cảm ơn Quý khách đã sử dụng dịch vụ Ngân hàng số MTPE BANK!<br><br>
        Kính chúc Quý khách nhiều sức khỏe, hạnh phúc và thành công.<br>
        Trong trường hợp cần hỗ trợ, Quý khách có thể sử dụng <b>Live Chat</b> trên ứng dụng hoặc liên hệ với chúng tôi qua <b>Facebook</b> hoặc gửi yêu cầu đến địa chỉ minibanking.project1@gmail.com để nhận được sự hỗ trợ nhanh nhất từ MTPE BANK.<br><br>
        Trân trọng,<br>
        <b>MTPE BANK Team</b>
        <img src='https://bvbank.net.vn/wp-content/uploads/2024/03/Website_Banner-desktop-1920x640.png' alt='Organization 2' style='width: 100%;'>
    ";

    sendEmail($senderEmail, "Thông báo giao dịch từ Ngân hàng số MTPE BANK", $sender_message);
}
// Thông báo cho người chuyển tiền
// if (isset($_SESSION['loggedInLoginName'])) {
//     $loggedInLoginName = $_SESSION['loggedInLoginName']; 

//     // Lấy thông tin của người chuyển từ cơ sở dữ liệu
//     $sql = "SELECT name, email FROM customer WHERE loginName = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("s", $loggedInLoginName);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         $senderName = $row['name'];
//         $senderEmail = $row['email'];
//         $amount = $_SESSION['amount'];
//         $senderNewBalance = $_SESSION['senderNewBalance'];
//         $transactionContentSender = $_SESSION['transactionContentSender'];
        
//         $senderNewBalanceFormatted = number_format($senderNewBalance, 0, ',', '.') . " VNĐ";
//         $amountFormatted = number_format($amount, 0, ',', '.') . " VNĐ";

//         // Tạo nội dung email
//         $sender_message = "
//             <b>$senderName</b> thân mến,<br><br>
//             Tài khoản của quý khách vừa bị trừ <b>$amountFormatted VND</b> vào " . date("d/m/Y H:i:s") . ". <br><br><br>Số dư hiện tại: <b>$senderNewBalanceFormatted VND</b>.<br><br>
//             Mô tả: <i>$transactionContentSender</i>.<br><br>
//             Cảm ơn Quý khách đã sử dụng dịch vụ Ngân hàng số MTPE BANK!<br><br>
//             Kính chúc Quý khách nhiều sức khỏe, hạnh phúc và thành công.<br>
//             Trong trường hợp cần hỗ trợ, Quý khách có thể sử dụng <b>Live Chat</b> trên ứng dụng hoặc liên hệ với chúng tôi qua <b>Facebook</b> hoặc gửi yêu cầu đến địa chỉ minibanking.project1@gmail.com để nhận được sự hỗ trợ nhanh nhất từ MTPE BANK.<br><br>
//             Trân trọng,<br>
//             <b>MTPE BANK Team</b>
//             <img src='https://bvbank.net.vn/wp-content/uploads/2024/03/Website_Banner-desktop-1920x640.png' alt='Organization 2' style='width: 100%;'>
//         ";

//         // Gửi email thông báo cho người chuyển
//         sendEmail($senderEmail, "Thông báo giao dịch từ Ngân hàng số MTPE BANK", $sender_message);
//     }
// }



// Thông báo cho người nhận tiền
if (isset($_GET['accountNumber'])) {
    include 'connect.php';
    $accountNumber = $_GET['accountNumber'];

    $sql = "SELECT customer.name, customer.email FROM customer INNER JOIN customerAccount ON customer.id = customerAccount.id WHERE customerAccount.accountNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $accountNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $recipientName = $row['name'];
        $recipientEmail = $row['email'];
        $amount = $_SESSION['amount'];
        $receiverNewBalance = $_SESSION['receiverNewBalance'];
        $transactionContentReceiver = $_SESSION['transactionContentReceiver'];

        $receiverNewBalanceFormatted = number_format($receiverNewBalance, 0, ',', '.') . " VNĐ";
        $amountFormatted = number_format($amount, 0, ',', '.') . " VNĐ";
        
        $receiver_message = "
            <b>$recipientName </b>thân mến,<br><br>
            Tài khoản của quý khách vừa được cộng <b>$amountFormatted VND </b>vào " . date("d/m/Y H:i:s") . ". <br><br><br>Số dư hiện tại: <b>$receiverNewBalanceFormatted VND</b>.<br><br>
            Mô tả:<i> $transactionContentReceiver</i>.<br><br>
            Cảm ơn Quý khách đã sử dụng dịch vụ Ngân hàng số MTPE BANK!<br><br>
            Kính chúc Quý khách nhiều sức khỏe, hạnh phúc và thành công.<br>
            Trong trường hợp cần hỗ trợ, Quý khách có thể sử dụng <b>Live Chat</b> trên ứng dụng hoặc liên hệ với chúng tôi qua <b>Facebook</b> hoặc gửi yêu cầu đến địa chỉ minibanking.project1@gmail.com để nhận được sự hỗ trợ nhanh nhất từ MTPE BANK <br><br>
            Trân trọng,<br>
            <b>MTPE BANK Team</b><br><br>
            <img src='https://bvbank.net.vn/wp-content/uploads/2024/03/Website_Banner-desktop-1920x640.png' alt='Organization 2' style='width: 100%;'>
                        
        ";
        sendEmail($recipientEmail, "Thông báo giao dịch từ Ngân hàng số MTPE BANK", $receiver_message);
    }
} 


 
