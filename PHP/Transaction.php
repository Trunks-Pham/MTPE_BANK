<style>
.notification {
    position: fixed;
    top: 50px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    display: none;
}

.notification.success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.notification.error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.notification-close {
    position: absolute;
    top: 5px;
    right: 5px;
    cursor: pointer;
    color: #999;
}
#overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); 
    z-index: 999;
}
.confirmation {
    display: none;
    position: fixed;
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    z-index: 9999;
}
button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block; 
    margin-top: 20px; 
}

button:hover {
    background-color: #1E90FF;
}

input[type="button"] {
    padding: 10px 20px; 
    font-size: 16px; 
    background-color: #007bff; 
    color: #fff; 
    border: none; 
    border-radius: 5px; 
    cursor: pointer; 
}

input[type="button"]:hover {
    background-color: #1E90FF; 
}
h3{
    margin-top: 20px;
}

#comback {
    padding: 10px 20px;
    font-size: 16px; 
    background-color: #6c757d; 
    color: #fff; 
    border: none; 
    border-radius: 5px; 
    cursor: pointer; 
    margin-top: 10px; 
}

#comback:hover {
    background-color: #495057; 
}

.headTransaction{
    text-align: center;
    margin-left: 350px;
    margin-right: 350px;
}

.headbtn{ 
    text-align: center;
    margin-top: 20px;
}


.btn{
    max-width: 100%;
    background-color: white;
    border: 1px solid black;
}
.btn:hover{
    color: white;
    background-color: #1E90FF;
    border: 2px solid #007bff
}

@media only screen and (max-width: 1000px) {
    .headTransaction{
        margin-left: 5px;
        margin-right: 5px;
    }
}
 
@media screen and (max-width: 1000px) {
    .headbtn .btn {
        width: 100%;
        display: block;
        margin-top: 5px;
    }
}

</style>



<div id="TransactionBank" class="page">
<?php
    include 'connect.php';

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['loggedInLoginName'])) {
        $loggedInLoginName = $_SESSION['loggedInLoginName']; 

        $sqlBank = "SELECT customer.name, customer.phoneNumber, customer.email, customer.address, customerAccount.accountNumber, customerAccount.balance 
                    FROM customer
                    INNER JOIN customerAccount ON customer.id = customerAccount.id
                    WHERE customer.loginName = ?";

        $stmt = $conn->prepare($sqlBank);

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $loggedInLoginName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $formatted_balance = number_format($row["balance"], 0, ',', ' ');
                echo '
                <div class="cont">
                    <p for="account-name">Chủ Tài Khoản:<b> ' . $row["name"] . '</b></p>
                    <p for="account-number">Số Tài Khoản:<b> ' . $row["accountNumber"] . '</b></p>
                    <p for="balance">Số dư hiện tại: <b>' . $formatted_balance .' VNĐ</b></p>

                <form id="transferForm" action="process_transfer.php" method="post"> 
                    <div class="form-group" id="bankGroup">
                        <label for="bank">Chọn Ngân Hàng:</label>
                        <select id="bank" name="bank">
                            <option value="MTPE BANK"> MTPE BANK</option>
                            <option value="Timo"> Timo Digital Bank</option>
                            <option value="Vietcombank"> Vietcombank</option>
                            <option value="Techcombank">Techcombank</option>
                            <option value="ACB">ACB</option>
                            <option value="BIDV">Bank for Investment and Development of Vietnam (BIDV)</option>
                            <option value="Vietinbank">Vietinbank</option>
                            <option value="OCB">Orient Commercial Joint Stock Bank (OCB)</option>
                            <option value="Shinhan">Shinhan Bank</option>
                            <option value="UOB">United Overseas Bank (UOB)</option>
                            <option value="TPbank">Tien Phong Commercial Joint Stock Bank (TP Bank)</option>
                            <option value="HDbank">Housing development Commercial Joint Stock Bank (HDBank)</option>
                            <option value="Indovina">Indovina Bank</option>
                            <option value="VRB">Vietnam – Russia Joint Venture Bank</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="accountNumber">Số Tài Khoản Người Nhận:</label>
                        <input type="text" id="accountNumber" name="accountNumber" onblur="getRecipientName(this.value)" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>
                    <div class="form-group">
                        <label for="recipientName">Tên Người Nhận:</label>
                        <input type="text" class="recipientName" name="recipientName" readonly>
                    </div>

                    <div class="form-group">
                        <label for="amount">Số Tiền:</label>
                        <input type="text" id="amount" name="amount" oninput="formatCurrency(this)">
                    </div>
                

                    <div class="form-group">
                        <label for="content">Nội Dung Giao Dịch:</label>
                        <input type="text" id="content" name="content" placeholder="*Không bắt buộc nhenn">
                    </div>

                    <div class="form-group">
                        <label for="password">Mật Khẩu:</label>
                        <input type="password" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <input type="button" value="Xem Trước" onclick="showPreview()">
                    </div>

                </form>
                <div id="confirmation" class="confirmation" style="display:none;">
                    <p style="text-align: center;"><strong>Thông Báo Giao Dịch:</strong></p>
                    <p>Đến Ngân Hàng: <b><span id="bankConfirmation"></span></b></p>
                    <p>Số Tài Khoản Người Nhận: <b><span id="accountNumberConfirmation"></span></b></p>
                    <p>Tên Người Nhận: <b><span id="nameConfirmation"></span></b></p>
                    
                    <p>Số Tiền Giao Dịch: <b><span id="amountConfirmation"></span> VNĐ</b></p> 

                    <p>Nội dung giao dịch: <b><span id="contentConfirmation"></span></b></p>
                    <button id="submitTransfer">Xác Nhận Chuyển Tiền</button>
                    <button id="comback">Quay lại</button>
                </div>
            </div>';
            }
        }
    }
?>

</div>

<div id="overlay"></div>
<div id="notification" class="notification"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

function toggleContent(id) {
    var element = document.getElementById(id);
    if (element.style.display === "none") {
        element.style.display = "block";
    } else {
        element.style.display = "none";
    }
}

function formatCurrency(input) {
    var value = input.value.replace(/[^\d.]/g, '');
    input.value = value;
}


$(document).ready(function(){
    $(document).on('click', '.notification-close', function(){
        $('#notification').hide(); 
        $('#transferForm')[0].reset(); 
        $('#confirmation').hide(); 
        $('#overlay').hide(); 
    });
    
    $('#accountNumber').on('blur', function(){
        var accountNumber = $(this).val();
        $.ajax({
            url: 'getRecipientName.php',
            type: 'GET',
            data: { accountNumber: accountNumber },
            success: function(response){ 
                $('.recipientName').val(response);

            },
            error: function(xhr, status, error){
                console.log(error);
            }
        });
    });
});



// function showPreview() {
//     $('#overlay').show();
//     $('#confirmation').show();

//     var bank = $('#bank').val();
//     var accountNumber = $('#accountNumber').val();
//     var name = $('.recipientName').val();
//     var amount = $('#amount').val();
//     if(amount < 1000) {
//         alert('Số tiền giao dịch tối thiểu phải là 1000');
//         return;
//     }

//     var content =$('#content').val();
//     $('#bankConfirmation').text(bank);
//     $('#accountNumberConfirmation').text(accountNumber);
//     $('#nameConfirmation').text(name);
//     $('#amountConfirmation').text(amount);
//     $('#contentConfirmation').text(content);

// }

function showPreview() {
    $('#overlay').show();
    $('#confirmation').show();

    var bank = $('#bank').val();
    var accountNumber = $('#accountNumber').val();
    var name = $('.recipientName').val();
    var amount = parseFloat($('#amount').val()); // Chuyển đổi sang số thập phân để xử lý
    if(amount < 1000) {
        alert('Số tiền giao dịch tối thiểu phải là 1.000 VNĐ');
        return;
    }

    var content = $('#content').val();

    // Định dạng số tiền thành tiền tệ
    var amountFormatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);

    // Loại bỏ ký hiệu đặc biệt của tiền tệ
    amountFormatted = amountFormatted.replace(/₫/g, '');

    // Hiển thị dạng tiền tệ trong giao diện người dùng
    $('#bankConfirmation').text(bank);
    $('#accountNumberConfirmation').text(accountNumber);
    $('#nameConfirmation').text(name);
    $('#amountConfirmation').text(amountFormatted); // Sử dụng số tiền đã định dạng
    $('#contentConfirmation').text(content);
}



$('#submitTransfer').click(function(){
    $('#overlay').hide();
    $('#confirmation').hide();
    $.ajax({
        type: 'POST',
        url: 'process_transfer.php',
        data: $('#transferForm').serialize(), 
        success: function(response){
            var responseData = JSON.parse(response);
            var success = responseData.success;
            var message = responseData.message;

            var notification = $('#notification');
            notification.text(message);
            notification.removeClass('success error');
            if(success){
                notification.addClass('success');
            } else {
                notification.addClass('error');
            }
            notification.append('<span class="notification-close">&times;</span>');
            notification.show();

            $('#transferForm')[0].reset();
            $('#confirmation').hide();

            location.reload(); 
        },
        error: function(xhr, status, error){
            console.log(error); 
        }
    });
});



$("#comback").click(function() {
    $("#confirmation").hide();
    $("#overlay").hide();
});


</script>