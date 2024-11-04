<?php
include 'connect.php';
session_start(); 

$filter = $_GET['filter'] ?? 'all';

if (isset($_SESSION['loggedInLoginName'])) {
    $loggedInLoginName = $_SESSION['loggedInLoginName'];

    $sql_transaction = "SELECT t.transactionID, t.accountNumber, t.transactionDate, t.transactionType, t.transactionAmount, t.transactionContent
            FROM transaction t
            INNER JOIN customerAccount ca ON t.accountNumber = ca.accountNumber
            INNER JOIN customer c ON ca.id = c.id
            WHERE c.loginName = '$loggedInLoginName'";

    if ($filter !== 'all') {
        switch ($filter) {
            case 'today':
                $sql_transaction .= " AND DATE(t.transactionDate) = CURDATE()";
                break;
            case 'this_week':
                $sql_transaction .= " AND YEARWEEK(t.transactionDate) = YEARWEEK(CURDATE())";
                break;
            case 'this_month':
                $sql_transaction .= " AND MONTH(t.transactionDate) = MONTH(CURDATE()) AND YEAR(t.transactionDate) = YEAR(CURDATE())";
                break;
            default:
                break;
        }
    }

    $result_transaction = $conn->query($sql_transaction);

    if ($result_transaction->num_rows > 0) {
        $transactions = array();
        while ($row = $result_transaction->fetch_assoc()) {
            $transactions[] = $row;
        }
        $transactions = array_reverse($transactions);
        echo "<div style='display: flex; justify-content: center;'>
        <table border='1'>
            <tr>
                <th> STT </th>
                <th> Số tài khoản</th>
                <th> Ngày giao dịch</th>
                <th> Loại giao dịch</th>
                <th> Số tiền</th>
                <th> Nội dung</th>
            </tr>";

        $counter = 1;
        foreach ($transactions as $transaction) {
            echo "<tr>
                    <td style='text-align: center;'>" . $counter++ . "</td>
                    <td>" . $transaction["accountNumber"] . "</td>
                    <td>" . date('H:i:s d/m/Y', strtotime($transaction["transactionDate"])) . "</td>
                    <td>" . $transaction["transactionType"] . "</td>
                    <td>" . $transaction["transactionAmount"] . "</td>
                    <td>" . $transaction["transactionContent"] . "</td>
                </tr>";
        }
        echo "</table></div>";
    } else {
        echo '<p style ="text-align: center"> Không có dữ liệu giao dịch ! </p>';
    }
} else {
    echo '<p style ="text-align: center"> Vui lòng đăng nhập để xem dữ liệu giao dịch ! </p>';
}

$conn->close();
?>