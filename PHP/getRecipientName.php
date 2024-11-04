<?php
// include 'connect.php';

// if(isset($_GET['accountNumber'])) {
//     $accountNumber = $_GET['accountNumber'];

//     $sql = "SELECT customer.name FROM customer INNER JOIN customerAccount ON customer.id = customerAccount.id WHERE customerAccount.accountNumber = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("s", $accountNumber);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         echo '' . $row['name'] . '';
//     } else {
//         echo "Không tìm thấy người nhận";
//     }
//     $stmt->close();
// }


//Có Khai Báo EMAIL NGƯỜI NHẬN TIỀN recipientEmail
include 'connect.php';

if(isset($_GET['accountNumber'])) {
    $accountNumber = $_GET['accountNumber'];

    $sql = "SELECT customer.name, customer.email FROM customer INNER JOIN customerAccount ON customer.id = customerAccount.id WHERE customerAccount.accountNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $accountNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $recipientName = $row['name'];
        $recipientEmail = $row['email']; 
        echo $recipientName;
    } else {
        echo "Không tìm thấy người nhận";
    }
    $stmt->close();
}  
?>
