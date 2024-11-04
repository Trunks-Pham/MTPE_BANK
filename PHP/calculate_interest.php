<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $principal = $_POST['principal'];
    $term = $_POST['term'];

    $sql = "SELECT interestRate FROM interestRateByTerm WHERE month = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $term);
    $stmt->execute();
    $stmt->bind_result($rate);
    $stmt->fetch();

    $interest = $principal * ($rate/12) * $term;
    $principalNumeric = $principal;
    $interestNumeric = $interest;
    
    // Định dạng số
    $principal = number_format($principalNumeric, 0, ',', '.');
    $interest = number_format($interestNumeric, 0, ',', '.');
    
    echo "Số tiền lãi nhận được:<b> $interest </b>VNĐ.<br>";
    echo "Tổng số tiền nhận được khi đáo hạn: <b>" . number_format($principalNumeric + $interestNumeric, 0, ',', '.') . "</b> VNĐ.";
}
?>
