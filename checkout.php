<?php
session_start();
require_once 'config.php'; // Menggunakan koneksi $conn dari file ini

// Fungsi untuk format angka (yang sudah dalam Rupiah) ke tampilan string Rupiah
function formatRupiahForDisplay($amount) {
    $numericAmount = floatval($amount);
    // Format Rupiah dengan pemisah ribuan titik dan tanpa desimal
    return "Rp " . number_format($numericAmount, 0, ',', '.');
}

function getClearClientCartScript() {
    return "
        <script>
            if (localStorage.getItem('shoppingCart')) {
                localStorage.removeItem('shoppingCart');
                console.log('Client-side cart (localStorage) cleared by PHP script.');
            }
            // alert('Pesanan Anda telah berhasil disimpan! Keranjang belanja telah dikosongkan.');
            // setTimeout(function() {
            //    window.location.href = 'index.php'; 
            // }, 2000); 
        </script>
    ";
}

$pageTitle = "Laporan Checkout";
$reportContent = "";
$showSaveButton = true; 
$clientCartClearScript = ""; 
$checkoutDataForForm = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_order_simulation') {
        $pageTitle = "Konfirmasi Penyimpanan Pesanan";
        $checkoutDataJSON = $_POST['checkout_data'] ?? null;

        if ($checkoutDataJSON) {
            $checkoutData = json_decode(urldecode($checkoutDataJSON), true);

            if (json_last_error() === JSON_ERROR_NONE && isset($checkoutData['items']) && isset($checkoutData['total'])) {
                $items = $checkoutData['items']; // Harga di sini sekarang dalam Rupiah
                $totalAmountRupiah = $checkoutData['total']; // Total dalam Rupiah
                $customerName = $checkoutData['customerName'];
                $customerEmail = $checkoutData['customerEmail'];
                $orderDate = $checkoutData['orderDate'];
                // $currency = $checkoutData['currency']; // Seharusnya 'IDR'

                if ($conn) { 
                    $conn->begin_transaction(); 
                    try {
                        $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null; 
                        $formattedOrderDate = date('Y-m-d H:i:s', strtotime($orderDate));
                        $orderStatus = 'Processing';
                        $lastOrderId = null;
                        
                        // Simpan total_amount sebagai nilai Rupiah
                        $totalAmountToStore = floatval($totalAmountRupiah); 

                        if ($userId === null) { 
                             $stmtOrder = $conn->prepare("INSERT INTO orders (customer_name, customer_email, total_amount, order_date, order_status) VALUES (?, ?, ?, ?, ?)");
                             if (!$stmtOrder) throw new Exception("Prepare orders (guest) gagal: " . $conn->error);
                             // Pastikan total_amount di bind sebagai double/decimal (d)
                             $stmtOrder->bind_param("ssdss", $customerName, $customerEmail, $totalAmountToStore, $formattedOrderDate, $orderStatus);
                        } else { 
                            $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_email, total_amount, order_date, order_status) VALUES (?, ?, ?, ?, ?, ?)");
                            if (!$stmtOrder) throw new Exception("Prepare orders (user) gagal: " . $conn->error);
                            $stmtOrder->bind_param("isssss", $userId, $customerName, $customerEmail, $totalAmountToStore, $formattedOrderDate, $orderStatus);
                        }
                        
                        if (!$stmtOrder->execute()) throw new Exception("Gagal menyimpan pesanan utama: " . $stmtOrder->error);
                        $lastOrderId = $conn->insert_id; 
                        $stmtOrder->close();

                        if ($lastOrderId) {
                            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price_per_item, sub_total, product_image_url) VALUES (?, ?, ?, ?, ?, ?)");
                            if (!$stmtItem) throw new Exception("Prepare order_items gagal: " . $conn->error);
                            foreach ($items as $item) {
                                $productName = $item['name'];
                                $quantity = intval($item['quantity']);
                                $pricePerItemRupiah = floatval($item['price']); // Harga per item Rupiah
                                $subTotalRupiah = $pricePerItemRupiah * $quantity; // Subtotal Rupiah
                                $productImageUrl = $item['img'] ?? null; 

                                // Simpan harga dan subtotal dalam Rupiah ke database
                                $stmtItem->bind_param("isidds", $lastOrderId, $productName, $quantity, $pricePerItemRupiah, $subTotalRupiah, $productImageUrl);
                                if (!$stmtItem->execute()) throw new Exception("Gagal menyimpan item ('".htmlspecialchars($productName)."'): " . $stmtItem->error);
                            }
                            $stmtItem->close();
                        } else {
                             throw new Exception("Gagal mendapatkan ID pesanan terakhir.");
                        }
                        
                        $conn->commit(); 
                        $reportContent .= "<div class='success-message'><strong>Pesanan Anda (#" . $lastOrderId .") Telah Berhasil Disimpan!</strong></div>";
                        // Tampilkan kembali detail pesanan dalam Rupiah setelah disimpan
                        $reportContent .= "<div class='customer-info'>";
                        $reportContent .= "<h3>Detail Pesanan yang Disimpan</h3>";
                        $reportContent .= "<p><strong>Tanggal:</strong> " . htmlspecialchars(date("d M Y, H:i", strtotime($orderDate))) . "</p>";
                        $reportContent .= "<p><strong>Nama:</strong> " . htmlspecialchars($customerName) . "</p>";
                        $reportContent .= "<p><strong>Email:</strong> " . htmlspecialchars($customerEmail) . "</p>";
                        $reportContent .= "</div>";
                        $reportContent .= "<h3>Barang yang Dibeli:</h3><ul>";
                        foreach ($items as $item) { 
                            $itemSubtotalRupiah = floatval($item['price']) * intval($item['quantity']);
                            $reportContent .= "<li>" . htmlspecialchars($item['name']) . " (Qty: " . htmlspecialchars($item['quantity']) . ") - " . formatRupiahForDisplay($itemSubtotalRupiah) . "</li>";
                        }
                        $reportContent .= "</ul><div class='total'>Total: " . formatRupiahForDisplay($totalAmountRupiah) . "</div>";
                        
                        $showSaveButton = false;
                        $clientCartClearScript = getClearClientCartScript();

                    } catch (Exception $e) {
                        $conn->rollback(); 
                        $reportContent .= "<div class='error-message'>Error: Terjadi masalah saat menyimpan pesanan. Silakan coba lagi.<br>Detail: " . htmlspecialchars($e->getMessage()) . "</div>";
                        if (isset($checkoutData) && isset($checkoutData['items'])) {
                            $reportContent .= "<div class='customer-info' style='margin-top:20px;'>";
                            $reportContent .= "<h3>Detail Pesanan (Gagal Simpan)</h3>";
                            $reportContent .= "<p><strong>Tanggal:</strong> " . htmlspecialchars(date("d M Y, H:i", strtotime($checkoutData['orderDate']))) . "</p>";
                            $reportContent .= "<p><strong>Nama:</strong> " . htmlspecialchars($checkoutData['customerName']) . "</p>";
                            $reportContent .= "<p><strong>Email:</strong> " . htmlspecialchars($checkoutData['customerEmail']) . "</p>";
                            $reportContent .= "</div>";
                            $reportContent .= "<h3>Barang yang Dibeli:</h3><ul>";
                           foreach ($checkoutData['items'] as $item) {
                               $itemSubtotalRupiah_fail = floatval($item['price']) * intval($item['quantity']);
                               $reportContent .= "<li>" . htmlspecialchars($item['name']) . " (Qty: " . htmlspecialchars($item['quantity']) . ") - " . formatRupiahForDisplay($itemSubtotalRupiah_fail) . "</li>";
                           }
                           $reportContent .= "</ul><div class='total'>Total: " . formatRupiahForDisplay($checkoutData['total']) . "</div>";
                       }
                    }
                } else {
                    $reportContent .= "<div class='error-message'>Error: Gagal terhubung ke database. Pesanan tidak dapat disimpan.</div>";
                }
            } else {
                $reportContent .= "<div class='error-message'>Error: Gagal memproses data pesanan (simpan). Format JSON tidak valid atau data item/total hilang.</div>";
            }
        } else {
            $reportContent .= "<div class='error-message'>Error: Tidak ada data pesanan (simpan).</div>";
        }
        $checkoutDataForForm = isset($_POST['checkout_data']) ? $_POST['checkout_data'] : "";

    } else { 
        $jsonPayload = file_get_contents('php://input');
        $checkoutData = json_decode($jsonPayload, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($checkoutData['items']) && isset($checkoutData['total'])) {
            $items = $checkoutData['items']; // Harga item dalam Rupiah
            $totalAmountRupiah = $checkoutData['total']; // Total dalam Rupiah
            $customerName = $checkoutData['customerName'] ?? 'Guest'; 
            $customerEmail = $checkoutData['customerEmail'] ?? 'guest@example.com';
            $orderDate = $checkoutData['orderDate'] ?? date("Y-m-d H:i:s"); 
            // $currency = $checkoutData['currency']; // Seharusnya 'IDR'

            $checkoutDataForForm = urlencode(json_encode($checkoutData)); // Data ini sekarang dalam Rupiah

            $reportContent .= "<div class='customer-info'>";
            $reportContent .= "<h3>Informasi Pelanggan</h3>";
            $reportContent .= "<p><strong>Tanggal Pesanan:</strong> " . htmlspecialchars(date("d M Y, H:i", strtotime($orderDate))) . "</p>";
            $reportContent .= "<p><strong>Nama:</strong> " . htmlspecialchars($customerName) . "</p>";
            $reportContent .= "<p><strong>Email:</strong> " . htmlspecialchars($customerEmail) . "</p>";
            $reportContent .= "</div>";
            
            $reportContent .= "<h3>Barang yang Dibeli</h3>";
            if (!empty($items)) {
                $reportContent .= "<ul>";
                foreach ($items as $item) {
                    $itemSubtotalRupiah = floatval($item['price']) * intval($item['quantity']);
                    $reportContent .= "<li><span>" . htmlspecialchars($item['name']) . " (Qty: " . htmlspecialchars($item['quantity']) . ")</span><span>" . formatRupiahForDisplay($itemSubtotalRupiah) . "</span></li>";
                }
                $reportContent .= "</ul>";
                $reportContent .= "<div class='total'>Total Pembelian: " . formatRupiahForDisplay($totalAmountRupiah) . "</div>";
            } else {
                $reportContent .= "<p>Keranjang belanja Anda kosong.</p>";
                $showSaveButton = false;
            }
        } else {
            $reportContent .= "<div class='error-message'>Error: Data checkout tidak valid atau format JSON salah.</div>";
            $showSaveButton = false;
        }
    }
} else { 
    $pageTitle = "Akses Tidak Sah";
    $reportContent .= "<div class='error-message'>Error: Halaman ini tidak dapat diakses secara langsung. Silakan melalui proses checkout.</div>";
    $showSaveButton = false;
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; background-color: #f4f6f9; color: #333; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding: 20px;}
        .report-container { 
            background-color: #ffffff; 
            padding: 30px 35px; 
            border-radius: 10px; 
            box-shadow: 0 6px 20px rgba(0,0,0,0.08); 
            width: 100%;
            max-width: 750px; 
            border-top: 6px solid #d90429; /* Warna utama tema Anda */
        }
        .report-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef;}
        .report-header h1 { color: #d90429; margin: 0; font-size: 2em; font-weight: 600;}
        .close-report-btn { font-size: 1.8em; color: #868e96; text-decoration: none; }
        .close-report-btn:hover { color: #343a40; }
        h3 { color: #495057; margin-top: 25px; margin-bottom: 15px; font-size: 1.3em; font-weight: 500;}
        ul { list-style-type: none; padding: 0; }
        li { background-color: #f8f9fa; margin-bottom: 10px; padding: 15px; border-radius: 6px; display: flex; justify-content: space-between; border: 1px solid #e9ecef; font-size: 1em;}
        li span:first-child { font-weight: 500; color: #495057; }
        li span:last-child { font-weight: 600; color: #212529; }
        .customer-info { background-color: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; margin-bottom:20px;}
        .customer-info p { margin: 10px 0; line-height: 1.7; font-size: 0.95em; }
        .customer-info p strong { font-weight: 600; color: #343a40; min-width:150px; display:inline-block;}
        .total { font-weight: bold; font-size: 1.4em; margin-top: 25px; padding-top:15px; border-top:1px solid #dee2e6; text-align: right; color: #d90429;}
        .action-buttons { margin-top: 35px; display: flex; justify-content: flex-end; gap: 15px; }
        .action-buttons button, .action-buttons a { padding: 12px 25px; font-size: 1em; border-radius: 6px; text-decoration: none; cursor: pointer; font-family: 'Poppins', sans-serif; font-weight: 500; transition: all 0.3s ease; border: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-save { background-color: #28a745; color: white; }
        .btn-save:hover { background-color: #218838; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-back { background-color: #6c757d; color: white; }
        .btn-back:hover { background-color: #5a6268; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success-message, .error-message { padding: 15px 20px; margin: 25px 0; border-radius: 6px; font-size: 1em; text-align: center; border-left-width: 5px; border-left-style: solid; }
        .success-message { background-color: #e6ffed; color: #1db954; border-left-color: #1db954;}
        .error-message { background-color: #ffebee; color: #f44336; border-left-color: #f44336;}
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            <a href="index.php" class="close-report-btn" title="Tutup dan Kembali ke Toko"><i class='bx bx-x'></i></a>
        </div>
        <?php echo $reportContent; ?>
        <div class="action-buttons">
            <?php if ($showSaveButton && !empty($checkoutDataForForm)): ?>
            <form action="checkout.php" method="POST" style="display: inline;">
                <input type="hidden" name="action" value="save_order_simulation">
                <input type="hidden" name="checkout_data" value="<?php echo $checkoutDataForForm; ?>">
                <button type="submit" class="btn-save"><i class='bx bx-save'></i> Simpan Pesanan</button>
            </form>
            <?php endif; ?>
            <a href="index.php" class="btn-back"><i class='bx bx-arrow-back'></i> Kembali ke Toko</a>
        </div>
    </div>
    <?php echo $clientCartClearScript; ?>
</body>
</html>