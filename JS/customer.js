//HEADER
// ---------Responsive-navbar-active-animation-----------
function test() {
    var tabsNewAnim = $("#navbarSupportedContent");
    var selectorNewAnim = $("#navbarSupportedContent").find("li").length;
    var activeItemNewAnim = tabsNewAnim.find(".active");
    var activeWidthNewAnimHeight = activeItemNewAnim.innerHeight();
    var activeWidthNewAnimWidth = activeItemNewAnim.innerWidth();
    var itemPosNewAnimTop = activeItemNewAnim.position();
    var itemPosNewAnimLeft = activeItemNewAnim.position();
    $(".hori-selector").css({
        top: itemPosNewAnimTop.top + "px",
        left: itemPosNewAnimLeft.left + "px",
        height: activeWidthNewAnimHeight + "px",
        width: activeWidthNewAnimWidth + "px"
});
$("#navbarSupportedContent").on("click", "li", function (e) {
    $("#navbarSupportedContent ul li").removeClass("active");
    $(this).addClass("active");
    var activeWidthNewAnimHeight = $(this).innerHeight();
    var activeWidthNewAnimWidth = $(this).innerWidth();
    var itemPosNewAnimTop = $(this).position();
    var itemPosNewAnimLeft = $(this).position();
    $(".hori-selector").css({
    top: itemPosNewAnimTop.top + "px",
    left: itemPosNewAnimLeft.left + "px",
    height: activeWidthNewAnimHeight + "px",
    width: activeWidthNewAnimWidth + "px"
    });
});
}
$(document).ready(function () {
setTimeout(function () {
    test();
});
});

$(window).on("resize", function () {
setTimeout(function () {
    test();
}, 500);
});
$(".navbar-toggler").click(function () {
$(".navbar-collapse").slideToggle(300);
setTimeout(function () {
    test();
});
});

// --------------add active class-on another-page move----------
jQuery(document).ready(function ($) {
// Get current path and find target link
var path = window.location.pathname.split("/").pop();

// Account for home page with empty path
if (path == "") {
    path = "index.html";
}

var target = $('#navbarSupportedContent ul li a[href="' + path + '"]');
// Add active class to target link
target.parent().addClass("active");
});

// Add active class on another page linked
$(window).on('load',function () {
    var current = location.pathname;
    console.log(current);
    $('#navbarSupportedContent ul li a').each(function(){
        var $this = $(this);
        // if the current path is like this link, make it active
        if($this.attr('href').indexOf(current) !== -1){
            $this.parent().addClass('active');
            $this.parents('.menu-submenu').addClass('show-dropdown');
            $this.parents('.menu-submenu').parent().addClass('active');
        }else{
            $this.parent().removeClass('active');
        }
    })
});

//CHỨC NĂNG

function information() {
$('#main').load('Information.php');
}

function transaction() {
$('#main').load('Transaction.php');
}

$(document).ready(function(){
    $('#main').load('Transaction.php');
});

function expJar() {
    $('#main').load('Jar.php');
}
function savingbooks(){
    $('#main').load('Savingbooks.php')
}

function contact(){
    $('#main').load('cardsbank.php')
}

function history(){
    $('#main').load('history.php')
}


//HIỂN THỊ THÔNG TIN
$(document).ready(function() {
    loadInformation();
});

function loadInformation() {
    $.ajax({
        url: 'Transaction.php',
        type: 'GET',
        success: function(response) {
            $('#main').html(response);
        },
        error: function(xhr, status, error) {
            console.log(error);
        }
    });
}

//CHUYỂN TIỀN
function showConfirmation() {
    var bank = document.getElementById("bank").value;
    var accountNumber = document.getElementById("accountNumber").value;
    var name = document.getElementById("name").value;
    var amount = document.getElementById("amount").value;
    var content = document.getElementById("content").value;

    document.getElementById("bankConfirmation").textContent = "Ngân Hàng: " + bank;
    document.getElementById("accountNumberConfirmation").textContent = "Số Tài Khoản: " + accountNumber;
    document.getElementById("nameConfirmation").textContent = "Tên: " + name;
    document.getElementById("amountConfirmation").textContent = "Số Tiền: " + amount;
    document.getElementById("contentConfirmation").textContent = "Nội Dung Giao Dịch: " + content;

    document.getElementById("confirmation").style.display = "block";
}

document.getElementById("transferForm").addEventListener("submit", function(event){
    event.preventDefault();
    showConfirmation();
});

function isNumberKey(evt) {
    return evt.charCode >= 48 && evt.charCode <= 57;
}

function formatCurrency(input) {
    const value = input.value.replace(/[^0-9]/g, '');
    const formattedValue = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    input.value = formattedValue;
}

function showConfirmation() {
    document.getElementById("confirmation").style.display = "block";
}

//HỦ CHI TIÊU
