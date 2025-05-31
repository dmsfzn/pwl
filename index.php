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
             <i class='bx bx-search' id="search-icon"></i>
             <div class="search-box container">
                <input type="search" nama="" id="" placeholder="Search here...">
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
            <img src="foto/mobil1.jpg" alt="">
            <h2>Nissan GT-R (R35)</h2>
            </div>
            <div class="box">
            <img src="foto/mobil2.jpg" alt="">
            <h2>Nissan Skyline GT-R (R34)</h2>
            </div>
            <div class="box">
            <img src="foto/mobil3.jpg" alt="">
            <h2>Nissan GT-R (R35)</h2>
            </div>
            <div class="box">
            <img src="foto/mobil4.jpg" alt="">
            <h2>Nissan GT-R (R35)</h2>
            </div>
        </div>
    </section>
    <section class="about container" id="about">
       <div class="about-img">
        <img src="foto/about.png" alt="">
    </div>
     <div class="about-text">
        <span>About Us</span>
        <h2>Cheap Prices With <br>Quality Cars</h2>
        <p>Brebes Autos berkomitmen menyediakan mobil berkualitas dengan harga terjangkau. Kami selalu memastikan setiap kendaraan telah melalui proses inspeksi ketat agar pelanggan mendapatkan mobil terbaik sesuai kebutuhan dan anggaran. Kepuasan dan kepercayaan Anda adalah prioritas utama kami.</p>
        <a href="" class="btn">Learn More</a>
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
              <img src="foto/patrs1.png" alt="">
               <h3>Mesin</h3>
                <span>$500</span>
                <i class='bx bxs-star'>(9 Reviws)</i>
                <a href="#" class="btn">Buy Now</a>
                <a href="#" class="details">View Details</a>
            </div>
            <div class="box">
                <img src="foto/parts2.png" alt="">
                 <h3>Auto Spare Parts</h3>
                  <span>$500</span>
                  <i class='bx bxs-star'>(9 Reviws)</i>
                  <a href="#" class="btn">Buy Now</a>
                  <a href="#" class="details">View Details</a>
              </div>
              <div class="box">
                <img src="foto/parts3.png" alt="">
                 <h3>Auto Spare Parts</h3>
                  <span>$500</span>
                  <i class='bx bxs-star'>(9 Reviws)</i>
                  <a href="#" class="btn">Buy Now</a>
                  <a href="#" class="details">View Details</a>
              </div>
              <div class="box">
                <img src="foto/parts4.png" alt="">
                 <h3>Auto Spare Parts</h3>
                  <span>$500</span>
                  <i class='bx bxs-star'>(9 Reviws)</i>
                  <a href="#" class="btn">Buy Now</a>
                  <a href="#" class="details">View Details</a>
              </div>
              <div class="box">
                <img src="foto/parts5.png" alt="">
                 <h3>Auto Spare Parts</h3>
                  <span>$500</span>
                  <i class='bx bxs-star'>(9 Reviws)</i>
                  <a href="#" class="btn">Buy Now</a>
                  <a href="#" class="details">View Details</a>
              </div>
              <div class="box">
                <img src="foto/patrs6.png" alt="">
                 <h3>Auto Spare Parts</h3>
                  <span>$500</span>
                  <i class='bx bxs-star'>(9 Reviws)</i>
                  <a href="#" class="btn">Buy Now</a>
                  <a href="#" class="details">View Details</a>
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
                   <img src="foto/mobil1.jpg" alt="">
                    <span>2 Mei 2025</span>
                    <h3>Cara Mendapatkan Mobil Sempurna dengan Harga Terjangkau</h3>
                    <p>Tips memilih mobil berkualitas dengan harga yang sesuai anggaran Anda.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
                 <div class="box">
                   <img src="foto/mobil2.jpg" alt="">
                    <span>18 Maret 2024</span>
                    <h3>Panduan Perawatan Mobil untuk Pemula</h3>
                    <p>Langkah mudah merawat mobil agar tetap awet dan nyaman digunakan.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
                 <div class="box">
                   <img src="foto/mobil3.jpg" alt="">
                    <span>7 Januari 2024</span>
                    <h3>Keuntungan Membeli Mobil Bekas Berkualitas</h3>
                    <p>Alasan memilih mobil bekas bisa menjadi solusi cerdas untuk Anda.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
                 <div class="box">
                   <img src="foto/mobil4.jpg" alt="">
                    <span>27 Juni 2023</span>
                    <h3>Tips Aman Berkendara di Musim Hujan</h3>
                    <p>Cara menjaga keselamatan saat mengemudi di kondisi hujan dan jalan licin.</p>
                    <a href="#" class="blog-btn">Read More<i class='bx bx-right-arrow-alt'></i></a>
                </div>
            </div>
        </section>
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
    <script src="main.js"> </script>
</body>
</html>
