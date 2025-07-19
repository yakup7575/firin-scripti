// Main JavaScript for Fırın Pastane Website

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize Application
function initializeApp() {
    initializeNavigation();
    initializeSearch();
    initializeProductCards();
    initializeLocalStorage();
    loadSavedProducts();
    initializeAnimations();
}

// Navigation Functions
function initializeNavigation() {
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
        });
    });

    // Smooth scrolling for internal links
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 100) {
            navbar.style.background = 'rgba(139, 69, 19, 0.95)';
        } else {
            navbar.style.background = 'linear-gradient(135deg, var(--primary-color), var(--secondary-color))';
        }
    });
}

// Search Functionality
function initializeSearch() {
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    
    if (searchInput && searchBtn) {
        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        // Real-time search
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            if (query.length > 0) {
                filterProducts(query);
            } else {
                showAllProducts();
            }
        });
    }
}

function performSearch() {
    const searchInput = document.getElementById('search-input');
    const query = searchInput.value.toLowerCase().trim();
    
    if (query) {
        filterProducts(query);
        saveSearchHistory(query);
    } else {
        showErrorMessage('Lütfen arama yapmak için bir kelime girin.');
    }
}

function filterProducts(query) {
    const productCards = document.querySelectorAll('.product-card');
    let foundProducts = 0;

    productCards.forEach(card => {
        const productName = card.getAttribute('data-name')?.toLowerCase() || '';
        const productCategory = card.getAttribute('data-category')?.toLowerCase() || '';
        const productTitle = card.querySelector('h4')?.textContent.toLowerCase() || '';
        const productDescription = card.querySelector('.description')?.textContent.toLowerCase() || '';
        
        const matches = productName.includes(query) || 
                       productCategory.includes(query) ||
                       productTitle.includes(query) ||
                       productDescription.includes(query);

        if (matches) {
            card.style.display = 'block';
            card.style.order = '1';
            foundProducts++;
            highlightSearchTerm(card, query);
        } else {
            card.style.display = 'none';
        }
    });

    if (foundProducts === 0) {
        showErrorMessage(`"${query}" için ürün bulunamadı.`);
    } else {
        hideMessages();
    }
}

function showAllProducts() {
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.style.display = 'block';
        card.style.order = '';
        removeHighlight(card);
    });
    hideMessages();
}

function highlightSearchTerm(card, term) {
    const elements = card.querySelectorAll('h4, .description');
    elements.forEach(element => {
        const text = element.textContent;
        const highlightedText = text.replace(new RegExp(term, 'gi'), `<mark>$&</mark>`);
        element.innerHTML = highlightedText;
    });
}

function removeHighlight(card) {
    const marks = card.querySelectorAll('mark');
    marks.forEach(mark => {
        mark.outerHTML = mark.textContent;
    });
}

// Product Cards Animation and Interaction
function initializeProductCards() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        // Add click interaction
        card.addEventListener('click', function() {
            const productName = this.querySelector('h4').textContent;
            const price = this.querySelector('.price').textContent;
            const category = this.getAttribute('data-category');
            
            showProductDetails(productName, price, category);
        });

        // Add hover effect enhancement
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

function showProductDetails(name, price, category) {
    const modal = createModal(name, price, category);
    document.body.appendChild(modal);
    
    // Show modal with animation
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

function createModal(name, price, category) {
    const modal = document.createElement('div');
    modal.className = 'product-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>${name}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="product-image-large">
                    <i class="fas fa-${getIconForCategory(category)} fa-4x"></i>
                </div>
                <p class="modal-price">${price}</p>
                <p class="modal-description">Bu ürün hakkında daha fazla bilgi için mağazamızı ziyaret edin veya bizi arayın.</p>
                ${category === 'pasta' ? `
                    <div class="order-section">
                        <h4>Sipariş Ver</h4>
                        <button class="btn btn-primary" onclick="redirectToOrder('${name}')">Sipariş Formu</button>
                    </div>
                ` : ''}
            </div>
        </div>
    `;

    // Add modal styles
    const style = document.createElement('style');
    style.textContent = `
        .product-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .product-modal.show {
            opacity: 1;
        }
        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        .modal-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: var(--text-light);
        }
        .modal-body {
            padding: 2rem;
            text-align: center;
        }
        .product-image-large {
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }
        .modal-price {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent-color);
            margin: 1rem 0;
        }
        .order-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
    `;
    document.head.appendChild(style);

    // Add close functionality
    const closeBtn = modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', function() {
        modal.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(modal);
            document.head.removeChild(style);
        }, 300);
    });

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeBtn.click();
        }
    });

    return modal;
}

function getIconForCategory(category) {
    switch(category) {
        case 'ekmek': return 'bread-slice';
        case 'simit': return 'circle-notch';
        case 'pasta': return 'birthday-cake';
        default: return 'bread-slice';
    }
}

function redirectToOrder(productName) {
    localStorage.setItem('selectedProduct', productName);
    window.location.href = 'pastalar.html';
}

// Local Storage Functions
function initializeLocalStorage() {
    // Initialize storage objects if they don't exist
    if (!localStorage.getItem('products')) {
        const defaultProducts = {
            ekmekler: [
                { name: 'Beyaz Ekmek', price: '₺3.50', description: 'Günlük taze beyaz ekmek' },
                { name: 'Tam Buğday Ekmeği', price: '₺4.00', description: 'Sağlıklı tam buğday ekmeği' },
                { name: 'Çavdar Ekmeği', price: '₺4.50', description: 'Lezzetli çavdar ekmeği' },
                { name: 'Köy Ekmeği', price: '₺5.00', description: 'Geleneksel köy ekmeği' }
            ],
            simitler: [
                { name: 'Klasik Simit', price: '₺2.50', description: 'Geleneksel İstanbul simidi' },
                { name: 'Susamlı Simit', price: '₺3.00', description: 'Bol susamlı taze simit' },
                { name: 'Çörek Otu Simidi', price: '₺3.50', description: 'Çörek otlu özel simit' },
                { name: 'Açma', price: '₺4.00', description: 'Yumuşak ve lezzetli açma' }
            ],
            pastalar: [
                { name: 'Doğum Günü Pastası', price: '₺150+', description: 'Özel doğum günü pastaları' },
                { name: 'Düğün Pastası', price: '₺500+', description: 'Muhteşem düğün pastaları' },
                { name: 'Özel Gün Pastası', price: '₺200+', description: 'Özel günler için pastalar' }
            ]
        };
        localStorage.setItem('products', JSON.stringify(defaultProducts));
    }

    if (!localStorage.getItem('orders')) {
        localStorage.setItem('orders', JSON.stringify([]));
    }

    if (!localStorage.getItem('searchHistory')) {
        localStorage.setItem('searchHistory', JSON.stringify([]));
    }
}

function loadSavedProducts() {
    // This function can be used to load dynamically added products
    // For now, we'll use the static HTML content
}

function saveSearchHistory(query) {
    const history = JSON.parse(localStorage.getItem('searchHistory')) || [];
    if (!history.includes(query)) {
        history.unshift(query);
        if (history.length > 10) {
            history.pop();
        }
        localStorage.setItem('searchHistory', JSON.stringify(history));
    }
}

// Animation Functions
function initializeAnimations() {
    // Fade in animation for elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe product cards and sections
    const animatedElements = document.querySelectorAll('.product-card, .contact-item, .product-category');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Utility Functions
function showLoading() {
    const loading = document.querySelector('.loading');
    if (loading) {
        loading.style.display = 'flex';
    }
}

function hideLoading() {
    const loading = document.querySelector('.loading');
    if (loading) {
        loading.style.display = 'none';
    }
}

function showErrorMessage(message) {
    hideMessages();
    let errorDiv = document.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        const searchSection = document.querySelector('.search-section');
        searchSection.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    
    // Auto hide after 5 seconds
    setTimeout(hideMessages, 5000);
}

function showSuccessMessage(message) {
    hideMessages();
    let successDiv = document.querySelector('.success-message');
    if (!successDiv) {
        successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        const searchSection = document.querySelector('.search-section');
        searchSection.appendChild(successDiv);
    }
    successDiv.textContent = message;
    successDiv.style.display = 'block';
    
    // Auto hide after 3 seconds
    setTimeout(hideMessages, 3000);
}

function hideMessages() {
    const messages = document.querySelectorAll('.error-message, .success-message');
    messages.forEach(msg => {
        msg.style.display = 'none';
    });
}

// Form Validation Utilities
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[\+]?[(]?[\d\s\-\(\)]{10,}$/;
    return phoneRegex.test(phone);
}

function validateRequired(value) {
    return value && value.trim().length > 0;
}

// Export functions for use in other files
window.firinPastane = {
    showLoading,
    hideLoading,
    showErrorMessage,
    showSuccessMessage,
    hideMessages,
    validateEmail,
    validatePhone,
    validateRequired,
    getProducts: () => JSON.parse(localStorage.getItem('products')),
    saveProducts: (products) => localStorage.setItem('products', JSON.stringify(products)),
    getOrders: () => JSON.parse(localStorage.getItem('orders')),
    saveOrder: (order) => {
        const orders = JSON.parse(localStorage.getItem('orders')) || [];
        order.id = Date.now();
        order.date = new Date().toISOString();
        orders.unshift(order);
        localStorage.setItem('orders', JSON.stringify(orders));
        return order.id;
    }
};