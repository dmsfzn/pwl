// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Original search and menu selectors
    let search = document.querySelector('.search-box');
    let menu = document.querySelector('.navbar');
    let header = document.querySelector('header');

    // Original event listeners for search and menu
    const searchIcon = document.querySelector('#search-icon');
    if (searchIcon && search) {
        searchIcon.onclick = () => {
            search.classList.toggle('active');
            if (menu) menu.classList.remove('active'); // Close menu if open
            if (cartSection && cartSection.classList.contains('active')) cartSection.classList.remove('active'); // Close cart
            if (detailsModal && detailsModal.style.display === "block") detailsModal.style.display = "none"; // Close details modal
        };
    }

    const menuIcon = document.querySelector('#menu-icon');
    if (menuIcon && menu) {
        menuIcon.onclick = () => {
            menu.classList.toggle('active');
            if (search) search.classList.remove('active'); // Close search if open
            if (cartSection && cartSection.classList.contains('active')) cartSection.classList.remove('active'); // Close cart
            if (detailsModal && detailsModal.style.display === "block") detailsModal.style.display = "none"; // Close details modal
        };
    }
    
    // Original scroll event for header shadow
    window.addEventListener('scroll', () => {
        if (header) {
            header.classList.toggle('shadow', window.scrollY > 0);
        }
    });

    // --- Shopping Cart Functionality ---
    const cartIcon = document.getElementById('cart-icon');
    const cartSection = document.getElementById('cart-section');
    const closeCartBtn = document.getElementById('close-cart-btn');
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn'); // For parts
    const addCarToCartTriggers = document.querySelectorAll('.add-car-to-cart-trigger'); // For cars
    const cartItemsContainer = document.querySelector('.cart-items-container');
    const cartTotalElement = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    const cartCountElement = document.getElementById('cart-count');
    const customAlertPopup = document.getElementById('custom-alert-popup'); 
    const customAlertMessage = document.getElementById('custom-alert-message'); 

    let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
    let alertTimeout; 

    const showCustomAlert = (message) => {
        if (customAlertPopup && customAlertMessage) {
            customAlertMessage.textContent = message;
            customAlertPopup.classList.add('show');

            if (alertTimeout) {
                clearTimeout(alertTimeout);
            }

            alertTimeout = setTimeout(() => {
                customAlertPopup.classList.remove('show');
            }, 3000);
        } else {
            console.warn('Custom alert elements not found. Falling back to standard alert.');
            alert(message);
        }
    };

    const toggleCart = () => {
        if (cartSection) {
            cartSection.classList.toggle('active');
            if (cartSection.classList.contains('active')) {
                if (search) search.classList.remove('active');
                if (menu) menu.classList.remove('active');
                if (detailsModal && detailsModal.style.display === "block") detailsModal.style.display = "none"; // Close details modal
            }
        }
    };

    if (cartIcon) {
        cartIcon.addEventListener('click', toggleCart);
    }
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', toggleCart);
    }

    const updateCartCount = () => {
        if (cartCountElement) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCountElement.textContent = totalItems;
        }
    };

    const saveCart = () => {
        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        updateCartCount();
    };

    const renderCart = () => {
        if (!cartItemsContainer || !cartTotalElement) return;

        cartItemsContainer.innerHTML = ''; 
        let total = 0;

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p>Your cart is empty.</p>';
            cartTotalElement.textContent = '$0.00';
            updateCartCount();
            return;
        }

        cart.forEach((item, index) => {
            const cartItemElement = document.createElement('div');
            cartItemElement.classList.add('cart-item');
            cartItemElement.innerHTML = `
                <img src="${item.img}" alt="${item.name}">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p class="price">$${parseFloat(item.price).toFixed(2)}</p>
                    <div class="quantity">
                        <label for="qty-${index}">Qty:</label>
                        <input type="number" id="qty-${index}" value="${item.quantity}" min="1" data-index="${index}" class="item-quantity-input">
                    </div>
                    <button class="remove-from-cart-btn" data-index="${index}">Remove</button>
                </div>
            `;
            cartItemsContainer.appendChild(cartItemElement);
            total += parseFloat(item.price) * item.quantity;
        });

        cartTotalElement.textContent = `$${total.toFixed(2)}`;
        addCartActionListeners();
        updateCartCount();
    };
    
    const addItemToCart = (name, price, img) => {
        const existingItemIndex = cart.findIndex(item => item.name === name); 
        if (existingItemIndex > -1) {
            cart[existingItemIndex].quantity += 1;
        } else {
            const numericPrice = parseFloat(price);
            if (isNaN(numericPrice)) {
                console.error("Invalid price for item:", name);
                showCustomAlert("Error: Item price is invalid."); 
                return;
            }
            cart.push({ name, price: numericPrice, img, quantity: 1 });
        }
        saveCart();
        renderCart();
        showCustomAlert(`${name} added to cart!`); 
    };

    const removeItemFromCart = (index) => {
        cart.splice(index, 1);
        saveCart();
        renderCart();
    };

    const updateItemQuantity = (index, newQuantity) => {
        const quantity = parseInt(newQuantity);
        if (isNaN(quantity) || quantity < 1) {
            removeItemFromCart(index); 
        } else {
            cart[index].quantity = quantity;
            saveCart();
            renderCart(); 
        }
    };

    const addCartActionListeners = () => {
        const removeButtons = cartItemsContainer.querySelectorAll('.remove-from-cart-btn');
        removeButtons.forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.index);
                removeItemFromCart(index);
            });
        });

        const quantityInputs = cartItemsContainer.querySelectorAll('.item-quantity-input');
        quantityInputs.forEach(input => {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            
            newInput.addEventListener('change', (e) => {
                const index = parseInt(e.target.dataset.index);
                const newQuantity = parseInt(e.target.value);
                updateItemQuantity(index, newQuantity);
            });
            newInput.addEventListener('keyup', (e) => { 
                 if (e.key === 'Enter') {
                    const index = parseInt(e.target.dataset.index);
                    const newQuantity = parseInt(e.target.value);
                    updateItemQuantity(index, newQuantity);
                }
            });
        });
    };

    if (addToCartButtons) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const name = button.dataset.name;
                const price = button.dataset.price;
                const img = button.dataset.img;
                if (name && price && img) {
                    addItemToCart(name, price, img);
                } else {
                    console.error('Product data missing for button:', button);
                    showCustomAlert('Error: Could not add item. Data missing.');
                }
            });
        });
    }

    if (addCarToCartTriggers) {
        addCarToCartTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault(); 
                const name = trigger.dataset.name;
                const price = trigger.dataset.price;
                const img = trigger.dataset.img;
                if (name && price && img) {
                    addItemToCart(name, price, img);
                } else {
                    console.error('Car data missing for element:', trigger);
                    showCustomAlert('Error: Could not add car. Data missing.');
                }
            });
        });
    }

    const handleCheckout = () => {
        if (cart.length === 0) {
            showCustomAlert('Your cart is empty. Please add items to proceed.');
            return;
        }
        
        // Ambil nama dan email dari variabel global yang diisi PHP (jika ada)
        // atau gunakan placeholder jika tidak ada.
        // Pastikan variabel ini didefinisikan di index.php sebelum script main.js dimuat
        // Contoh di index.php:
        // <script>
        //     const currentUserName = "<?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>";
        //     const currentUserEmail = "<?php echo htmlspecialchars($_SESSION['email'] ?? 'guest@example.com'); ?>";
        // </script>
        const customerName = typeof currentUserName !== 'undefined' ? currentUserName : 'Guest';
        const customerEmail = typeof currentUserEmail !== 'undefined' ? currentUserEmail : 'guest@example.com';

        const checkoutData = {
            items: cart,
            total: cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0).toFixed(2),
            customerName: customerName,
            customerEmail: customerEmail,
            orderDate: new Date().toISOString() // Tambahkan tanggal pesanan
        };

        // Kirim data ke skrip PHP menggunakan fetch
        fetch('checkout.php', { // Pastikan path ke checkout.php benar
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(checkoutData),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text(); // Dapatkan respons sebagai teks (HTML)
        })
        .then(htmlResponse => {
            // Ganti konten halaman saat ini dengan laporan checkout dari server
            // Simpan posisi scroll sebelum mengganti konten
            const scrollY = window.scrollY;
            document.body.innerHTML = htmlResponse;
            // Setelah konten baru dimuat, coba kembalikan posisi scroll
            // Ini mungkin tidak selalu sempurna tergantung bagaimana browser me-render ulang
            window.scrollTo(0, scrollY);

            // Keranjang akan dibersihkan oleh skrip dari checkout.php jika pesanan "disimpan"
            // Jadi tidak perlu membersihkan keranjang di sini secara langsung setelah fetch.
        })
        .catch((error) => {
            console.error('Error during checkout:', error);
            showCustomAlert('An error occurred during checkout. Please try again. (Simulation)');
        });
    };

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', handleCheckout);
    }

    // --- View Details Modal Functionality ---
    const detailsModal = document.getElementById('details-modal');
    const closeModalBtn = document.querySelector('.close-modal-btn');
    const viewDetailsBtns = document.querySelectorAll('.view-details-btn');

    const modalTitle = document.getElementById('modal-title');
    const modalImg = document.getElementById('modal-img');
    const modalPrice = document.getElementById('modal-price');
    const modalReviews = document.getElementById('modal-reviews');
    const modalDescription = document.getElementById('modal-description');

    const productDetailsData = {
        "part1": { 
            name: "Mesin Mobil V-Engine Assembly",
            description: "<p>Unit mesin V-Engine assembly lengkap, siap pasang untuk performa optimal. Ideal untuk penggantian atau proyek restorasi.</p><ul><li>Konfigurasi: V6 / V8 (tergantung stok)</li><li>Kondisi: Baru / Rekondisi Pabrik</li><li>Estimasi Tenaga: 280-450 HP (tergantung model)</li><li>Garansi: 1 Tahun Terbatas</li></ul>",
        },
        "part2": { 
            name: "Set Komponen Rem Depan & Belakang",
            description: "<p>Paket lengkap komponen sistem pengereman untuk roda depan dan belakang. Meningkatkan keamanan dan responsivitas pengereman.</p><ul><li>Termasuk: Cakram Rem Berventilasi, Kaliper, Kampas Rem Keramik</li><li>Material: Baja karbon tinggi (cakram), Aluminium (kaliper)</li><li>Fitur: Pengurangan debu, umur pakai panjang</li></ul>",
        },
        "part3": { 
            name: "Body Fairing Samping Sportbike",
            description: "<p>Panel body fairing samping untuk motor sport. Terbuat dari material ABS berkualitas tinggi, ringan dan tahan benturan.</p><ul><li>Warna: Dasar (siap cat) / Pilihan Warna (tergantung stok)</li><li>Material: Plastik ABS Injection Molded</li><li>Kompatibilitas: Model sportbike tertentu (cek detail)</li></ul>",
        },
        "part4": { 
            name: "Velg Mobil Alloy Klasik (1pcs)",
            description: "<p>Velg mobil alloy dengan desain klasik multi-spoke (model jaring). Memberikan tampilan retro dan sporty pada kendaraan Anda.</p><ul><li>Ukuran: Tersedia 15-18 inch (pilih ukuran)</li><li>Material: Aluminium Alloy Ringan</li><li>Finishing: Silver / Gold Center (tergantung model)</li><li>PCD: Berbagai pilihan (konfirmasi saat pemesanan)</li></ul>",
        },
        "part5": { 
            name: "Set Ban Mobil Radial Performance (4pcs)",
            description: "<p>Satu set (4 buah) ban mobil radial berperforma tinggi. Memberikan cengkeraman maksimal di berbagai kondisi jalan.</p><ul><li>Tipe: Radial All-Season / Summer Performance (pilih tipe)</li><li>Ukuran: Berbagai ukuran tersedia</li><li>Fitur: Pola tapak asimetris, kompon karet canggih</li><li>Speed Rating: H / V / W (tergantung tipe)</li></ul>",
        },
        "part6": { 
            name: "Filter Oli Mesin Mobil (3pcs Pack)",
            description: "<p>Paket berisi 3 buah filter oli mesin berkualitas tinggi. Menjaga kebersihan oli dan melindungi komponen mesin dari keausan.</p><ul><li>Tipe: Spin-on / Cartridge (tergantung model mobil)</li><li>Efisiensi Filtrasi: Tinggi</li><li>Kompatibilitas: Berbagai merek dan model mobil (cek panduan)</li></ul>",
        }
    };

    const openDetailsModal = (productId, nameFromButton, price, reviews, imgSrc) => {
        const details = productDetailsData[productId] || { 
            description: "<p>Detail untuk produk ini belum tersedia.</p>", 
            name: nameFromButton 
        };
        let modalBuyNowBtn = document.getElementById('modal-buy-now-btn'); 

        if (modalTitle) modalTitle.textContent = details.name; 
        if (modalImg) {
            modalImg.src = imgSrc;
            modalImg.alt = details.name; 
        }
        if (modalPrice) modalPrice.textContent = price;
        if (modalReviews) modalReviews.textContent = reviews;
        if (modalDescription) modalDescription.innerHTML = details.description;

        if (modalBuyNowBtn) {
            modalBuyNowBtn.dataset.name = nameFromButton; 
            modalBuyNowBtn.dataset.price = price.replace('$', ''); 
            modalBuyNowBtn.dataset.img = imgSrc; 

            const newModalBuyNowBtn = modalBuyNowBtn.cloneNode(true);
            modalBuyNowBtn.parentNode.replaceChild(newModalBuyNowBtn, modalBuyNowBtn);
            modalBuyNowBtn = newModalBuyNowBtn; 
            
            modalBuyNowBtn.addEventListener('click', (e) => {
                e.preventDefault();
                addItemToCart(
                    modalBuyNowBtn.dataset.name, 
                    modalBuyNowBtn.dataset.price, 
                    modalBuyNowBtn.dataset.img
                );
                if (detailsModal) detailsModal.style.display = "none"; 
            });
        }

        if (detailsModal) {
            detailsModal.style.display = "block";
            if (search && search.classList.contains('active')) search.classList.remove('active');
            if (menu && menu.classList.contains('active')) menu.classList.remove('active');
            if (cartSection && cartSection.classList.contains('active')) cartSection.classList.remove('active');
        }
    };

    if (viewDetailsBtns) {
        viewDetailsBtns.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.dataset.productId;
                const name = button.dataset.name;
                const price = button.dataset.price;
                const reviews = button.dataset.reviews;
                const imgSrc = button.dataset.img;
                openDetailsModal(productId, name, price, reviews, imgSrc);
            });
        });
    }

    if (closeModalBtn && detailsModal) { 
        closeModalBtn.onclick = function() {
            detailsModal.style.display = "none";
        }
    }

    if (detailsModal) { 
        window.onclick = function(event) {
            if (event.target == detailsModal) {
                detailsModal.style.display = "none";
            }
        }
    }
    
    renderCart(); 
});