<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng Nhập</title>
<link rel="icon" type="image/png" href="../IMAGES/Logo/logo 1.png">
<link rel="stylesheet" href="../CSS/sign.css">
</head>
<body>

<div class="container">
<a href="index.php"><img src="../IMAGES/Logo/logo 1.png" class="center" width="100" height="auto"></a>
    <h2>Chào mừng bạn quay trở lại<br><a href="index.php"><span style="color:#0099CC;"><b>MTPE BANK</b></span></a></h2>
    <P>Phát triển bởi <B>PHẠM MINH THẢO</B></P>
<?php
ob_start();
session_start();
include 'connect.php';
    
    function CreateMD5($input) {
        return md5($input);
    }
    
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
     
    $max_attempts = 5;
    $lockout_time = 300;
    
    if ($_SESSION['login_attempts'] >= 5) {
        echo "<div class='alert'>
                <span class='closebtn'>&times;</span>  
                Tài khoản của bạn đã bị tạm khóa do nhập sai mật khẩu quá nhiều lần.
              </div>";
        exit();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $loginName = $_POST['loginName'];
        $password = $_POST['password'];
     
        $sql = "SELECT password FROM customerLogin WHERE loginName='$loginName'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashed_password = CreateMD5($password); 
    
            if ($hashed_password == $row['password']) { 
                $_SESSION['login_attempt'] = 0;
                $_SESSION['login_attempt_time'] = 0;

                $_SESSION['loggedInLoginName'] = $loginName;
                header("Location: indexCustomer.php"); 
                exit;
            } else { 
                $_SESSION['login_attempt']++;
                $_SESSION['login_attempt_time'] = time();
                echo "<div class='alert'>
                        <span class='closebtn'>&times;</span>  
                        Sai tên đăng nhập hoặc mật khẩu.
                      </div>";
                echo "<a href='forgot_password.php'>Quên mật khẩu?</a>";
            }
        } else {
            echo "<div class='alert'>
                    <span class='closebtn'>&times;</span>  
                    Sai tên đăng nhập hoặc mật khẩu.
                  </div>";
        }
    
        mysqli_close($conn);
    }
    ?>

    <form id="signinForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="input-group">
            <input type="text" name="loginName" placeholder="Tên đăng nhập" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Mật khẩu" required>
        </div>
        <button type="submit"><b>ĐĂNG NHẬP</b></button>

        <div class="signup-link">
            <br>Tôi Chưa Có Tài Khoản<a href="signup.php"> <span style="color:#0099CC;"><b>ĐĂNG KÝ NGAY</b></span></a> !
        </div>

        <div class="forgot_password" style="text-align: center;"> 
            <br><a href="forgot_password.php"> <span style="color:#0099CC;">Tôi không còn nhớ mật khẩu của mình nữa !! </span></a>
        </div>
    </form>
</div>


<script src="../JS/checksign.js"> </script>
</body>
</html>
