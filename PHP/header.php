<header class="header">
    <div id="dp_menu" class="dp_menu">
            <button id="menu_button"></button>
            <ul class="main-menu" style="display:none">

                <div class="input-container">
                    <form action="search.php">
                    <input type="text" class="input-field" placeholder="Nhập từ khóa">
                    <button type="submit" class="input-button"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <br><br>
                <b>&nbsp;&nbsp;&nbsp;Về Ngân Hàng</b>
                <li><a href="#_Tầm_Nhìn_Sứ_Mệnh" onclick="myNews()">Tầm Nhìn Sứ Mệnh</a></li>

                <li><a href="#_Hành_Trình_Phát_Triển_MTPE">Hành Trình Phát Triển MTPE</a></li>

                <br><b>&nbsp;&nbsp;&nbsp;Tin Tức</b>
                <li><a href="#_Kiến_Thức_Tài_Chính" onclick="myFinance()">Kiến Thức Tài Chính</a></li> 

                <br>&nbsp;&nbsp;&nbsp;<b>Thẻ</b>
                <li><a href="#_Thẻ_MTPE_BANK" onclick="myCard()">Thẻ MTPE BANK</a></li> 

                <br>&nbsp;&nbsp;&nbsp;<b>Tài Khoản</b>
                <li><a href="#_Hủ_Chi_Tiêu" onclick="myMoneyPot()">Hủ Chi Tiêu</a></li>

                <li><a href="#_Tiết_Kiệm_Trực_Tuyến">Tiết Kiệm Trực Tuyến</a></li>

                <br><b>&nbsp;&nbsp;&nbsp;Sản Phẩm Tài Chính</b>
                <li><a href="#_Bảo_Hiểm_Toàn_Diện_MTPE_LIFE ">Bảo Hiểm Toàn Diện MTPE LIFE </a></li> 

            </ul>
    </div>

    <div class="logo">
        <a href="index.php" id="transactionLink" onclick="myHome()">
            <img src="../IMAGES/Logo/logo 1.png" alt="MTPE BANK" >
        </a>
    </div>

    <div id="signin-container">
        <button id="signup-button"><b>Đăng Ký</b></button>
        <button id="signin-button">Đăng Nhập</button>
    </div>
</header>