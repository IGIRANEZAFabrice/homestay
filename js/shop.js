document.addEventListener('DOMContentLoaded', function() {
    // Product data
    const products = {
        basket: {
            name: 'Traditional Basket',
            price: 35,
            originalPrice: 45,
            image: '../img/shop/products/basket.jpg',
            description: 'Hand-woven basket made from natural fibers',
            rating: 4.5,
            reviews: 24
        },
        kitenge: {
            name: 'Kitenge Fabric',
            price: 15,
            image: '../img/shop/products/kitenge.jpg',
            description: 'Colorful traditional fabric, perfect for clothing',
            rating: 4,
            reviews: 18
        },
        necklace: {
            name: 'Traditional Necklace',
            price: 65,
            image: '../img/shop/products/necklace.jpg',
            description: 'Handcrafted bead necklace with traditional patterns',
            rating: 5,
            reviews: 32
        },
        painting: {
            name: 'Traditional Painting',
            price: 45,
            originalPrice: 60,
            image: '../img/shop/products/painting.jpg',
            description: 'Hand-painted scene of Rwandan village life',
            rating: 4,
            reviews: 15
        }
    };

    // Shopping cart
    let cart = [];

    // DOM Elements
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');
    const sortBy = document.getElementById('sortBy');
    const productsGrid = document.querySelector('.products-grid');
    const quickViewModal = document.getElementById('quickViewModal');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartItems = document.querySelector('.cart-items');
    const totalAmount = document.querySelector('.total-amount');

    // Filter functionality
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        const price = priceFilter.value;
        const sort = sortBy.value;

        const filteredProducts = Array.from(productsGrid.children).filter(card => {
            const productName = card.querySelector('h3').textContent.toLowerCase();
            const productCategory = card.dataset.category;
            const productPrice = card.dataset.price;

            const matchesSearch = productName.includes(searchTerm);
            const matchesCategory = category === 'all' || productCategory === category;
            const matchesPrice = price === 'all' || productPrice === price;

            return matchesSearch && matchesCategory && matchesPrice;
        });

        // Sort products
        filteredProducts.sort((a, b) => {
            const priceA = parseFloat(a.querySelector('.price').textContent.replace('$', ''));
            const priceB = parseFloat(b.querySelector('.price').textContent.replace('$', ''));

            switch (sort) {
                case 'price-low':
                    return priceA - priceB;
                case 'price-high':
                    return priceB - priceA;
                default:
                    return 0;
            }
        });

        // Update display
        productsGrid.innerHTML = '';
        filteredProducts.forEach(card => {
            productsGrid.appendChild(card);
            card.classList.add('fade-in');
        });
    }

    // Event listeners for filters
    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);
    priceFilter.addEventListener('change', filterProducts);
    sortBy.addEventListener('change', filterProducts);

    // Quick view functionality
    function quickView(productId) {
        const product = products[productId];
        const modal = quickViewModal;
        const content = modal.querySelector('.quick-view-content');

        content.querySelector('img').src = product.image;
        content.querySelector('h2').textContent = product.name;
        content.querySelector('.product-description').textContent = product.description;
        content.querySelector('.price').textContent = `$${product.price}`;
        
        if (product.originalPrice) {
            content.querySelector('.original-price').textContent = `$${product.originalPrice}`;
            content.querySelector('.original-price').style.display = 'inline';
        } else {
            content.querySelector('.original-price').style.display = 'none';
        }

        // Set rating
        const rating = content.querySelector('.product-rating');
        rating.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('i');
            star.className = i <= Math.floor(product.rating) ? 'fas fa-star' :
                            i === Math.ceil(product.rating) ? 'fas fa-star-half-alt' :
                            'far fa-star';
            rating.appendChild(star);
        }
        rating.innerHTML += ` (${product.reviews})`;

        modal.classList.add('show');
    }

    // Close modal
    document.querySelector('.close-modal').addEventListener('click', () => {
        quickViewModal.classList.remove('show');
    });

    window.addEventListener('click', (e) => {
        if (e.target === quickViewModal) {
            quickViewModal.classList.remove('show');
        }
    });

    // Cart functionality
    function addToCart(productId) {
        const product = products[productId];
        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                id: productId,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1
            });
        }

        updateCart();
        showCart();
    }

    function updateCart() {
        cartItems.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>$${item.price} x ${item.quantity}</p>
                </div>
                <div class="cart-item-actions">
                    <button class="quantity-btn minus" onclick="updateQuantity('${item.id}', -1)">-</button>
                    <span>${item.quantity}</span>
                    <button class="quantity-btn plus" onclick="updateQuantity('${item.id}', 1)">+</button>
                    <button class="remove-btn" onclick="removeFromCart('${item.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            cartItems.appendChild(cartItem);
        });

        totalAmount.textContent = `$${total.toFixed(2)}`;
    }

    function updateQuantity(productId, change) {
        const item = cart.find(item => item.id === productId);
        if (item) {
            item.quantity = Math.max(1, item.quantity + change);
            updateCart();
        }
    }

    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        updateCart();
    }

    function showCart() {
        cartSidebar.classList.add('show');
        cartOverlay.classList.add('show');
    }

    function hideCart() {
        cartSidebar.classList.remove('show');
        cartOverlay.classList.remove('show');
    }

    // Event listeners for cart
    document.querySelector('.close-cart').addEventListener('click', hideCart);
    cartOverlay.addEventListener('click', hideCart);

    // Checkout functionality
    document.querySelector('.checkout-btn').addEventListener('click', function() {
        if (cart.length === 0) {
            showNotification('Your cart is empty', 'error');
            return;
        }

        // Here you would typically redirect to a checkout page
        console.log('Proceeding to checkout with items:', cart);
        showNotification('Proceeding to checkout...', 'success');
    });

    // Notification system
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Make functions globally available
    window.quickView = quickView;
    window.addToCart = addToCart;
    window.updateQuantity = updateQuantity;
    window.removeFromCart = removeFromCart;

    // Intersection Observer for fade-in animations
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe product cards
    document.querySelectorAll('.product-card').forEach(card => {
        observer.observe(card);
    });
}); 