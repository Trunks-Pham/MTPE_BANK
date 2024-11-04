<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedInLoginName'])) {
    echo "Bạn cần đăng nhập để truy cập trang này.";
    exit;
}

include 'connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $savingsName = $_POST['savingsName'];
    $savingsAmount = $_POST['savingsAmount'];
    $interestRate = $_POST['interestRate'];

    // Lấy accountNumber và balance từ tài khoản khách hàng đang đăng nhập
    $loggedInLoginName = $_SESSION['loggedInLoginName'];
    $accountInfoQuery = "SELECT id, accountNumber, balance FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName')";
    $accountInfoResult = mysqli_query($conn, $accountInfoQuery);

    if ($accountInfoResult && mysqli_num_rows($accountInfoResult) > 0) {
        $accountInfo = mysqli_fetch_assoc($accountInfoResult);
        $accountId = $accountInfo['id'];
        $accountNumber = $accountInfo['accountNumber'];
        $currentBalance = $accountInfo['balance'];

        // Kiểm tra nếu số tiền tiết kiệm mới vượt quá số dư hiện tại
        if ($savingsAmount > $currentBalance) {
            echo "Số tiền tiết kiệm không được vượt quá số dư hiện tại của tài khoản.";
            header("Location: indexCustomer.php");
            exit();
        }

        // Trừ số dư
        $newBalance = $currentBalance - $savingsAmount;
        $updateBalanceQuery = "UPDATE customerAccount SET balance = $newBalance WHERE id = $accountId";
        if (!mysqli_query($conn, $updateBalanceQuery)) {
            echo "Không thể cập nhật số dư tài khoản.";
            exit();
        }
    } else {
        echo "Không thể lấy thông tin tài khoản khách hàng.";
        exit();
    }

    $savingsTermQuery = "SELECT month FROM interestRateByTerm WHERE interestRate = '$interestRate'";
    $savingsTermResult = mysqli_query($conn, $savingsTermQuery);
    if ($savingsTermResult && mysqli_num_rows($savingsTermResult) > 0) {
        $savingsTermRow = mysqli_fetch_assoc($savingsTermResult);
        $savingsTerm = $savingsTermRow['month'];

        $interestAmountPerTerm = $savingsAmount * $interestRate * ($savingsTerm / 12);
        $query = "INSERT INTO savingsBook (accountNumber, savingsName, savingsTerm, savingsAmount, interestRate,interestAmountPerTerm) 
                  VALUES ('$accountNumber', '$savingsName', '$savingsTerm', '$savingsAmount', '$interestRate','$interestAmountPerTerm')";

        if (mysqli_query($conn, $query)) {
            echo "Sổ tiết kiệm đã được tạo thành công!";
            header("Location: indexCustomer.php");
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Không thể lấy thông tin kỳ hạn tiết kiệm.";
    }
}
?>
