document.getElementById("otpButton").addEventListener("click", function() {
    var email = document.getElementsByName("email")[0].value;
    var xhr = new XMLHttpRequest();

    xhr.open("POST", "send_otp.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    var appPassword = "NHỚ ĐỂ MẬT KHẨU ỨNG DỤNG VÀO ĐÂYYYYYYY";

    var data = JSON.stringify({email: email, appPassword: appPassword});
    xhr.send(data);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                alert("Mã OTP đã được gửi qua email.");
            } else {
                alert("Có lỗi xảy ra khi gửi mã OTP qua email.");
            }
        }
    };
});

//Đóng thông báo
var close = document.getElementsByClassName("closebtn");
var i;

for (i = 0; i < close.length; i++) {
  close[i].onclick = function(){
    var div = this.parentElement;
    div.style.opacity = "0";
    setTimeout(function(){ div.style.display = "none"; }, 600);
  }
}
