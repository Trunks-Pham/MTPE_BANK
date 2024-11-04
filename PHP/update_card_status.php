<?php
include 'connect.php'; // Kết nối đến cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin từ request
    $cardNumber = $_POST['cardNumber'];
    $newStatus = $_POST['newStatus'];

    // Cập nhật trạng thái thẻ trong cơ sở dữ liệu
    $sql = "UPDATE Cards SET cardStatus = '$newStatus' WHERE cardNumber = '$cardNumber'";
    if ($conn->query($sql) === TRUE) {
        echo "Cập nhật trạng thái thẻ thành công";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
}
?>
