<?php
include 'connect.php'; 
session_start();

// Hàm để tạo MD5
function CreateMD5($input) {
    return md5($input);
}

// Định nghĩa một mảng để lưu trữ phản hồi
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['bank']) && isset($_POST['accountNumber']) && isset($_POST['amount']) && isset($_POST['content']) && isset($_POST['password'])) {
 
        $bank = $_POST['bank'];
        $accountNumber = $_POST['accountNumber'];
        $amount = $_POST['amount'];
        $content = $_POST['content'];
        $password = $_POST['password'];
        
        if(isset($_SESSION['loggedInLoginName'])) {
            $loggedInLoginName = $_SESSION['loggedInLoginName'];

            // Lấy mật khẩu từ cơ sở dữ liệu dựa trên tên đăng nhập
            $sql = "SELECT password FROM customerLogin WHERE loginName='$loggedInLoginName'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $correctPassword = $row['password']; // Lấy mật khẩu đã mã hóa từ cơ sở dữ liệu

                $entered_password = CreateMD5($password); // Mã hóa mật khẩu nhập vào từ biểu mẫu

                if ($entered_password === $correctPassword) {

                    // Thực hiện chuyển khoản
                    $sql = "SELECT accountNumber, balance FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName')";
                    $result = mysqli_query($conn, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $senderAccountNumber = $row['accountNumber'];
                        $senderBalance = $row['balance'];
                        
                        // Kiểm tra người gửi không chuyển tiền cho chính mình
                        if ($accountNumber != $senderAccountNumber){
                            // Kiểm tra số dư tài khoản người chuyển
                            if ($senderBalance >= $amount) {
                                // Trừ số tiền từ tài khoản người chuyển
                                $senderNewBalance = $senderBalance - floatval($amount);
                                $sql = "UPDATE customerAccount SET balance = $senderNewBalance WHERE accountNumber = '$senderAccountNumber'";
                                mysqli_query($conn, $sql);

                                // Cộng số tiền vào tài khoản người nhận
                                $sql = "SELECT balance FROM customerAccount WHERE accountNumber = '$accountNumber'";
                                $result = mysqli_query($conn, $sql);
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);
                                    $receiverBalance = $row['balance'];
                                    $receiverNewBalance = $receiverBalance + floatval($amount);
                                    
                                 // Cập nhật số dư vào tài khoản người nhận
                                $sql = "UPDATE customerAccount SET balance = $receiverNewBalance WHERE accountNumber = '$accountNumber'";
                                mysqli_query($conn, $sql);

                                // Chèn dữ liệu vào bảng transaction cho người gửi (transactionType là "Chuyển Tiền")
                                $transactionTypeSender = "Chuyển tiền";
                                $transactionContentSender = "Chuyển khoản đến số tài khoản $accountNumber với nội dung:  $content";
                                $sqlSender = "INSERT INTO transaction (accountNumber, transactionDate, transactionType, transactionAmount, transactionContent) VALUES ('$senderAccountNumber', NOW(), '$transactionTypeSender', $amount, '$transactionContentSender')";
                                mysqli_query($conn, $sqlSender);

                                // Chèn dữ liệu vào bảng transaction cho người nhận (transactionType là "Nhận Tiền")
                                $transactionTypeReceiver = "Nhận tiền";
                                $transactionContentReceiver = "Nhận khoản chuyển từ số tài khoản $senderAccountNumber với nội dung:  $content";
                                $sqlReceiver = "INSERT INTO transaction (accountNumber, transactionDate, transactionType, transactionAmount, transactionContent) VALUES ('$accountNumber', NOW(), '$transactionTypeReceiver', $amount, '$transactionContentReceiver')";
                                mysqli_query($conn, $sqlReceiver);

                                // Lưu thông tin giao dịch vào session để hiển thị trong Notification_transfer.php
                                $_SESSION['amount'] = $amount;
                                $_SESSION['senderNewBalance'] = $senderNewBalance;
                                $_SESSION['receiverNewBalance'] = $receiverNewBalance;
                                $_SESSION['transactionContentSender'] = $transactionContentSender;
                                $_SESSION['transactionContentReceiver'] = $transactionContentReceiver;

                                // Chuyển hướng đến trang thông báo
                                header("Location: Notification_transfer.php?accountNumber=$accountNumber");
                                exit();

                                } else {
                                    $response['success'] = false;
                                    $response['message'] = "Số tài khoản người nhận không tồn tại!";
                                }
                            } else {
                                $response['success'] = false;
                                $response['message'] = "Số dư trong tài khoản không đủ!";
                            }
                        } else {
                            $response['success'] = false;
                            $response['message'] = "Bạn không thể chuyển tiền cho chính mình!";
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Không tìm thấy thông tin tài khoản người chuyển!";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "Mật khẩu không đúng!";
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Không tìm thấy thông tin đăng nhập!";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Người dùng chưa đăng nhập!";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Vui lòng điền đầy đủ tất cả các trường bắt buộc!";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Yêu cầu không hợp lệ!";
}

// Trả về phản hồi dưới dạng chuỗi JSON
echo json_encode($response);
?>
