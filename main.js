// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Original search and menu selectors
    let search = document.querySelector('.search-box');
    let menu = document.querySelector('.navbar');
    let header = document.querySelector('header');

    // Fungsi untuk format angka ke format mata uang Rupiah
    const formatToRupiahDisplay = (amount) => {
        // Pastikan amount adalah angka
        const numericAmount = parseFloat(amount);
        if (isNaN(numericAmount)) {
            console.warn("Invalid amount for Rupiah formatting:", amount);
            return "Rp 0"; // Atau penanganan error lain
        }
        return new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR',
            minimumFractionDigits: 0, 
            maximumFractionDigits: 0 
        }).format(numericAmount);
    };

    // Original event listeners for search and menu
    const searchIcon = document.querySelector('#search-icon');
    if (searchIcon && search) {
        searchIcon.onclick = () => {
            search.classList.toggle('active');
            if (menu) menu.classList.remove('active');
            if (cartSection && cartSection.classList.contains('active')) cartSection.classList.remove('active');
            if (detailsModal && detailsModal.style.display === "block") detailsModal.style.display = "none";
        };
    }

    const menuIcon = document.querySelector('#menu-icon');
    if (menuIcon && menu) {
        menuIcon.onclick = () => {
            menu.classList.toggle('active');
            if (search) search.classList.remove('active');
            if (cartSection && cartSection.classList.contains('active')) cartSection.classList.remove('active');
            if (detailsModal && detailsModal.style.display === "block") detailsModal.style.display = "none";
        };
    }
    
    window.addEventListener('scroll', () => {
        if (header) {
            header.classList.toggle('shadow', window.scrollY > 0);
        }
    });

    // --- Shopping Cart Functionality ---
    const cartIcon = document.getElementById('cart-icon');
    const cartSection = document.getElementById('cart-section');
    const closeCartBtn = document.getElementById('close-cart-btn');
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const addCarToCartTriggers = document.querySelectorAll('.add-car-to-cart-trigger');
    const cartItemsContainer = document.querySelector('.cart-items-container');
    const cartTotalElement = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    const cartCountElement = document.getElementById('cart-count');
    const customAlertPopup = document.getElementById('custom-alert-popup'); 
    const customAlertMessage = document.getElementById('custom-alert-message'); 

    let cart = JSON.parse(localStorage.getItem('shoppingCart')) || []; // Harga dalam cart sekarang adalah Rupiah
    let alertTimeout; 

    const showCustomAlert = (message) => {
        if (customAlertPopup && customAlertMessage) {
            customAlertMessage.textContent = message;
            customAlertPopup.classList.add('show');
            if (alertTimeout) clearTimeout(alertTimeout);
            alertTimeout = setTimeout(() => {
                customAlertPopup.classList.remove('show');
            }, 3000);
        } else {
            alert(message);
        }
    };

    const toggleCart = () => {
        if (cartSection) {
            cartSection.classList.toggle('active');
            if (cartSection.classList.contains('active')) {
                if (search) search.classList.remove('active');
                if (menu) menu.classList.remove('active');
                if (detailsModal && detailsModal.style.display === "block") detailsModal.style.display = "none";
            }
        }
    };

    if (cartIcon) cartIcon.addEventListener('click', toggleCart);
    if (closeCartBtn) closeCartBtn.addEventListener('click', toggleCart);

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
        let totalRupiah = 0; 

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p>Your cart is empty.</p>';
            cartTotalElement.textContent = formatToRupiahDisplay(0);
            updateCartCount();
            return;
        }

        cart.forEach((item, index) => {
            const cartItemElement = document.createElement('div');
            cartItemElement.classList.add('cart-item');
            // item.price sekarang adalah nilai numerik Rupiah
            cartItemElement.innerHTML = `
                <img src="${item.img}" alt="${item.name}">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p class="price">${formatToRupiahDisplay(item.price)}</p> 
                    <div class="quantity">
                        <label for="qty-${index}">Qty:</label>
                        <input type="number" id="qty-${index}" value="${item.quantity}" min="1" data-index="${index}" class="item-quantity-input">
                    </div>
                    <button class="remove-from-cart-btn" data-index="${index}">Remove</button>
                </div>
            `;
            cartItemsContainer.appendChild(cartItemElement);
            totalRupiah += parseFloat(item.price) * item.quantity;
        });

        cartTotalElement.textContent = formatToRupiahDisplay(totalRupiah);
        addCartActionListeners();
        updateCartCount();
    };
    
    const addItemToCart = (name, priceRupiah, img) => { // priceRupiah adalah nilai numerik
        const existingItemIndex = cart.findIndex(item => item.name === name); 
        const numericPriceRupiah = parseFloat(priceRupiah);

        if (isNaN(numericPriceRupiah)) {
            showCustomAlert("Error: Harga produk tidak valid."); 
            return;
        }

        if (existingItemIndex > -1) {
            cart[existingItemIndex].quantity += 1;
        } else {
            cart.push({ name, price: numericPriceRupiah, img, quantity: 1 }); // Simpan harga Rupiah numerik
        }
        saveCart();
        renderCart();
        showCustomAlert(`${name} ditambahkan ke keranjang!`); 
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

    // Fungsi untuk membersihkan format Rupiah dari string (misal "Rp 7.500.000" atau "7.500.000") menjadi angka
    const parseRupiahString = (rupiahString) => {
        if (typeof rupiahString !== 'string') return parseFloat(rupiahString); // Jika sudah angka
        // Hapus "Rp", spasi, dan titik pemisah ribuan
        const numericString = rupiahString.replace(/Rp\s*|\./g, '');
        return parseFloat(numericString);
    };


    if (addToCartButtons) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const name = button.dataset.name;
                // Asumsi data-price sekarang berisi nilai numerik Rupiah, misal "7500000"
                const priceRupiah = parseRupiahString(button.dataset.price); 
                const img = button.dataset.img;
                if (name && !isNaN(priceRupiah) && img) {
                    addItemToCart(name, priceRupiah, img);
                } else {
                    showCustomAlert('Error: Tidak dapat menambahkan item. Data produk kurang.');
                }
            });
        });
    }

    if (addCarToCartTriggers) {
        addCarToCartTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault(); 
                const name = trigger.dataset.name;
                // Asumsi data-price sekarang berisi nilai numerik Rupiah
                const priceRupiah = parseRupiahString(trigger.dataset.price);
                const img = trigger.dataset.img;
                if (name && !isNaN(priceRupiah) && img) {
                    addItemToCart(name, priceRupiah, img);
                } else {
                    showCustomAlert('Error: Tidak dapat menambahkan mobil. Data produk kurang.');
                }
            });
        });
    }

    const handleCheckout = () => {
        if (cart.length === 0) {
            showCustomAlert('Keranjang Anda kosong. Silakan tambahkan item.');
            return;
        }
        
        const customerName = typeof currentUserName !== 'undefined' ? currentUserName : 'Guest';
        const customerEmail = typeof currentUserEmail !== 'undefined' ? currentUserEmail : 'guest@example.com';

        // Data yang dikirim ke server sekarang dalam Rupiah
        const checkoutData = {
            items: cart.map(item => ({ 
                ...item,
                price: parseFloat(item.price) // Harga sudah dalam Rupiah numerik
            })),
            total: cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0).toFixed(0), // Total dalam Rupiah numerik, tanpa desimal
            customerName: customerName,
            customerEmail: customerEmail,
            orderDate: new Date().toISOString(),
            currency: 'IDR' // Mata uang sekarang IDR
            // exchangeRate tidak relevan lagi jika semua dalam IDR
        };

        fetch('checkout.php', { 
            method: 'POST',
            headers: { 'Content-Type': 'application/json', },
            body: JSON.stringify(checkoutData),
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.text(); 
        })
        .then(htmlResponse => {
            const scrollY = window.scrollY;
            document.body.innerHTML = htmlResponse; 
            window.scrollTo(0, scrollY);
        })
        .catch((error) => {
            console.error('Error saat checkout:', error);
            showCustomAlert('Terjadi error saat checkout. Silakan coba lagi.');
        });
    };

    if (checkoutBtn) checkoutBtn.addEventListener('click', handleCheckout);

    // --- View Details Modal Functionality ---
    const detailsModal = document.getElementById('details-modal');
    const closeModalBtn = document.querySelector('.close-modal-btn');
    const viewDetailsBtns = document.querySelectorAll('.view-details-btn');
    const modalTitle = document.getElementById('modal-title');
    const modalImg = document.getElementById('modal-img');
    const modalPrice = document.getElementById('modal-price');
    const modalReviews = document.getElementById('modal-reviews');
    const modalDescription = document.getElementById('modal-description');

    // productDetailsData tidak menyimpan harga, harga diambil dari tombol
    const productDetailsData = {
        "part1": { 
            name: "Mesin Mobil V-Engine Assembly", 
            description: `
                <p>Unit mesin V-Engine assembly lengkap, cocok untuk berbagai tipe mobil sport dan sedan premium. Dirakit dengan presisi tinggi dan material berkualitas.</p>
                <ul>
                    <li>Kapasitas: 3000cc, 6 silinder</li>
                    <li>Tenaga maksimum: 250 HP</li>
                    <li>Sudah termasuk sistem pendingin dan kelistrikan</li>
                    <li>Garansi resmi 1 tahun</li>
                </ul>
            `
        },
        "part2": { 
            name: "Set Komponen Rem Depan & Belakang", 
            description: `
                <p>Paket lengkap komponen sistem pengereman untuk mobil keluarga dan SUV. Menjamin keamanan dan performa pengereman optimal di segala kondisi jalan.</p>
                <ul>
                    <li>Disk brake depan & belakang</li>
                    <li>Kampas rem premium</li>
                    <li>Master rem dan selang hidrolik</li>
                    <li>Kompatibel dengan ABS</li>
                </ul>
            `
        },
        "part3": { 
            name: "Body Fairing Samping Sportbike", 
            description: `
                <p>Panel body fairing samping untuk motor sport, desain aerodinamis dan ringan. Tersedia dalam berbagai pilihan warna dan motif.</p>
                <ul>
                    <li>Bahan ABS plastik berkualitas</li>
                    <li>Finishing glossy anti gores</li>
                    <li>Pemasangan plug & play</li>
                    <li>Sudah termasuk baut dan bracket</li>
                </ul>
            `
        },
        "part4": { 
            name: "Velg Mobil Alloy Klasik (1pcs)", 
            description: `
                <p>Velg mobil alloy dengan desain klasik, cocok untuk mobil retro maupun modern. Tahan karat dan ringan untuk performa berkendara lebih baik.</p>
                <ul>
                    <li>Ukuran: 16 inci</li>
                    <li>Warna: Silver polish</li>
                    <li>Bolt pattern universal</li>
                    <li>Berat hanya 6kg</li>
                </ul>
            `
        },
        "part5": { 
            name: "Set Ban Mobil Radial Performance (4pcs)", 
            description: `
                <p>Satu set (4 buah) ban mobil radial dengan grip maksimal untuk segala cuaca. Cocok untuk penggunaan harian maupun touring jarak jauh.</p>
                <ul>
                    <li>Ukuran: 205/55 R16</li>
                    <li>Indeks kecepatan: H (210 km/jam)</li>
                    <li>Teknologi anti-aquaplaning</li>
                    <li>Garansi 2 tahun</li>
                </ul>
            `
        },
        "part6": { 
            name: "Filter Oli Mesin Mobil (3pcs Pack)", 
            description: `
                <p>Paket berisi 3 buah filter oli mesin, kompatibel untuk berbagai tipe mobil Jepang dan Korea. Menjaga kebersihan oli dan memperpanjang usia mesin.</p>
                <ul>
                    <li>Efisiensi filtrasi hingga 99%</li>
                    <li>Bahan filter serat mikro</li>
                    <li>Mudah dipasang tanpa alat khusus</li>
                    <li>Kemasan ekonomis</li>
                </ul>
            `
        }
    };

    const openDetailsModal = (productId, nameFromButton, priceRupiahString, reviews, imgSrc) => {
        // priceRupiahString bisa jadi "Rp 7.500.000" atau hanya "7500000" dari data-price
        const priceRupiahNumeric = parseRupiahString(priceRupiahString); 

        const details = productDetailsData[productId] || { description: "<p>Detail belum tersedia.</p>", name: nameFromButton };
        let modalBuyNowBtn = document.getElementById('modal-buy-now-btn'); 

        if (modalTitle) modalTitle.textContent = details.name; 
        if (modalImg) { modalImg.src = imgSrc; modalImg.alt = details.name; }
        if (modalPrice) modalPrice.textContent = formatToRupiahDisplay(priceRupiahNumeric); 
        if (modalReviews) modalReviews.textContent = reviews;
        if (modalDescription) modalDescription.innerHTML = details.description;

        if (modalBuyNowBtn) {
            modalBuyNowBtn.dataset.name = nameFromButton; 
            modalBuyNowBtn.dataset.price = priceRupiahNumeric; // Simpan harga Rupiah numerik
            modalBuyNowBtn.dataset.img = imgSrc; 

            const newModalBuyNowBtn = modalBuyNowBtn.cloneNode(true);
            modalBuyNowBtn.parentNode.replaceChild(newModalBuyNowBtn, modalBuyNowBtn);
            modalBuyNowBtn = newModalBuyNowBtn; 
            
            modalBuyNowBtn.addEventListener('click', (e) => {
                e.preventDefault();
                addItemToCart(modalBuyNowBtn.dataset.name, modalBuyNowBtn.dataset.price, modalBuyNowBtn.dataset.img);
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
                // Asumsi data-price di HTML sekarang adalah nilai numerik Rupiah atau string yang bisa diparsing
                const priceRupiahString = button.dataset.price; 
                const reviews = button.dataset.reviews;
                const imgSrc = button.dataset.img;
                openDetailsModal(productId, name, priceRupiahString, reviews, imgSrc);
            });
        });
    }

    if (closeModalBtn && detailsModal) closeModalBtn.onclick = function() { detailsModal.style.display = "none"; }
    if (detailsModal) window.onclick = function(event) { if (event.target == detailsModal) detailsModal.style.display = "none"; }
    
    renderCart(); 
});