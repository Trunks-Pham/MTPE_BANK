<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loggedInLoginName = $_SESSION['loggedInLoginName'];
    $savingsBookID = $_POST['savingsID'];
    $withdrawAmount = $_POST['withdrawAmount'];

    // Lấy thông tin sổ tiết kiệm
    $savingsBookQuery = "SELECT savingsAmount, savingsTerm, interestRate FROM savingsBook WHERE savingsBookID = $savingsBookID";
    $result = mysqli_query($conn, $savingsBookQuery);
    $row = mysqli_fetch_assoc($result);
    $currentSavingsAmount = $row['savingsAmount'];
    $savingsTerm = $row['savingsTerm'];
    $interestRate = $row['interestRate'];

    // Kiểm tra xem người dùng đã nhấn vào nút "Rút Tiền" hay "Đáo Hạn"
    if (isset($_POST['withdraw'])) {
        // Kiểm tra xem có đủ tiền trong sổ tiết kiệm để rút không
        if ($withdrawAmount > $currentSavingsAmount) {
            echo "Số tiền rút vượt quá số dư trong sổ tiết kiệm!";
        } else {
            // Tính lại số tiền tiết kiệm và lãi suất theo số tiền rút
            $newSavingsAmount = $currentSavingsAmount - $withdrawAmount;
            $interestAmountPerTerm = ($newSavingsAmount * ($savingsTerm / 12) * ($interestRate)); // Tính lãi suất dựa trên công thức

            // Cập nhật số dư trong tài khoản
            $updateBalanceQuery = "UPDATE customerAccount SET balance = balance + $withdrawAmount WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName')";
            mysqli_query($conn, $updateBalanceQuery);

            // Cập nhật số tiền tiết kiệm và lãi suất mới vào cơ sở dữ liệu
            $updateSavingsQuery = "UPDATE savingsBook SET savingsAmount = $newSavingsAmount, interestAmountPerTerm = $interestAmountPerTerm WHERE savingsBookID = $savingsBookID";
            mysqli_query($conn, $updateSavingsQuery);

            echo "Rút tiền thành công!";
            header("Location: indexCustomer.php");
            exit;
        }
    } elseif (isset($_POST['mature'])) {
        // Xử lý logic sau khi đáo hạn
        // Tính lãi suất và số tiền mới
        $interestAmountPerTerm = ($currentSavingsAmount * ($savingsTerm / 12) * ($interestRate));
        $newBalance = $currentSavingsAmount + $interestAmountPerTerm;

        // Cập nhật số dư trong tài khoản
        $updateBalanceQuery = "UPDATE customerAccount SET balance = balance + $newBalance WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName')";
        mysqli_query($conn, $updateBalanceQuery);

        // Xóa sổ tiết kiệm đã đáo hạn
        $deleteSavingsQuery = "DELETE FROM savingsBook WHERE savingsBookID = $savingsBookID";
        mysqli_query($conn, $deleteSavingsQuery);

        echo "Đáo hạn thành công!";
        header("Location: indexCustomer.php");
        exit;
    }
}
?>
