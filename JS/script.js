 // Menu
$(document).ready(function(){
	$("#menu_button").click(function(){
		$("#dp_menu > ul").toggle(500);
		$("#dp_menu").toggleClass("show");
	});
});
// Lấy tất cả các mục li trong menu chính
var menuItems = document.querySelectorAll('.main-menu li');

// Thêm sự kiện click vào từng mục li
for (var i = 0; i < menuItems.length; i++) {
    menuItems[i].addEventListener('click', function() {
        // Khi một mục li được nhấp vào, đặt thuộc tính display của menu chính thành 'none'
        document.querySelector('.main-menu').style.display = 'none';
    });
}
// Sign in
// $(document).ready(function(){
//     $("#signin-button").click(function(){
//         window.location.href = 'signin.php';
//     });
// });

document.getElementById('signin-button').addEventListener('click', function() {
    window.open('signin.php', '_blank');
});

document.getElementById('signup-button').addEventListener('click', function() {
    window.open('signup.php','_blank');
});

//LOAD TRANG
$(document).ready(function(){
    $('#bodymain').load('home.php');
});
function myHome() {
	$('#bodymain').load('home.php');
}

function myNews() {
	$('#bodymain').load('news.php');
}

function myFinance() {
	$('#bodymain').load('finance.php');
}

function myMoneyPot() {
    $('#bodymain').load('moneypot.php');
}

function myCard() {
    $('#bodymain').load('cards.php');
}