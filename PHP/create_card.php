<?php
session_start();
include 'connect.php';

// Lấy thông tin cần thiết từ session hoặc từ dữ liệu POST
if (isset($_SESSION['loggedInLoginName'])) {
    $loggedInLoginName = $_SESSION['loggedInLoginName'];
    // Bổ sung mã lấy dữ liệu khác nếu cần thiết
}

// Kiểm tra tồn tại của accountNumber trong bảng customerAccount
$sql_check_account = "SELECT accountNumber FROM customerAccount WHERE id IN (SELECT [nhập] FROM [nhập] WHERE loginName = '$loggedInLoginName')";
$result_check_account = $conn->query($sql_check_account);

if ($result_check_account->num_rows > 0) {
    // Thêm thông tin vào bảng Cards nếu accountNumber tồn tại (Nó tồn tại mà nó khumm zô huhuuuu)
    $sql_card = "INSERT INTO Cards (cardNumber, accountNumber, cardStatus, pinCode) VALUES ('$cardNumber', 'Chưa Kích Hoạt', '$pinCode')";
    if (mysqli_query($conn, $sql_card)) {
        echo "Thẻ đã được tạo thành công!";
    } else {
        echo "Lỗi khi tạo thẻ: " . mysqli_error($conn);
    }
} else {
    echo "Lỗi khi tạo thẻ: accountNumber không tồn tại trong bảng customerAccount";
}


// Thêm thông tin vào bảng Cards
$cardNumber = '2301' . mt_rand(100000000000000, 999999999999999); // Số thẻ gồm 16 số, bắt đầu bằng 2301
$pinCode = mt_rand(100, 999); // Mã CVC gồm 3 số
$sql_card = "INSERT INTO Cards (cardNumber, accountNumber, cardStatus, pinCode) VALUES ('$cardNumber', 'Chưa Kích Hoạt', '$pinCode')";

if (mysqli_query($conn, $sql_card)) {
    echo "Thẻ đã được tạo thành công!";
} else {
    echo "Lỗi khi tạo thẻ: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
