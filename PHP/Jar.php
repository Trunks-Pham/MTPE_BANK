<style>
    .container {
        margin: 20px auto;
        text-align: center; 
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        margin: 10px;
        border: none;
        cursor: pointer;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
    }
    .btn:hover {
        background-color: #0056b3;
        color: #fff;
    }

    .main{
        margin: 50px 250px 50px 250px;
        border: 1px solid #C0C0C0;
    }
    
    .container{
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.19), 0px 6px 6px rgba(0, 0, 0, 0.23);
    }

    #createJar,#transferMoney,#transactionReport,#updateJar,#deleteJar{
        margin-right: 50px;
        margin-left: 50px;
    }

    @media  (max-width:1000px) {
        #createJar,#transferMoney,#transactionReport,#updateJar,#deleteJar{
        max-width: 100%;
        margin-right: 50px;
        margin-left: 50px;
        }.btn{
            padding: 8px 16px;
            margin: 8px; 
            font-size: 14px;
        }.container{
            max-width: 100%;
        }    
        .main{
            margin: 5px;
        }
    }
/* CSS cho bảng */
#jar_table {
    margin: 10px auto;
    width: 100%;
    overflow-x: auto; /* Thêm overflow-x để tạo thanh cuộn ngang */
}

#jar_table table {
    width: 100%;
    border-collapse: collapse;
    white-space: nowrap; /* Ngăn chặn việc quấn dòng */
    overflow-wrap: break-word; /* Phá vỡ từ khi cần thiết */
}

#jar_table th, #jar_table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

#jar_table th {
    background-color: #f2f2f2;
}

/* CSS cho hover */
#jar_table tr:hover {
    background-color: #f5f5f5;
    cursor: pointer;
}


</style>
</head>
<body>
<main class="main">
    <div class="container">
        <h2 style="margin-top: 20px;"><b>Hủ Chi Tiêu</b></h2>
        <button class="btn" onclick="showPage('createJar')">Tạo Hủ</button>
        <button class="btn" onclick="showPage('transferMoney')">Giao Dịch</button>
        <button class="btn" onclick="showPage('updateJar')">Cập Nhật Hủ</button>
        <button class="btn" onclick="showPage('deleteJar')">Xóa Hủ</button>
        <button class="btn" onclick="showPage('transactionReport')">Báo Cáo</button>
    </div>

<div id="createJar" class="page">
    <?php
    include 'connect.php';
    session_start(); 
    if (isset($_SESSION['loggedInLoginName'])) {
        $loggedInLoginName = $_SESSION['loggedInLoginName'];

        // Xử lý khi người dùng thực hiện tạo hủ chi tiêu
        if (isset($_POST['createJar'])) {
            $jarName = $_POST['jarName'];
            $jarAmount = $_POST['jarAmount'];
            $jarExpenseContent = $_POST['jarExpenseContent'];

            // Kiểm tra dữ liệu nhập vào
            if (empty($jarName) || empty($jarAmount) || empty($jarExpenseContent)) {
                echo "Vui lòng điền đầy đủ thông tin!";
            } else {
                // Thực hiện INSERT vào bảng expenseJar
                $sql_insert = "INSERT INTO expenseJar (jarName, jarAmount, accountNumber, jarExpenseContent) 
                VALUES (?, ?, (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = ?)), ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("ssss", $jarName, $jarAmount, $loggedInLoginName, $jarExpenseContent);

                // Thực hiện UPDATE số dư trong bảng customerAccount
                $sql_update_balance = "UPDATE customerAccount SET balance = balance - ? WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = ?))";
                $stmt_update_balance = $conn->prepare($sql_update_balance);
                $stmt_update_balance->bind_param("ss", $jarAmount, $loggedInLoginName);

                // Bắt đầu transaction để đảm bảo tính nhất quán của dữ liệu
                $conn->begin_transaction();

                if ($stmt_insert->execute() && $stmt_update_balance->execute()) {
                    // Nếu cả hai câu truy vấn đều thành công, commit transaction
                    $conn->commit();
                    echo "Hủ chi tiêu đã được tạo thành công!";
                } else {
                    // Nếu có lỗi xảy ra, rollback transaction
                    $conn->rollback();
                    echo "Đã xảy ra lỗi trong quá trình tạo hủ chi tiêu!";
                }

                $stmt_insert->close();
                $stmt_update_balance->close();

            }
        }

        // Hiển thị form tạo hủ chi tiêu
        echo '
        <h3 style="text-align: center;">Tạo Hủ Chi Tiêu</h3>
        <form id="createJarForm" action="" method="post"> 
            <div class="form-group">
                <label for="jarName">Tên Hủ Chi Tiêu:</label>
                <input type="text" id="jarName" name="jarName">
            </div>

            <div class="form-group">
                <label for="jarAmount">Số Tiền:</label>
                <input type="text" id="jarAmount" name="jarAmount" pattern="\d*" style="direction: rtl;">
            </div>

            <div class="form-group">
                <label for="jarExpenseContent">Nội Dung Chi Tiêu:</label>
                <input type="text" id="jarExpenseContent" name="jarExpenseContent">
            </div>
            <div class="form-group">
                <input type="submit" name="createJar" value="Tạo Hủ Chi Tiêu">
            </div>
        </form>';
    }
    ?>
    </div>


    <div id="transferMoney" class="page" style="display: none;">
        <?php

        if (isset($_SESSION['loggedInLoginName'])) {
            $loggedInLoginName = $_SESSION['loggedInLoginName'];

            // Xử lý khi người dùng thực hiện chuyển tiền từ hủ chi tiêu
            if (isset($_POST['transferFromJar'])) {
                $jarID = $_POST['jarID'];
                $accountNumber = $_POST['accountNumber'];
                $jarAmount = $_POST['jarAmount'];

                // Kiểm tra dữ liệu nhập vào
                if (empty($jarID) || empty($accountNumber) || empty($jarAmount)) {
                    echo "Vui lòng điền đầy đủ thông tin!";
                } else {
                    // Lấy thông tin về số dư trong hủ chi tiêu
                    $sql_select_jar = "SELECT jarAmount FROM expenseJar WHERE jarID = ?";
                    $stmt_select_jar = $conn->prepare($sql_select_jar);
                    $stmt_select_jar->bind_param("s", $jarID);
                    $stmt_select_jar->execute();
                    $result_select_jar = $stmt_select_jar->get_result();
                    $row_jar = $result_select_jar->fetch_assoc();
                    $jarBalance = $row_jar['jarAmount'];
                    $stmt_select_jar->close();

                    // Kiểm tra xem số tiền trong hủ có đủ để chuyển không
                    if ($jarAmount > $jarBalance) {
                        echo "Số tiền trong hủ không đủ!";
                    } else {
                        // Thực hiện trừ số tiền trong hủ
                        $sql_update_jar = "UPDATE expenseJar SET jarAmount = jarAmount - ? WHERE jarID = ?";
                        $stmt_update_jar = $conn->prepare($sql_update_jar);
                        $stmt_update_jar->bind_param("ss", $jarAmount, $jarID);
                        $stmt_update_jar->execute();
                        $stmt_update_jar->close();

                        // Thực hiện cộng tiền cho người nhận
                        $sql_update_balance = "UPDATE customerAccount SET balance = balance + ? WHERE accountNumber = ?";
                        $stmt_update_balance = $conn->prepare($sql_update_balance);
                        $stmt_update_balance->bind_param("ss", $jarAmount, $accountNumber);
                        $stmt_update_balance->execute();
                        $stmt_update_balance->close();

                        // Thực hiện thêm thông tin giao dịch vào bảng jarTransaction
                        $jarTransactionName = "Chuyển tiền từ hủ chi tiêu đến $accountNumber";
                        $sql_insert_transaction = "INSERT INTO jarTransaction (jarID, jarTransactionName, jarTransactionAmount) VALUES (?, ?, ?)";
                        $stmt_insert_transaction = $conn->prepare($sql_insert_transaction);
                        $stmt_insert_transaction->bind_param("ssd", $jarID, $jarTransactionName, $jarAmount);
                        $stmt_insert_transaction->execute();
                        $stmt_insert_transaction->close();

                        echo "Giao dịch thành công!";
                    }
                }
            }

            // Hiển thị form chuyển khoản từ hủ chi tiêu
            echo '
            <h3 style="text-align: center;">Giao Dịch Hủ</h3>
            <form id="transferFromJarForm" action="" method="post"> 
                <div class="form-group">
                    <label for="jarID">Chọn Hủ Chi Tiêu:</label>
                    <select id="jarID" name="jarID">
            ';

            // Truy vấn danh sách hủ chi tiêu của người dùng
            $sql_jars = "SELECT jarID, jarName, jarAmount FROM expenseJar WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName'))";
            $result_jars = $conn->query($sql_jars);

            // Hiển thị danh sách hủ chi tiêu trong dropdown menu
            while ($row_jar = $result_jars->fetch_assoc()) {
                echo '<option value="' . $row_jar["jarID"] . '">' . $row_jar["jarName"] . ' - Số dư: ' . number_format($row_jar["jarAmount"], 0, ',', '.') . ' VNĐ</option>';
            }

            echo '
                    </select>
                </div>
                <div class="form-group">
                    <label for="accountNumber">Số Tài Khoản Nhận Tiền:</label>
                    <input type="text" id="accountNumber" name="accountNumber" onblur="getRecipientName(this.value)" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="Nhập số tài khoản nhận tiền">
                </div>

                <div class="form-group">
                    <label for="recipientName">Tên Người Nhận:</label>
                    <input type="text" id="recipientName" name="recipientName" readonly>
                </div>

                <div class="form-group">
                    <label for="jarAmount">Số Tiền:</label>
                    <input type="text" id="jarAmount" name="jarAmount" pattern="\d*" style="direction: rtl;">
                </div>
                
                <div class="form-group">
                    <input type="submit" name="transferFromJar" value="Chuyển Khoản">
                </div>
            </form>
            
            <div id="confirmation" class="confirmation" style="display:none;">
                <p style="text-align: center;"><strong>Thông Báo Giao Dịch:</strong></p>
                <p>Đến Ngân Hàng: <b><span id="bankConfirmation"></span></b></p>
                <p>Số Tài Khoản Người Nhận: <b><span id="accountNumberConfirmation"></span></b></p>
                <p>Tên Người Nhận: <b><span id="nameConfirmation"></span></b></p>
                <p>Số Tiền Giao Dịch: <b><span id="jarAmountConfirmation"></span> VNĐ</b></p>
                <button id="submitTransfer">Xác Nhận Chuyển Tiền</button>
                <button id="comback">Quay lại</button>
            </div>';   
        }
        ?>
    </div>

    <div id="updateJar" class="page" style="display: none;">
    <?php

        // Xử lý khi người dùng thực hiện cập nhật hủ chi tiêu
        if (isset($_POST['updateJar'])) {
            $jarID = $_POST['selectJar'];
            $updatedAmount = $_POST['updatedAmount'];

            // Kiểm tra dữ liệu nhập vào
            if (empty($jarID) || empty($updatedAmount)) {
                echo "Vui lòng điền đầy đủ thông tin!";
            } else {
                // Lấy thông tin về số tiền hiện tại của hủ
                $sql_select_jar = "SELECT jarAmount FROM expenseJar WHERE jarID = ?";
                $stmt_select_jar = $conn->prepare($sql_select_jar);
                $stmt_select_jar->bind_param("s", $jarID);
                $stmt_select_jar->execute();
                $result_select_jar = $stmt_select_jar->get_result();
                $row_jar = $result_select_jar->fetch_assoc();
                $currentAmount = $row_jar['jarAmount'];
                $stmt_select_jar->close();

                // Xác định phương án cộng hoặc trừ tiền từ balance
                if ($updatedAmount > $currentAmount) {
                    // Nếu số tiền mới lớn hơn số tiền hiện tại của hủ, trừ tiền từ balance và cộng vào hủ
                    $amountDifference = $updatedAmount - $currentAmount;

                    // Thực hiện trừ tiền từ balance
                    $sql_update_balance = "UPDATE customerAccount SET balance = balance - ? WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = ?))";
                    $stmt_update_balance = $conn->prepare($sql_update_balance);
                    $stmt_update_balance->bind_param("ss", $amountDifference, $loggedInLoginName);
                    $stmt_update_balance->execute();
                    $stmt_update_balance->close();

                    // Thực hiện cập nhật số tiền mới vào hủ
                    $sql_update_jar = "UPDATE expenseJar SET jarAmount = ? WHERE jarID = ?";
                    $stmt_update_jar = $conn->prepare($sql_update_jar);
                    $stmt_update_jar->bind_param("ss", $updatedAmount, $jarID);
                    $stmt_update_jar->execute();
                    $stmt_update_jar->close();

                    echo "Số tiền hủ chi tiêu đã được thay đổi thành công!";
                } else {
                    // Nếu số tiền mới nhỏ hơn hoặc bằng số tiền hiện tại của hủ, thực hiện cộng tiền từ balance và trừ ra khỏi hủ
                    $amountDifference = $currentAmount - $updatedAmount;

                    // Thực hiện cộng tiền từ balance
                    $sql_update_balance = "UPDATE customerAccount SET balance = balance + ? WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = ?))";
                    $stmt_update_balance = $conn->prepare($sql_update_balance);
                    $stmt_update_balance->bind_param("ss", $amountDifference, $loggedInLoginName);
                    $stmt_update_balance->execute();
                    $stmt_update_balance->close();

                    // Thực hiện cập nhật số tiền mới vào hủ
                    $sql_update_jar = "UPDATE expenseJar SET jarAmount = ? WHERE jarID = ?";
                    $stmt_update_jar = $conn->prepare($sql_update_jar);
                    $stmt_update_jar->bind_param("ss", $updatedAmount, $jarID);
                    $stmt_update_jar->execute();
                    $stmt_update_jar->close();

                    echo "Hủ chi tiêu đã được cập nhật thành công!";
                }
            }
        }

        // Hiển thị form cập nhật hủ chi tiêu
        echo '
        <h3 style="text-align: center;">Thay Đổi Số Tiền Hủ Chi Tiêu</h3>
        <form id="updateJarForm" action="" method="post"> 
            <div class="form-group">
                <label for="selectJar">Chọn Hủ Chi Tiêu:</label>
                <select id="selectJar" name="selectJar">
        ';

        // Truy vấn danh sách hủ chi tiêu của người dùng
        $sql_jars = "SELECT jarID, jarName, jarAmount FROM expenseJar WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName'))";
        $result_jars = $conn->query($sql_jars);

        // Hiển thị danh sách hủ chi tiêu trong dropdown menu
        while ($row_jar = $result_jars->fetch_assoc()) {
            echo '<option value="' . $row_jar["jarID"] . '">' . $row_jar["jarName"] . ' - Số dư: ' . number_format($row_jar["jarAmount"], 0, ',', '.') . ' VNĐ</option>';
        }

        echo '
                </select>
            </div>
            <div class="form-group">
                <label for="updatedAmount">Số Tiền Muốn Thay Đổi:</label>
                <input type="text" id="updatedAmount" name="updatedAmount" pattern="\d*" style="direction: rtl;">
            </div>
 
            <div class="form-group">
                <input type="submit" name="updateJar" value="Xác Nhận">
            </div>
        </form>';

    ?>
    </div>

    <div id="deleteJar" class="page" style="display: none;">
        <h3 style="text-align: center;">Xóa Hủ Chi Tiêu Vô Tình Tạo và Không Muốn Xài</h3>
        <form id="deleteJarForm" action="" method="post"> 
            <div class="form-group">
                <label for="selectJarToDelete">Chọn Hủ Chi Tiêu:</label>
                <select id="selectJarToDelete" name="selectJarToDelete">
                    <?php
                    // Thực hiện truy vấn SQL để lấy danh sách các hủ chi tiêu của người dùng
                    $sql_jars = "SELECT jarID, jarName FROM expenseJar WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = '$loggedInLoginName'))";
                    $result_jars = $conn->query($sql_jars);

                    // Hiển thị danh sách các hủ chi tiêu trong dropdown menu
                    while ($row_jar = $result_jars->fetch_assoc()) {
                        echo '<option value="' . $row_jar["jarID"] . '">' . $row_jar["jarName"] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" name="deleteJar" value="Xóa Hủ">
            </div>
        </form>

        <?php
        // Xử lý khi người dùng gửi yêu cầu xóa hủ chi tiêu
        if(isset($_POST['deleteJar'])) {
            $jarIDToDelete = $_POST['selectJarToDelete'];

            // Thực hiện truy vấn SQL để lấy thông tin số tiền của hủ cần xóa
            $sql_select_jar_amount = "SELECT jarAmount FROM expenseJar WHERE jarID = ?";
            $stmt_select_jar_amount = $conn->prepare($sql_select_jar_amount);
            $stmt_select_jar_amount->bind_param("s", $jarIDToDelete);
            $stmt_select_jar_amount->execute();
            $result_select_jar_amount = $stmt_select_jar_amount->get_result();
            $row_jar_amount = $result_select_jar_amount->fetch_assoc();
            $jarAmountToDelete = $row_jar_amount['jarAmount'];
            $stmt_select_jar_amount->close();

            // Thực hiện cập nhật số tiền vào balance của người dùng
            $sql_update_balance = "UPDATE customerAccount SET balance = balance + ? WHERE accountNumber = (SELECT accountNumber FROM customerAccount WHERE id = (SELECT id FROM customer WHERE loginName = ?))";
            $stmt_update_balance = $conn->prepare($sql_update_balance);
            $stmt_update_balance->bind_param("ds", $jarAmountToDelete, $loggedInLoginName);
            $stmt_update_balance->execute();
            $stmt_update_balance->close();

            // Thực hiện xóa hủ chi tiêu khỏi cơ sở dữ liệu
            $sql_delete_jar = "DELETE FROM expenseJar WHERE jarID = ?";
            $stmt_delete_jar = $conn->prepare($sql_delete_jar);
            $stmt_delete_jar->bind_param("s", $jarIDToDelete);
            if ($stmt_delete_jar->execute()) {
                echo "Hủ chi tiêu đã được xóa thành công và số tiền đã được trả lại vào balance!";
            } else {
                echo "Đã xảy ra lỗi trong quá trình xóa hủ chi tiêu!";
            }
            $stmt_delete_jar->close();
        }
        ?>
    </div>


    <div id="transactionReport" class="page" style="display: none;">  
        <?php
        if (isset($_SESSION['loggedInLoginName'])) {
            $loggedInLoginName = $_SESSION['loggedInLoginName'];
            // Truy vấn SQL để lấy danh sách các hủ và lịch sử giao dịch hủ
            $sql = "
            SELECT ej.jarID, ej.jarName, ej.jarAmount, ej.jarDateCreated,
                jt.transactionID, jt.jarTransactionName, jt.jarTransactionAmount, jt.jarTransactionDate
            FROM expenseJar ej
            LEFT JOIN jarTransaction jt ON ej.jarID = jt.jarID
            LEFT JOIN customerAccount ca ON ej.accountNumber = ca.accountNumber
            LEFT JOIN customer c ON ca.id = c.id
            WHERE c.loginName = '$loggedInLoginName'
            ";
            

            $result = $conn->query($sql);

            // Mảng để lưu dữ liệu cho biểu đồ
            $jarData = [];

            // Kiểm tra xem có dữ liệu trả về từ truy vấn hay không
            if ($result->num_rows > 0) {
                // Hiển thị báo cáo giao dịch hủ
                echo "<h2 style='text-align: center;'>Danh Sách Hủ Thực Hiện Giao Dịch</h2>";
                echo "<div id='jar_table'>";
                echo "<table border='1'>";
                echo "<tr> 
                        <th>Tên Hủ</th> 
                        <th>Số Tiền Hiện Có</th> 
                        <th>Ngày Tạo Hủ</th>
                        <th>Tên Giao Dịch</th>
                        <th>Ngày Giao Dịch</th>
                        <th>Số Tiền Giao Dịch</th>
                    </tr>";

                // Hiển thị từng hàng cho danh sách các giao dịch và hủ
                while ($row = $result->fetch_assoc()) {
                    echo "<tr> 
                            <td>" . $row["jarName"] . "</td> 
                            <td>" . number_format($row["jarAmount"], 0, ',', '.') . " VNĐ" . "</td> 
                            <td>" . $row["jarDateCreated"] . "</td>
                            <td>" . $row["jarTransactionName"] . "</td>
                            <td>" . $row["jarTransactionDate"] . "</td>
                            <td>" . number_format($row["jarTransactionAmount"], 0, ',', '.') . " VNĐ" .  "</td>
                        </tr>";
                    
                    // Tính tổng số tiền cho mỗi hủ
                    if (isset($jarData[$row["jarName"]])) {
                        $jarData[$row["jarName"]] += $row["jarTransactionAmount"];
                    } else {
                        $jarData[$row["jarName"]] = $row["jarTransactionAmount"];
                    }
                }

                echo "</table></div>"; // Đóng bảng HTML
            } else {
                echo "<p style='text-align: center;'>Không có giao dịch nào được tìm thấy.</p>";
            }

            // Đóng kết nối
            $conn->close();
        }
        ?>
    </div>

 
    
</main>
</body>


<script>

//////////
    document.getElementById('jarAmount').addEventListener('input', function(event) {
    event.target.value = event.target.value.replace(/[^0-9]/g, '');
});

    
    function showPage(pageId) {
        var pages = document.getElementsByClassName('page');
        for (var i = 0; i < pages.length; i++) {
            pages[i].style.display = 'none';
        }
        document.getElementById(pageId).style.display = 'block';
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
                $('#recipientName').val(response);
            },
            error: function(xhr, status, error){
                console.log(error);
            }
        });
    });
});

function showPreview() {
    $('#overlay').show();
    $('#confirmation').show();

    var bank = $('#bank').val();
    var accountNumber = $('#accountNumber').val();
    var name = $('#recipientName').val();
    var jarAmount = $('#jarAmount').val();
    $('#bankConfirmation').text(bank);
    $('#accountNumberConfirmation').text(accountNumber);
    $('#nameConfirmation').text(name);
    $('#jarAmountConfirmation').text(jarAmount);
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


//////////////////////////////////////
 
//////////////////////////////////////
</script>
 