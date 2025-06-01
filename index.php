<?php
session_start();

// Jika tombol logout ditekan
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

// Jika belum login, arahkan ke login.php
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Data user, misal nama/email diambil dari session
$username = $_SESSION['username'] ?? 'User';
$email = $_SESSION['email'] ?? '';

// Tampilkan halaman utama
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTOMOTIF</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <header>   
        <div class="nav container">
            <i class='bx bx-menu' id="menu-icon"></i> 
             <a href="#" class="logo">Brebes<span>Autos</span></a>
             <ul class="navbar">
                <li><a href="#home"class="active">Home</a></li>
                <li><a href="#cars">Cars</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#parts">Parts</a></li>
                <li><a href="#blog">Blog</a></li>
             </ul>
             <div class="header-icons">
                 <i class='bx bx-search' id="search-icon"></i>
                 <i class='bx bx-shopping-bag' id="cart-icon"></i>
                 <span id="cart-count">0</span>
                 <a href="logout.php" title="Logout"><i class='bx bx-log-out' id="logout-icon"></i></a>
             </div>
             <div class="search-box container">
                <input type="search" name="" id="" placeholder="Search here...">
             </div>
        </div>   
    </header>
    <section class="home" id="home">
       <div class="home-text">
        <h1>We Have Everything <br>Your <span>car</span> Need</h1>
        <p>Temukan mobil impian Anda dengan harga terbaik dan kualitas terjamin di Brebes Autos. Kami menyediakan berbagai pilihan mobil dan suku cadang untuk memenuhi kebutuhan otomotif Anda.</p>
        <a href="#" class="btn">Discover Now</a>   
       </div>
    </section>
    <section class="cars" id="cars">
        <div class="heading">
          <span>ALL Cars</span>
          <h2>We have all types cars</h2>
           <p>Kami menyediakan berbagai jenis mobil, mulai dari mobil keluarga, SUV, hingga mobil sport dengan kualitas terbaik dan harga bersaing. Temukan mobil impian Anda di Brebes Autos!</p> 
        </div>
        <div class="cars-container container">
            <div class="box">
                <img src="foto/mobil1.jpg" alt="Nissan GT-R (R35)">
                <h2 class="add-car-to-cart-trigger" 
                    data-name="Nissan GT-R (R35)" 
                    data-price="4700000000" 
                    data-img="foto/mobil1.jpg" 
                    style="cursor: pointer;">Nissan GT-R (R35)</h2>
            </div>
            <div class="box">
                <img src="foto/mobil2.jpg" alt="Nissan Skyline GT-R (R34)">
                <h2 class="add-car-to-cart-trigger" 
                    data-name="Nissan Skyline GT-R (R34)" 
                    data-price="2950000000" 
                    data-img="foto/mobil2.jpg" 
                    style="cursor: pointer;">Nissan Skyline GT-R (R34)</h2>
            </div>
            <div class="box">
                <img src="foto/mobil3.jpg" alt="Nissan GT-R (R35) Sport">
                <h2 class="add-car-to-cart-trigger" 
                    data-name="Nissan GT-R (R35) Sport" 
                    data-price="1380000000" 
                    data-img="foto/mobil3.jpg" 
                    style="cursor: pointer;">Nissan GT-R (R35) Sport</h2>
            </div>
            <div class="box">
                <img src="foto/mobil4.jpg" alt="Nissan GT-R (R35) White">
                <h2 class="add-car-to-cart-trigger" 
                    data-name="Nissan GT-R (R35) Black" 
                    data-price="540000000" 
                    data-img="foto/mobil4.jpg" 
                    style="cursor: pointer;">Nissan GT-R (R35) White</h2>
            </div>
        </div>
    </section>
    <section class="about container" id="about">
       <div class="about-img">
        <img src="foto/about.png" alt="About Brebes Autos">
    </div>
     <div class="about-text">
        <span>About Us</span>
        <h2>Cheap Prices With <br>Quality Cars</h2>
        <p>Brebes Autos berkomitmen menyediakan mobil berkualitas dengan harga terjangkau. Kami selalu memastikan setiap kendaraan telah melalui proses inspeksi ketat agar pelanggan mendapatkan mobil terbaik sesuai kebutuhan dan anggaran. Kepuasan dan kepercayaan Anda adalah prioritas utama kami.</p>
        <a href="#" class="btn">Learn More</a>
     </div>
    </section>
    <section class="parts" id="parts">
        <div class="heading">
            <span>What We Offer</span>
            <h2>Our Car Is Always Excellent</h2>
            <p>Kami menyediakan suku cadang mobil berkualitas tinggi dengan harga terjangkau. Setiap produk telah melalui proses seleksi ketat untuk memastikan keamanan dan kepuasan pelanggan.</p>
        </div>
        <div class="parts-container container">
            <div class="box">
            <img src="foto/patrs1.png" alt="Mesin V-Engine Mobil" id="part-img-mesin">
            <h3>Mesin V-Engine</h3>
            <span class="price">Rp. 8.200.000</span>
            <i class='bx bxs-star'>(9 Reviws)</i>
            <a href="#" class="btn add-to-cart-btn" data-name="Mesin V-Engine" data-price="8200000" data-img="foto/patrs1.png">Buy Now</a>
            <a href="#" class="details view-details-btn" 
               data-name="Mesin V-Engine" 
               data-price="8200000" 
               data-reviews="(9 Reviws)" 
               data-img="foto/patrs1.png"
               data-product-id="part1">View Details</a>
            </div>
        <div class="box">
            <img src="foto/parts2.png" alt="Set Komponen Rem Mobil" id="part-img-spa">
            <h3>Set Komponen Rem</h3>
            <span class="price">Rp. 4.000.000</span>
            <i class='bx bxs-star'>(7 Reviws)</i>
            <a href="#" class="btn add-to-cart-btn" data-name="Set Komponen Rem" data-price="4000000" data-img="foto/parts2.png">Buy Now</a>
            <a href="#" class="details view-details-btn"
               data-name="Set Komponen Rem"
               data-price="4000000"
               data-reviews="(7 Reviws)"
               data-img="foto/parts2.png"
               data-product-id="part2">View Details</a>
        </div>
          <div class="box">
            <img src="foto/parts3.png" alt="Body Fairing Motor Sport" id="part-img-spb">
            <h3>Body Fairing Motor</h3>
            <span class="price">Rp. 2.500.000</span>
            <i class='bx bxs-star'>(5 Reviws)</i>
            <a href="#" class="btn add-to-cart-btn" data-name="Body Fairing Motor" data-price="2500000" data-img="foto/parts3.png">Buy Now</a>
            <a href="#" class="details view-details-btn"
               data-name="Body Fairing Motor"
               data-price="2500000"
               data-reviews="(5 Reviws)"
               data-img="foto/parts3.png"
               data-product-id="part3">View Details</a>
          </div>
          <div class="box">
            <img src="foto/parts4.png" alt="Velg Mobil Alloy Klasik" id="part-img-spc">
            <h3>Velg Alloy Klasik</h3>
            <span class="price">Rp. 150.000</span>
            <i class='bx bxs-star'>(12 Reviws)</i>
            <a href="#" class="btn add-to-cart-btn" data-name="Velg Alloy Klasik" data-price="150000" data-img="foto/parts4.png">Buy Now</a>
            <a href="#" class="details view-details-btn"
               data-name="Velg Alloy Klasik"
               data-price="150000"
               data-reviews="(12 Reviws)"
               data-img="foto/parts4.png"
               data-product-id="part4">View Details</a>
          </div>
          <div class="box">
            <img src="foto/parts5.png" alt="Set Ban Mobil Radial" id="part-img-spd">
            <h3>Set Ban Mobil</h3>
            <span class="price">Rp. 400.000</span>
            <i class='bx bxs-star'>(8 Reviws)</i>
            <a href="#" class="btn add-to-cart-btn" data-name="Set Ban Mobil" data-price="400000" data-img="foto/parts5.png">Buy Now</a>
            <a href="#" class="details view-details-btn"
               data-name="Set Ban Mobil"
               data-price="400000"
               data-reviews="(8 Reviws)"
               data-img="foto/parts5.png"
               data-product-id="part5">View Details</a>
          </div>
          <div class="box">
            <img src="foto/patrs6.png" alt="Filter Oli Mobil" id="part-img-spe"> 
            <h3>Filter Oli Mobil</h3>
            <span class="price">Rp. 320.000</span>
            <i class='bx bxs-star'>(10 Reviws)</i>
            <a href="#" class="btn add-to-cart-btn" data-name="Filter Oli Mobil" data-price="320000" data-img="foto/patrs6.png">Buy Now</a>
            <a href="#" class="details view-details-btn"
               data-name="Filter Oli Mobil"
               data-price="320000"
               data-reviews="(10 Reviws)"
               data-img="foto/patrs6.png"
               data-product-id="part6">View Details</a>
          </div>
        </div>
    </section>
        <section class="blog" id="blog">
            <div class="heading">
                <span>Blog & News</span>
                <h2>Our Blog Content</h2>
                <p>Dapatkan informasi terbaru seputar dunia otomotif, tips perawatan mobil, ulasan produk, dan berita menarik lainnya hanya di Brebes Autos. Kami selalu menghadirkan konten bermanfaat untuk Anda para pecinta otomotif.</p>
            </div>
            <div class="blog-container container">
                <div class="box">
                   <img src="foto/mobil1.jpg" alt="Blog post image 1">
                    <span>2 Mei 2025</span>
                    <h3>Cara Mendapatkan Mobil Sempurna dengan Harga Terjangkau</h3>
                    <p>Tips memilih mobil berkualitas dengan harga yang sesuai anggaran Anda.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
                 <div class="box">
                   <img src="foto/mobil2.jpg" alt="Blog post image 2">
                    <span>18 Maret 2024</span>
                    <h3>Panduan Perawatan Mobil untuk Pemula</h3>
                    <p>Langkah mudah merawat mobil agar tetap awet dan nyaman digunakan.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
                 <div class="box">
                   <img src="foto/mobil3.jpg" alt="Blog post image 3">
                    <span>7 Januari 2024</span>
                    <h3>Keuntungan Membeli Mobil Bekas Berkualitas</h3>
                    <p>Alasan memilih mobil bekas bisa menjadi solusi cerdas untuk Anda.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
                 <div class="box">
                   <img src="foto/mobil4.jpg" alt="Blog post image 4">
                    <span>27 Juni 2023</span>
                    <h3>Tips Aman Berkendara di Musim Hujan</h3>
                    <p>Cara menjaga keselamatan saat mengemudi di kondisi hujan dan jalan licin.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
            </div>
        </section>

    <section class="cart-section" id="cart-section">
        <div class="cart-header">
            <h2>Your Cart</h2>
            <i class='bx bx-x' id="close-cart-btn"></i>
        </div>
        <div class="cart-items-container">
            </div>
        <div class="cart-summary">
            <p>Total: <span id="cart-total">$0.00</span></p>
            <button class="btn" id="checkout-btn">Checkout</button>
        </div>
    </section>

    <div id="details-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal-btn">&times;</span>
        <h2 id="modal-title">Product Details</h2>
        <img id="modal-img" src="" alt="Product Image" style="width: 200px; height: auto; margin-bottom: 1rem; border-radius: 0.3rem;">
        <p><strong>Price:</strong> <span id="modal-price"></span></p>
        <p><strong>Reviews:</strong> <span id="modal-reviews"></span></p>
        <div id="modal-description">
            </div>
        <a href="#" id="modal-buy-now-btn" class="btn" style="margin-top: 1rem;">Buy Now</a>
    </div>
    </div>

    <section class="footer">
        <div class="footer-container container">
         <div class="footer box">  
            <a href="#" class="logo">Car<span>Point</span></a>
            <div class="social">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-instagram'></i></a>
                <a href="#"><i class='bx bxl-twitter'></i></a>
                <a href="#"><i class='bx bxl-youtube'></i></a>
            </div>
         </div>  
         <div class="footer-box">
            <h3>Page</h3>
            <a href="#">Home</a>
            <a href="#">Cars</a>
            <a href="#">parts</a>
            <a href="#">Sales</a>
         </div> 
          <div class="footer-box">
            <h3>Legal</h3>
            <a href="#">Privacy</a>
            <a href="#">Refund Policy</a>
            <a href="#">Cookie Policy</a>
            
         </div>
          <div class="footer-box">
           <h3>Containt</h3>
           <p>Indonesia</p>
           <p>Japan</p>
           <p>United States</p>
         </div>  
        </div>
    </section>
    <div class="copyright">
       <p>&#169; DellerBapuk Siap Menerima Servis</p>
    </div>
    <div id="custom-alert-popup" class="custom-alert">
        <p id="custom-alert-message"></p>
    </div>
    <script>
        const currentUserName = "<?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>";
        const currentUserEmail = "<?php echo htmlspecialchars($_SESSION['email'] ?? 'guest@example.com'); ?>";
    </script>
    <script src="main.js"> </script>
</body>
</html>