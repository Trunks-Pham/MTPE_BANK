<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginName = $_POST['loginName'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $cccd = $_POST['cccd'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $specificAddress = $_POST['address'];
    $ward = isset($_POST['ward']) ? $_POST['ward'] : '';
    $district = isset($_POST['district']) ? $_POST['district'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $email = $_POST['email'];

    // Tạo chuỗi địa chỉ đầy đủ
    $address = $specificAddress . ", " . $ward . ", " . $district . ", " . $city;

    $otp = isset($_POST['otp']) ? $_POST['otp'] : '';

    // Danh sách các trường bắt buộc
    $required_fields = ['loginName', 'password1', 'password2', 'cccd', 'name', 'dob', 'phone', 'address', 'ward', 'district', 'city', 'email', 'otp'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo "<div class='alert'>
                    <span class='closebtn'>&times;</span>  
                    Không được để trống dòng này: $field
                  </div>";
            exit();
        }
    }

    function calculateAge($dob) {
        $today = new DateTime(date('Y-m-d'));
        $birthdate = new DateTime($dob);
        $age = $today->diff($birthdate);
        return $age->y;
    }

    $age = calculateAge($dob);
    if ($age < 18) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Bạn chưa đủ tuổi để đăng ký.
              </div>";
        exit();
    }
    
    if ($password1 !== $password2) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Mật khẩu không khớp.
              </div>";
        exit();
    }
    
    ////////////////////////
    // Kiểm tra điều kiện tên đăng nhập
    if (strlen($loginName) < 6) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Tên đăng nhập phải có ít nhất 6 ký tự.
              </div>";
        exit();
    }
    
    // Kiểm tra điều kiện mật khẩu
    if (strlen($password1) < 6 || !preg_match('/[A-Za-z]/', $password1) || !preg_match('/[0-9]/', $password1) || !preg_match('/[\W]/', $password1)) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ cái, số và ký tự đặc biệt.
              </div>";
        exit();
    }
    
    // Kiểm tra số CCCD
    if (!preg_match('/^\d{12}$/', $cccd)) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Số CCCD phải gồm 12 chữ số.
              </div>";
        exit();
    }
    
    // Kiểm tra số điện thoại
    if (!preg_match('/^\d{10}$/', $phone)) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Số điện thoại phải gồm 10 chữ số.
              </div>";
        exit();
    }
    
//////////////////////
    // // Kiểm tra tên
    // if (!preg_match('/^[A-Z][a-zA-Z]*(\s[A-Z][a-zA-Z]*)*$/', $name)) {
    //     echo "Tên phải viết hoa chữ cái đầu tiên và không được chứa số hoặc ký tự đặc biệt.";
    //     exit();
    // }
//////////////////////    
    
    // Kiểm tra email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Địa chỉ email không hợp lệ.
              </div>";
        exit();
    }    
////////////////////////


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

    $hashed_password = md5($password1);

    $sql_login = "INSERT INTO customerLogin (loginName, password) VALUES ('$loginName', '$hashed_password')";
    $sql_customer = "INSERT INTO customer (id, name, dateOfBirth, gender, phoneNumber, address, email, loginName) VALUES ('$cccd', '$name', '$dob', '$gender', '$phone', '$address', '$email', '$loginName')";
    $sql_account = "INSERT INTO customerAccount (accountNumber, id) VALUES ('2301" . mt_rand(10000000, 99999999) . "', '$cccd')";

    if (mysqli_query($conn, $sql_login) && mysqli_query($conn, $sql_customer) && mysqli_query($conn, $sql_account)) {
        unset($_SESSION['otp']);
        unset($_SESSION['otp_exp']);

        echo "<div class='alert success'>
                <span class='closebtn'>&times;</span>  
                Đăng ký thành công! Mời quý khách đăng nhập
              </div>";
    } else {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Lỗi: " . mysqli_error($conn) . "
              </div>";
    }

    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng Ký</title>
<link rel="icon" type="image/png" href="../IMAGES/Logo/logo 1.png">
<link rel="stylesheet" href="../CSS/sign.css">
</head>
<body>
<div class="container">
  <a href="index.php"><img src="../IMAGES/Logo/logo 1.png" class="center" width="100" height="auto"></a>
    <h2>Chào mừng bạn đến với<br><a href="index.php"><span style="color:#0099CC;"><b>MTPE BANK</b></span></a></h2>
    <P>Phát triển bởi <B>PHẠM MINH THẢO</B></P>

<!-- FORM TÀI KHOẢN     -->
<form id="signupForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="loginName" placeholder="Tên đăng nhập" required>
    <input type="password" name="password1" placeholder="Mật khẩu lần 1" required>
    <input type="password" name="password2" placeholder="Mật khẩu lần 2" required>
    <!-- <button type="button" id="next" onclick="showNextForm()">Tiếp tục</button> -->
    <button type="button" id="next">Tiếp tục</button> <!-- Đổi type thành button -->
</form>

<!-- FORM THÔNG TIN -->
<form id="personalInfoForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display: none;">
    <input type="hidden" name="loginName" id="hiddenLoginName">
    <input type="hidden" name="password1" id="hiddenPassword1">
    <input type="hidden" name="password2" id="hiddenPassword2">

    <input type="number" name="cccd" placeholder="Số CCCD (12 Số)" required>
    <input type="text" name="name" placeholder="Họ và tên (in hoa)" required>
    <input type="date" name="dob" placeholder="Ngày sinh" required>
    <select name="gender" required style=" margin-bottom:20px; width: 100%; height: 40px;">
        <option value="" disabled selected>Giới tính</option>
        <option value="Nam">Nam</option>
        <option value="Nữ">Nữ</option>
        <option value="Khác">Khác</option>
    </select>
    <input type="number" name="phone" placeholder="Số điện thoại" required>
    <div class="where">
        <label for="" style="font-size: 16px; color: #595959; display: block; margin-left: 5px; margin-bottom:2px">Địa chỉ của bạn:</label>
        <select name="city" id="city" required style=" width: 100%; padding: 10px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
            <option value="" selected>Chọn tỉnh thành</option>
        </select>
        <select name="district" id="district" required style=" width: 100%; padding: 10px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
            <option value="" selected>Chọn quận huyện</option>
        </select>
        <select name="ward" id="ward" required style=" width: 100%; padding: 10px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
            <option value="" selected>Chọn phường xã</option>
        </select>
        <input type="text" name="address" placeholder="Số nhà - Đường/Thôn/Xóm - Ấp" required>
    </div>
    <input type="email" name="email" placeholder="Email" required>
    <button type="button" id="otpButton" onclick="sendOTP()">Nhận OTP</button>
    <input type="text" name="otp" placeholder="Mã OTP" required>
    <input type="checkbox" id="terms" required>
    <label for="terms"> Tôi đồng ý với <a href="../TEXT/MTPEBANK-Digital-Bank .pdf" target="_blank"> <span style="color:#FF8C00;"><B>Điều Khoản Dịch Vụ</B></span></a> của <a href="index.php"><span style="color:#0099CC;"><b>MTPE BANK</b></span></a></label>
    <button type="submit"><b>XÁC NHẬN ĐĂNG KÝ</b></button>

    <!-- <button type="button" id="submit">XÁC NHẬN ĐĂNG KÝ</button> -->
     <!-- Đổi type thành button -->

    <button type="button" id="back" onclick="showPrevForm()">Quay lại</button>
</form>

        <!-- ĐĂNG NHẬP -->
        <div class="signin-link" >
            <br>Tôi Đã Có Tài Khoản <a href="signin.php" > <span style="color:#0099CC;"><b>ĐĂNG NHẬP NGAY</b></span></a> !
        </div>

  </div>
<script src="../JS/checksign.js"></script>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
/////////////////////////////////////////////
//Lưu dữ liệu form để khi qua lại không bị mất
let formData = {
    signupForm: {},
    personalInfoForm: {}
};

function saveSignupFormData() {
    formData.signupForm.loginName = document.querySelector('#signupForm input[name="loginName"]').value;
    formData.signupForm.password1 = document.querySelector('#signupForm input[name="password1"]').value;
    formData.signupForm.password2 = document.querySelector('#signupForm input[name="password2"]').value;
}

function savePersonalInfoFormData() {
    formData.personalInfoForm.cccd = document.querySelector('#personalInfoForm input[name="cccd"]').value;
    formData.personalInfoForm.name = document.querySelector('#personalInfoForm input[name="name"]').value;
    formData.personalInfoForm.dob = document.querySelector('#personalInfoForm input[name="dob"]').value;
    formData.personalInfoForm.gender = document.querySelector('#personalInfoForm select[name="gender"]').value;
    formData.personalInfoForm.phone = document.querySelector('#personalInfoForm input[name="phone"]').value;
    formData.personalInfoForm.city = document.querySelector('#personalInfoForm select[name="city"]').value;
    formData.personalInfoForm.district = document.querySelector('#personalInfoForm select[name="district"]').value;
    formData.personalInfoForm.ward = document.querySelector('#personalInfoForm select[name="ward"]').value;
    formData.personalInfoForm.address = document.querySelector('#personalInfoForm input[name="address"]').value;
    formData.personalInfoForm.email = document.querySelector('#personalInfoForm input[name="email"]').value;
    formData.personalInfoForm.otp = document.querySelector('#personalInfoForm input[name="otp"]').value;
}

function fillSignupFormData() {
    document.querySelector('#signupForm input[name="loginName"]').value = formData.signupForm.loginName || '';
    document.querySelector('#signupForm input[name="password1"]').value = formData.signupForm.password1 || '';
    document.querySelector('#signupForm input[name="password2"]').value = formData.signupForm.password2 || '';
}

function fillPersonalInfoFormData() {
    document.querySelector('#personalInfoForm input[name="cccd"]').value = formData.personalInfoForm.cccd || '';
    document.querySelector('#personalInfoForm input[name="name"]').value = formData.personalInfoForm.name || '';
    document.querySelector('#personalInfoForm input[name="dob"]').value = formData.personalInfoForm.dob || '';
    document.querySelector('#personalInfoForm select[name="gender"]').value = formData.personalInfoForm.gender || '';
    document.querySelector('#personalInfoForm input[name="phone"]').value = formData.personalInfoForm.phone || '';
    document.querySelector('#personalInfoForm select[name="city"]').value = formData.personalInfoForm.city || '';
    document.querySelector('#personalInfoForm select[name="district"]').value = formData.personalInfoForm.district || '';
    document.querySelector('#personalInfoForm select[name="ward"]').value = formData.personalInfoForm.ward || '';
    document.querySelector('#personalInfoForm input[name="address"]').value = formData.personalInfoForm.address || '';
    document.querySelector('#personalInfoForm input[name="email"]').value = formData.personalInfoForm.email || '';
    document.querySelector('#personalInfoForm input[name="otp"]').value = formData.personalInfoForm.otp || '';
}

function fillHiddenLoginNameField() {
    const loginName = document.getElementById('signupForm').querySelector('input[name="loginName"]').value;
    document.getElementById('hiddenLoginName').value = loginName;
}

function fillHiddenPassword1Field() {
    const password1 = document.getElementById('signupForm').querySelector('input[name="password1"]').value;
    document.getElementById('hiddenPassword1').value = password1;
}

function fillHiddenPassword2Field() {
    const password2 = document.getElementById('signupForm').querySelector('input[name="password2"]').value;
    document.getElementById('hiddenPassword2').value = password2;
}


function showNextForm() {
    saveSignupFormData(); // Lưu dữ liệu của form signupForm
    fillSignupHiddenFields(); // Điền dữ liệu vào các hidden fields

    document.getElementById('signupForm').style.display = 'none';
    document.getElementById('personalInfoForm').style.display = 'block';

    fillPersonalInfoFormData(); // Điền lại dữ liệu vào form personalInfoForm nếu có
}

function showPrevForm() {
    savePersonalInfoFormData(); // Lưu dữ liệu của form personalInfoForm

    document.getElementById('personalInfoForm').style.display = 'none';
    document.getElementById('signupForm').style.display = 'block';

    fillSignupFormData(); // Điền lại dữ liệu vào form signupForm nếu có
}

function fillSignupHiddenFields() {
    document.getElementById('hiddenLoginName').value = formData.signupForm.loginName;
    document.getElementById('hiddenPassword1').value = formData.signupForm.password1;
    document.getElementById('hiddenPassword2').value = formData.signupForm.password2;
}

/////////////////////////////////////////////


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

//LẤY DỮ LIỆU ĐỊA LÝ
var citis = document.getElementById("city");
var districts = document.getElementById("district");
var wards = document.getElementById("ward");
var Parameter = {
  url: "https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json", 
  method: "GET", 
  responseType: "application/json", 
};
var promise = axios(Parameter);
promise.then(function (result) {
  renderCity(result.data);
});

function renderCity(data) {
  for (const x of data) {
	var opt = document.createElement('option');
	 opt.value = x.Name;
	 opt.text = x.Name;
	 opt.setAttribute('data-id', x.Id);
	 citis.options.add(opt);
  }
  citis.onchange = function () {
    district.length = 1;
    ward.length = 1;
    if(this.options[this.selectedIndex].dataset.id != ""){
      const result = data.filter(n => n.Id === this.options[this.selectedIndex].dataset.id);

      for (const k of result[0].Districts) {
		var opt = document.createElement('option');
		 opt.value = k.Name;
		 opt.text = k.Name;
		 opt.setAttribute('data-id', k.Id);
		 district.options.add(opt);
      }
    }
  };
  district.onchange = function () {
    ward.length = 1;
    const dataCity = data.filter((n) => n.Id === citis.options[citis.selectedIndex].dataset.id);
    if (this.options[this.selectedIndex].dataset.id != "") {
      const dataWards = dataCity[0].Districts.filter(n => n.Id === this.options[this.selectedIndex].dataset.id)[0].Wards;

      for (const w of dataWards) {
		var opt = document.createElement('option');
		 opt.value = w.Name;
		 opt.text = w.Name;
		 opt.setAttribute('data-id', w.Id);
		 wards.options.add(opt);
      }
    }
  };
}

document.getElementById('next').addEventListener('click', function() {
    // Kiểm tra và hiển thị thông báo lỗi nếu có
    if (!validateSignupForm()) {
        return; // Nếu có lỗi, không chuyển form
    }

    fillHiddenLoginNameField(); // Điền dữ liệu vào trường ẩn "hiddenLoginName"
    fillHiddenPassword1Field(); // Điền dữ liệu vào trường ẩn "hiddenPassword1"
    fillHiddenPassword2Field(); // Điền dữ liệu vào trường ẩn "hiddenPassword2"

    // Nếu không có lỗi, chuyển sang form thông tin cá nhân
    document.getElementById('signupForm').style.display = 'none';
    document.getElementById('personalInfoForm').style.display = 'block';

    fillPersonalInfoFormData(); // Điền lại dữ liệu vào form personalInfoForm nếu có
});




document.getElementById('submit').addEventListener('click', function() {
    // Kiểm tra và hiển thị thông báo lỗi nếu có
    if (!validatePersonalInfoForm()) {
        return; // Nếu có lỗi, không gửi form
    }

    // Nếu không có lỗi, gửi form
    document.getElementById('personalInfoForm').submit();
});

function validateSignupForm() {
    let errorMessage = ""; // Chuỗi lưu trữ thông báo lỗi

    // Kiểm tra tên đăng nhập
    if (!validateLoginName()) {
        errorMessage += "Tên đăng nhập phải có ít nhất 6 ký tự CHỮ và SỐ.\n";
    }

    // Kiểm tra mật khẩu
    if (!validatePassword()) {
        errorMessage += "Mật khẩu phải có ít nhất 6 ký tự: <br>CHỮ HOA, CHỮ THƯỜNG, SỐ, KÝ TỰ ĐẶC BIỆT và mật khẩu phải khớp.\n";
    }

    // Nếu có lỗi, hiển thị thông báo và trả về false
    if (errorMessage !== "") {
        alert(errorMessage);
        return false;
    }

    // Nếu không có lỗi, trả về true
    return true;
} 

function validatePersonalInfoForm() {
    let errorMessage = ""; // Chuỗi lưu trữ thông báo lỗi

    // Kiểm tra và thêm thông báo lỗi nếu có
    if (!validateCCCD()) {
        errorMessage += "Số CCCD không hợp lệ.\n";
    }
    if (!validateName()) {
        errorMessage += "Tên không hợp lệ.\n";
    }
    if (!validateDOB()) {
        errorMessage += "Vui lòng chọn ngày sinh.\n";
    }
    if (!validateGender()) {
        errorMessage += "Vui lòng chọn giới tính.\n";
    }
    if (!validatePhone()) {
        errorMessage += "Số điện thoại không hợp lệ.\n";
    }
    if (!validateAddress()) {
        errorMessage += "Vui lòng nhập địa chỉ.\n";
    }
    if (!validateEmail()) {
        errorMessage += "Địa chỉ email không hợp lệ.\n";
    }
    if (!validateOTP()) {
        errorMessage += "Vui lòng nhập mã OTP.\n";
    }

    // Nếu có lỗi, hiển thị thông báo và trả về false
    if (errorMessage !== "") {
        alert(errorMessage);
        return false; // Trả về false nếu có lỗi
    }

    // Nếu không có lỗi, trả về true
    return true; // Trả về true nếu không có lỗi
}


function validateLoginName() {
    const loginName = document.getElementById('signupForm').querySelector('input[name="loginName"]').value;
    return loginName.length >= 6;
}

function validatePassword() {
    const password1 = document.getElementById('signupForm').querySelector('input[name="password1"]').value;
    const password2 = document.getElementById('signupForm').querySelector('input[name="password2"]').value;
    return password1.length >= 6 && password1 === password2;
}

function validateCCCD() {
    const cccd = document.getElementById('personalInfoForm').querySelector('input[name="cccd"]').value;
    return /^\d{12}$/.test(cccd);
}

function validateName() {
    const name = document.getElementById('personalInfoForm').querySelector('input[name="name"]').value;
    return /^[A-Z][a-zA-Z]*(\s[A-Z][a-zA-Z]*)*$/.test(name);
}

function validateDOB() {
    const dob = document.getElementById('personalInfoForm').querySelector('input[name="dob"]').value;
    return !!dob; // Kiểm tra xem ngày sinh có được chọn không
}

function validateGender() {
    const gender = document.getElementById('personalInfoForm').querySelector('select[name="gender"]').value;
    return !!gender; // Kiểm tra xem giới tính có được chọn không
}

function validatePhone() {
    const phone = document.getElementById('personalInfoForm').querySelector('input[name="phone"]').value;
    return /^\d{10}$/.test(phone);
}

function validateAddress() {
    const address = document.getElementById('personalInfoForm').querySelector('input[name="address"]').value;
    return !!address; // Kiểm tra xem địa chỉ có được điền không
}

function validateEmail() {
    const email = document.getElementById('personalInfoForm').querySelector('input[name="email"]').value;
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validateOTP() {
    const otp = document.getElementById('personalInfoForm').querySelector('input[name="otp"]').value;
    return !!otp; // Kiểm tra xem mã OTP có được điền không
}

///////////////////////////
</script>

</html>