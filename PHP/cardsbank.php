 
  <style>
    .container {
        margin-top: 50px; 
        display: flex; 
        justify-content: space-between; 
        overflow: hidden; 
    }
    
    .card_img{
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 200px;
    }

    .card_text {
      width: 40%; 
    }

    @media (max-width: 1000px) {
      .container {
        flex-direction: column;
        margin-left: 5px;
        margin-right: 5px;
      } 
 
    }



.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
  margin: 20px;
}
 
.switch input {display:none;}
 
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}
 
.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
 
input:checked + .slider {
  background-color: #2196F3;
}
 
input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}
 
input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}
 
/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}
 
.slider.round:before {
  border-radius: 50%;
}
input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

.disabled {
        opacity: 0.6; 
    }

</style>



<?php 
include 'connect.php';
session_start(); 

// Khởi tạo biến kiểm tra có dữ liệu trong bảng Cards hay không
$hasData = false;

if (isset($_SESSION['loggedInLoginName'])) {
    $loggedInLoginName = $_SESSION['loggedInLoginName'];
    // Lấy thông tin từ bảng Cards dựa trên accountNumber của người dùng
    $sql = "SELECT * FROM Cards WHERE accountNumber IN (SELECT accountNumber FROM customerAccount WHERE id IN (SELECT id FROM customer WHERE loginName = '$loggedInLoginName'))";

    $result = $conn->query($sql);

    if ($result !== false && $result->num_rows > 0) {
        $hasData = true; // Đặt biến kiểm tra thành true nếu có dữ liệu trong bảng Cards
        while ($row = $result->fetch_assoc()) {
            // Hiển thị thông tin từ bảng Cards
            echo '
            <div class="container">
                <img class="card_img" src="../IMAGES/Cards/debit.jpg" alt="card_img" width="200px" height="auto">
                <div class="card_text" id="cardInfo">
                    <p>Tài Khoản Nguồn: <b>' . $row["accountNumber"] . '</b></p>
                    <p>Số Thẻ: <b>' . $row["cardNumber"] . '</b></p>
                    <p>Loại Thẻ: <b>' . $row["cardType"] . '</b></p>
                    <p>Trạng Thái Thẻ: <b>' . $row["cardStatus"] . '</b></p>
                    <p>Mã CVC: <b>' . $row["pinCode"] . '</b></p> 

                    <label class="switch">
                    <input type="checkbox" id="statusSwitch" checked>
                    <span class="slider round"></span>
                    </label>
                </div>
            </div>';
        }
    }
}

//Đổi trạng thái
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Lấy thông tin từ request
  $cardNumber = $_POST['cardNumber'];
  $newStatus = $_POST['newStatus'];

  // Cập nhật trạng thái thẻ trong cơ sở dữ liệu
  $sql = "UPDATE Cards SET cardStatus = '$newStatus' WHERE cardNumber = '$cardNumber'";
  if ($conn->query($sql) === TRUE) {
      echo "Cập nhật trạng thái thẻ thành công";
  } else {
      echo "Lỗi: " . $sql . "<br>" . $conn->error;
  }
}
?>

<!-- Hiển thị mặc định nếu không có dữ liệu trong bảng Cards -->
<?php if (!$hasData): ?>
<div class="container">
    <img class="card_img" src="../IMAGES/Cards/debit.jpg" alt="card_img" width="200px" height="auto">
    <div class="card_text" id="cardInfo">
        <p>Nguồn Tiền Cho Thẻ: <b>Khách hàng chưa mở thẻ</b></p>
        <p>Số Thẻ: <b>Khách hàng chưa mở thẻ</b></p>
        <p>Chủ Thẻ: <b>Khách hàng chưa mở thẻ</b></p>
        <p>Loại Thẻ: <b>Khách hàng chưa mở thẻ</b></p>
        <p>Mã CVC: <b>Khách hàng chưa mở thẻ</b></p>
        <p>Trạng Thái Thẻ: <span id="status"><b>Khách hàng chưa mở thẻ</b></p>

        <label class="switch">
        <input type="checkbox" id="statusSwitch" checked>
        <span class="slider round"></span>
    </label>
    </div>
</div>
<?php endif; ?>

<!-- Button để tạo thẻ -->
<!-- <button onclick="createCard()">Tạo Thẻ</button> -->


<script>

document.getElementById('statusSwitch').addEventListener('change', function() {
    var cardInfo = document.getElementById('cardInfo');
    var status = document.getElementById('status');
    var cardNumber = '<?php echo $row["cardNumber"]; ?>'; // Lấy số thẻ từ PHP
    var newStatus = this.checked ? 'Đang Hoạt Động' : 'Tạm Khóa'; // Xác định trạng thái mới

    // Gửi yêu cầu AJAX để cập nhật trạng thái thẻ
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_card_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText); // Hiển thị kết quả từ máy chủ
            location.reload(); // Làm mới trang sau khi thêm thẻ thành công
        }
    };
    xhr.send("cardNumber=" + cardNumber + "&newStatus=" + newStatus);
});



    // document.getElementById('statusSwitch').addEventListener('change', function() {
    //     var cardInfo = document.getElementById('cardInfo');
    //     var status = document.getElementById('status');
    //     if(this.checked) {
    //       status.innerHTML = '<strong>Mơ là Đang Hoạt Động HẺẺẺẺẺ =))))</strong>';
    //       cardInfo.classList.remove('disabled');

    //     } else {
    //         status.innerHTML = '<strong>Chưa Mở Thẻ Mà Cũng Cố Tình Quá Nhỉ =)))</strong>';
    //         cardInfo.classList.add('disabled');
    //     }
    // });

    function createCard() {
        // Gửi yêu cầu AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "create_card.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText); // Hiển thị kết quả từ máy chủ
                location.reload(); // Làm mới trang sau khi thêm thẻ thành công
            }
        };
        xhr.send();
    }
</script>