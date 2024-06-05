<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <div class="index_all">
        <div class="index_text">
            <h1>Run your restaurant <br>
right from your fingertips
  </h1>
            <p>
            Streamline your dining experience – manage orders, track inventory, and enhance customer satisfaction with our intuitive restaurant POS system.
            </p>
            <a href="login.php">
                <div class="index_btn">
                    <button>
                        Login as a Cashier
                    </button>
                </div>
            </a>
        </div>
        <div class="index_swiper">
        <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="./images/1.png" alt="">
                    </div>
                    <div class="swiper-slide">
                        <img src="./images/2.png" alt="">
                    </div>
                </div>
                <div class="swipper_arrow">
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        </div>
    </div>
    <script src="./js/swiper.js"></script>
</body>
</html>