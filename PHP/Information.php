<style>

.all {
    margin: 20px auto;
    max-width: 600px; 
    padding: 20px;
    border-radius: 5px;
    background-color: #ffff;
}

.infor {
    margin: 20px auto;
    max-width: 600px; 
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    
    transition: all 0.3s ease;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}
.infor:hover{
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}

h3{
    text-align: center;
}

#account-form{
    margin-left: 150px;
}

#account-form p {
    margin-bottom: 10px;
}

button[type="submit"] {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block; 
    margin-top: 20px; 
}

button[type="submit"]:hover {
    background-color: #007bfa;
}


@media (max-width: 600px) {
    .all, .infor {
        margin: 20px 10px;
        max-width: calc(100% - 20px);
    }

    #account-form {
        margin-left: 10px; 
        margin-right: 10px; 
    }
}

@media (min-width: 600px) {
    .all, .infor { 
        margin: 20px auto;
        max-width: 600px; 
    }

    #account-form {
        margin-left: 100px;
    }
}


</style>

<main class="main">
    <?php
    include 'connect.php';
    session_start();

    if (isset($_SESSION['loggedInLoginName'])) {
        $loggedInLoginName = $_SESSION['loggedInLoginName'];

        // Lấy thông tin người dùng từ CSDL
        $sql = "SELECT customer.name, customer.phoneNumber, customer.email, customer.address, customerAccount.accountNumber, customerAccount.balance
                FROM customer
                INNER JOIN customerAccount ON customer.id = customerAccount.id
                WHERE customer.loginName = '$loggedInLoginName'";

        $result = $conn->query($sql);

        if ($result !== false && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $formatted_balance = number_format($row["balance"], 0, ',', ' ');
                echo '<div class=all>
                        <h3>Thông Tin Của Tôi</h3>
                            <div class="infor">
                                <form id="account-form">
                                    <p for="account-name">Chủ Tài Khoản: <B>' . $row["name"] . '</B></p>
                                    <p>Tên Đăng Nhập: <B>' . $loggedInLoginName . '</B></p>
                                </form>
                            </div>

                            <div class="infor">
                                <form id="account-form">
                                    <p for="account-number">Số Tài Khoản: <B>' . $row["accountNumber"] . '</B></p>
                                    <p for="balance">Số Dư Hiện Tại: <B>' . $formatted_balance . ' VNĐ </B></p>
                                </form>
                            </div>

                            <div class="infor">
                                <form id="account-form">
                                    <p for="phone">Số Điện Thoại: <B>' . $row["phoneNumber"] . '</B></p>
                                    <p for="email">Email: <B>' . $row["email"] . '</B></p>
                                    <p for="address"> Địa Chỉ: <B>' . $row["address"] . '</B></p>
                                </form>
                            </div>

                            <div class="infor"> 
                                <form action="logout.php" method="post">
                                    <button type="submit">Đăng xuất</button>
                                </form>
                            </div>';

                // Hiển thị các nút button cho các chức năng khác
                echo '<div class="showPagebtn">
                        <button class="btn" onclick="toggleUpdateInfo()">Bạn Muốn Cập Nhật Thông Tin ?</button>
                        <button class="btn" onclick="showChangePassword()">Bạn Muốn Đổi Mật Khẩu?</button>
                    </div>';
            }
        } else {
            echo "Không tìm thấy thông tin khách hàng.";
        }
    } else {
        echo "Bạn chưa đăng nhập.";
    }
?> 

    <!-- Nội dung của trang "Cập Nhật Thông Tin" -->
    <div id="updateInfo" class="page" style="display: none;">
        <h3 style="text-align: center;">Cập Nhật Địa Chỉ</h3>
        <form id="updateInfoForm" action="" method="post"> 
            <div class="form-group">
                <label for="newAddress">Địa Chỉ Mới:</label>
                <input type="text" id="newAddress" name="newAddress" required>
            </div>
            <div class="form-group">
                <input type="submit" name="updateInfo" value="Cập Nhật Địa Chỉ">
            </div>
        </form>

        <?php
        if(isset($_POST['updateInfo'])) {
            $newAddress = $_POST['newAddress'];

            // Thực hiện truy vấn SQL để cập nhật địa chỉ
            $sql_update_info = "UPDATE customer SET address = ? WHERE loginName = ?";
            $stmt_update_info = $conn->prepare($sql_update_info);
            $stmt_update_info->bind_param("ss", $newAddress, $loggedInLoginName);

            if ($stmt_update_info->execute()) {
                echo "Địa chỉ đã được cập nhật thành công!";
            } else {
                echo "Đã xảy ra lỗi trong quá trình cập nhật địa chỉ!";
            }
            $stmt_update_info->close();
        }
        ?>
    </div>




<!-- Nội dung của trang "Đổi Mật Khẩu" -->
<?php
// Biến để kiểm tra xem mật khẩu đã được đổi thành công hay không
$passwordChanged = false;

// Thêm phần xử lý đổi mật khẩu vào phần kiểm tra khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra xem action là gì, nếu là changePassword thì thực hiện đổi mật khẩu
    if (isset($_POST['changePassword'])) {
        $oldPassword = md5($_POST['oldPassword']);
        $newPassword = md5($_POST['newPassword']);
        $confirmNewPassword = md5($_POST['confirmNewPassword']);

        // Kiểm tra mật khẩu cũ của người dùng
        $sql_check_old_password = "SELECT password FROM customerLogin WHERE loginName = ?";
        $stmt_check_old_password = $conn->prepare($sql_check_old_password);
        $stmt_check_old_password->bind_param("s", $loggedInLoginName);
        $stmt_check_old_password->execute();
        $result_check_old_password = $stmt_check_old_password->get_result();

        if ($result_check_old_password->num_rows > 0) {
            $row = $result_check_old_password->fetch_assoc();
            $hashedPassword = $row['password'];

            if ($hashedPassword != $oldPassword) {
                echo "Mật khẩu cũ không đúng.";
                exit();
            }
        } else {
            echo "Không tìm thấy tên đăng nhập.";
            exit();
        }

        // Kiểm tra mật khẩu mới và xác nhận mật khẩu mới
        if ($newPassword != $confirmNewPassword) {
            echo "Mật khẩu mới và xác nhận mật khẩu mới không khớp.";
            exit();
        }

        // Mã hóa mật khẩu mới và cập nhật vào cơ sở dữ liệu
        $sql_update_password = "UPDATE customerLogin SET password = ? WHERE loginName = ?";
        $stmt_update_password = $conn->prepare($sql_update_password);
        $stmt_update_password->bind_param("ss", $newPassword, $loggedInLoginName);

        if ($stmt_update_password->execute()) {
            $passwordChanged = true; // Đánh dấu rằng mật khẩu đã được đổi thành công
        } else {
            echo "Lỗi khi cập nhật mật khẩu: " . $conn->error;
        }
    }
}

$otp = isset($_POST['otp']) ? $_POST['otp'] : '';
$now = time();
$expiration_time = $now + 60; // Thêm 60 giây (1 phút) vào thời gian hiện tại

$_SESSION['otp'] = $otp;
$_SESSION['otp_exp'] = $expiration_time;
$serverOTP = isset($_SESSION['otp']) ? $_SESSION['otp'] : '';
$expiration_time = isset($_SESSION['otp_exp']) ? $_SESSION['otp_exp'] : null;

if ($otp !== $serverOTP) {
    echo "<div class='alert'>
            <span class='closebtn'>&times;</span>  
            Mã OTP không hợp lệ.
          </div>";
    exit();
}

if ($now > $expiration_time) {
    echo "<div class='alert'>
            <span class='closebtn'>&times;</span>  
            Mã OTP đã hết hạn.
          </div>";
    exit();
}
?>

<div id="changePassword" class="page" style="display: none;">
    <h3 style="text-align: center;">Đổi Mật Khẩu</h3>
    <form id="changePasswordForm" action="" method="post"> 
        <div class="form-group">
            <label for="oldPassword">Mật Khẩu Cũ:</label>
            <input type="password" id="oldPassword" name="oldPassword">
        </div>
        <div class="form-group">
            <label for="newPassword">Mật Khẩu Mới:</label>
            <input type="password" id="newPassword" name="newPassword">
        </div>
        <div class="form-group">
            <label for="confirmNewPassword">Xác Nhận Mật Khẩu Mới:</label>
            <input type="password" id="confirmNewPassword" name="confirmNewPassword">
        </div>
        <div class="form-group">
            <input type="submit" name="changePassword" value="Đổi Mật Khẩu">
        </div>
    </form>

    <?php
    // Hiển thị hộp thông báo và button "Đăng Nhập Lại" nếu mật khẩu đã được đổi thành công
    if ($passwordChanged) {
        echo '<div class="success-message">
                <p>Hoàn Tất Đổi Mật Khẩu. Hãy <a href="Signin.php">Đăng Nhập Lại</a> để Sử Dụng Dịch Vụ.</p>
            </div>';
    }
    ?>
</div>



    
</main>

<script>
    var isUpdateInfoVisible = false;

    function toggleUpdateInfo() {
        var updateInfoDiv = document.getElementById('updateInfo');
        isUpdateInfoVisible = !isUpdateInfoVisible;
        if (isUpdateInfoVisible) {
            updateInfoDiv.style.display = 'block';
        } else {
            updateInfoDiv.style.display = 'none';
        }
    }

    var isChangePasswordVisible = false;

    function showChangePassword() {
        var changePasswordDiv = document.getElementById('changePassword');
        isChangePasswordVisible = !isChangePasswordVisible;
        if (isChangePasswordVisible) {
            changePasswordDiv.style.display = 'block';
        } else {
            changePasswordDiv.style.display = 'none';
        }
    }

    //GỬI OTP
function sendOTP() {
    var email = document.getElementsByName("email")[0].value;
    var name = document.getElementsByName("name")[0].value;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "send_otp.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.hasOwnProperty("otp")) {
                document.getElementById("otpInput").value = response.otp;
            } else {
                console.error(response.message);
            }
        }
    };
    xhr.send("email=" + email + "&name=" + name); 
}

</script>
