<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "minibankingdata";//minibankingdata

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
die("Lỗi Kết Nối: " . $conn->connect_error);
}
?>