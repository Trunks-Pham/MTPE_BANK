<style>
    .NewsBackground{
        background-color: black;
        width: auto;
        height: 550px;
    }
    .NewsLiveText{ 
        margin-left: 250px;
        margin-right: 250px;
    }

    @media only screen and (max-width: 600px) {
        .NewsLiveText p {
            font-size: 20px;
            line-height: 30px;
        }
    }

    @media only screen and (min-width: 601px) {
        .NewsLiveText p {
            font-size: 32px;
            line-height: 50px;
        }
        
    }

    .NewsLiveText p {
        color: aliceblue;
        margin: 0px;
        padding: 0px;
        text-align: center;
    }

    .NewsLiveText p span {
        color: #FF9933;
    }
  
    /* #MTPEbanner {
  content: url('../IMAGES/Banner/MTPE1.png');
}

    #BannerNews{
        content: url('../IMAGES/Banner/bannerNews.jpg');
    }
@media (max-width: 600px) {
  #MTPEbanner {
      content: url('../IMAGES/Banner/MTPE2.png');
  }    #BannerNews{
        content: url('../IMAGES/Banner/bannerNewsz.jpg');
    }
} */
 
.news-post .title button {
  background-color: #F4A460;
  color: white; 
  padding: 10px 20px;
  text-align: center; 
  text-decoration: none;
  display: inline-block; 
  font-size: 16px; 
  margin: 4px 2px;
  cursor: pointer;
  border: none; 
  transition: background-color 0.3s; 
  border-radius: 12px; 
}

.news-post .title button:hover {
  background-color: #DAA520; 
}

</style>
 
<img src="../IMAGES/Cards/BACK.jpg" id="BannerCards" alt="BannerCards" width="100%" >
<main class="chairman-container-news">
    <h1 style="text-align:center;" ><b><span style="color:#0099CC;"><B>Dịch Vụ Thẻ MTPE BANK</B></span></b></h1>

    <main class="news">

        <div class="news-post">
            <img src="../IMAGES/Cards/CARD.jpg" alt="">
            <div class="title">Thẻ MTPE NAPAS <BR>
                <button>Mở Thẻ Ngay</button></div></a>
        </div>
        <div class="news-post">
            <img src="../IMAGES/Cards/CARD1.jpg" alt="">
            <div class="title">Thẻ MTPE MASTERCARD <BR>
                <button>Mở Thẻ Ngay</button></div></a>
        </div>

        <div class="news-post">
            <img src="../IMAGES/Cards/AME.jpg" alt="">
            <div class="title">Thẻ MTPE AMERICAN EXPRESS<BR>
                <button>Mở Thẻ Ngay</button></div></a>
        </div>

        <div class="news-post">
            <img src="../IMAGES/Cards/JCB.jpg" alt="">
            <div class="title">Thẻ MTPE JCB <BR>
                <button>Mở Thẻ Ngay</button></div></a>
        </div>
        <div class="news-post">
            <img src="../IMAGES/Cards/UNIO.jpg" alt="">
            <div class="title">Thẻ MTPE UNIONPAY <BR>
                <button>Mở Thẻ Ngay</button></div></a>
        </div>

        <div class="news-post">
            <img src="../IMAGES/Cards/VISA.jpg" alt="">
            <div class="title">Thẻ MTPE VISA SIGNATURE<BR>
                <button>Mở Thẻ Ngay</button></div></a>
        </div>
    </main>
</main>
<?php include 'Chatbot.php'; ?>
 