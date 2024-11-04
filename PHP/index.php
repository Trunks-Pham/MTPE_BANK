<?php
session_start();

$message = "";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "email_customer_mtpebank";

// Kiểm tra xác nhận dữ liệu nhập từ người dùng
if(isset($_POST['email']) && !isset($_SESSION['email_submitted'])) { 
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); // Xác thực định dạng email
    
    if($email === false) {
        $message = "Email không hợp lệ!";
    } else {
        // Kết nối cơ sở dữ liệu và thực hiện câu lệnh SQL
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO email_subscriptions (email) VALUES (?)");
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute() === TRUE) {
            $_SESSION['email_submitted'] = true;
            $message = "Chào mừng bạn $email đã thành một phần của hệ sinh thái MTPE BANK !!!";
            
            // Gửi email cảm ơn
            require 'thanksemail_helper.php'; // Đường dẫn tới tệp gửi email
            sendThankYouEmail($email);
        } else {
            $message = "Có lỗi xảy ra khi gửi email! " . $conn->error;
        }
        $stmt->close();
     
        $conn->close();
    }
}

// Đóng thông báo nếu đã được đăng ký hoặc gửi email
if(isset($_SESSION['email_submitted']) && !isset($_SESSION['closed_message'])) {
    $message_display = true;
} else {
    $message_display = false;
}

// Xử lý sự kiện đóng thông báo
if(isset($_POST['close_message'])) {
    $_SESSION['closed_message'] = true;
}
?> 


<!DOCTYPE html>
<html>
<head>
    <title>MTPE BANK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="../IMAGES/Logo/logo 1.png">
    <link href="https://fonts.googleapis.com/css2?family=Play&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
    <link href="../CSS/style.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?> 

    <div id="bodymain">

    </div>
    <article class="ending">
        <figure class="end_text">
            <h3><b>Tải Ngay Ứng Dụng <span style="color:#0099CC;"> MINI BANKING</span> Để Có Trải Nghiệm Tuyệt Vời</b></h3>
            <div class="store" style="display: flex; " >
                <a href="https://play.google.com/store/apps?hl=vi" target="_blank"> <img src="../IMAGES/Logo/CHplay.png" width="50px" height="auto"></a>
                <a href="https://www.apple.com/app-store/" target="_blank"> <img src="../IMAGES/Logo/App-Store-Logo.png" width="50px" height="auto"></a>
                <a href="https://www.microsoft.com/vi-vn/store/top-free/apps/pc" target="_blank"> <img src="../IMAGES/Logo/Micosoft Store.png" width="50px" height="auto"></a>
            </div>
        </figure>

        <!-- GỬI MAIL -->
        <?php if($message_display): ?>
            <div id="successMessage" style="display:block; background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px;">
                <?php echo $message; ?>
                <button id="closeButton" style="background-color: transparent; border: none; color: #155724; float: right; cursor: pointer;">&times;</button>
            </div>
        <?php endif;?>

        <figure class="form-container">
            <form id="emailForm" method="post">
                <h3><b>Đăng ký nhận tin tức mới từ MTPE BANK</b></h3>
                <input type="email" placeholder="Nhập Email" name="email" required>
                <input type="submit" value="ĐĂNG KÝ NGAY"/>
            </form>
        </figure>
        
    </article>
    <!-- GỬI MAIL -->
        

    <img id="Loan" src="../IMAGES/Basic Color/whiteba.jpg" alt="Loan">
    <?php include 'Chatbot.php'; ?>
    <?php include 'footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="../js/script.js"></script>
<script>
        $(document).ready(function() {
            $("#closeButton").click(function() {
                $("#successMessage").hide();
            });
        });
    </script>
</body>
</html>