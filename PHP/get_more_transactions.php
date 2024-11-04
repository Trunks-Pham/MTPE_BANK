<?php
// Kết nối đến cơ sở dữ liệu của bạn
include 'connect.php';

// Lấy số lượng giao dịch hiện tại từ yêu cầu AJAX
$count = $_POST['count'];

// Xây dựng truy vấn SQL để lấy thêm giao dịch từ cơ sở dữ liệu
$query = "SELECT * FROM transaction LIMIT $count, 11";
$result = $conn->query($query);

// Kiểm tra xem truy vấn có thành công hay không
if ($result === false) {
    // Nếu có lỗi, in ra thông báo lỗi
    echo "Lỗi truy vấn: " . $conn->error;
} else {
    // Kiểm tra xem có bản ghi nào không
    if ($result->num_rows > 0) {
        // Loop qua từng dòng kết quả và hiển thị ra HTML
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td style='text-align: center;'>" . $count++ . "</td> 
                    <td style='text-align: center;'>" . date("H:i:s d/m/Y", strtotime($row["transactionDate"])) . "</td>
                    <td style='text-align: center;'>" . $row["transactionType"] . "</td>
                    <td style='text-align: right;'>" . number_format($row["transactionAmount"], 0, ',', '.') . " VNĐ"  . "</td>
                    <td>" . $row["transactionContent"] . "</td>
                </tr>";
        }
    } else {
        // Nếu không có giao dịch nào nữa, thông báo cho người dùng
        echo "<tr><td colspan='5' style='text-align:center;'>Không có giao dịch nào khác nữa.</td></tr>";
    }
}

// Đóng kết nối
$conn->close();
?>
