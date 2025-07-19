// Cake Order JavaScript - siparis.js

document.addEventListener('DOMContentLoaded', function() {
    initializeCakeOrder();
});

function initializeCakeOrder() {
    // Set minimum delivery date to tomorrow
    const deliveryDateInput = document.getElementById('delivery-date');
    if (deliveryDateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        deliveryDateInput.min = tomorrow.toISOString().split('T')[0];
    }

    // Check for pre-selected product
    const selectedProduct = localStorage.getItem('selectedProduct');
    if (selectedProduct) {
        selectCategoryByProduct(selectedProduct);
        localStorage.removeItem('selectedProduct');
    }

    // Initialize form handlers
    const orderForm = document.getElementById('cake-order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', handleOrderSubmit);
    }

    // Initialize price calculation
    calculatePrice();
}

function selectCategory(category) {
    // Remove previous selections
    const cards = document.querySelectorAll('.category-card');
    cards.forEach(card => card.classList.remove('selected'));

    // Select current category
    event.target.closest('.category-card').classList.add('selected');

    // Update form
    const categorySelect = document.getElementById('cake-category');
    if (categorySelect) {
        categorySelect.value = category;
        calculatePrice();
    }

    // Scroll to form
    document.getElementById('order-form-section').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

function selectCategoryByProduct(productName) {
    const categoryMap = {
        'Doğum Günü Pastası': 'birthday',
        'Düğün Pastası': 'wedding',
        'Özel Gün Pastası': 'special'
    };

    const category = categoryMap[productName];
    if (category) {
        // Find and select the category card
        const cards = document.querySelectorAll('.category-card');
        cards.forEach(card => {
            if (card.onclick.toString().includes(category)) {
                card.classList.add('selected');
            }
        });

        // Update form
        const categorySelect = document.getElementById('cake-category');
        if (categorySelect) {
            categorySelect.value = category;
            calculatePrice();
        }

        // Scroll to form
        setTimeout(() => {
            document.getElementById('order-form-section').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }, 500);
    }
}

function calculatePrice() {
    const category = document.getElementById('cake-category')?.value;
    const size = document.getElementById('cake-size')?.value;
    const layers = document.getElementById('cake-layers')?.value;
    const photoPrint = document.getElementById('photo-print')?.checked;
    const delivery = document.getElementById('delivery-service')?.checked;

    // Base prices by category
    const basePrices = {
        'birthday': 150,
        'wedding': 500,
        'special': 200,
        'corporate': 300
    };

    // Size multipliers
    const sizeMultipliers = {
        'small': 1,
        'medium': 1.5,
        'large': 2,
        'xlarge': 3
    };

    // Layer multipliers
    const layerMultipliers = {
        '1': 1,
        '2': 1.3,
        '3': 1.6,
        '4': 2
    };

    // Calculate base price
    let basePrice = basePrices[category] || 0;
    let sizeMultiplier = sizeMultipliers[size] || 1;
    let layerMultiplier = layerMultipliers[layers] || 1;

    // Calculate extras
    let extraServices = 0;
    if (photoPrint) extraServices += 50;
    if (delivery) extraServices += 30;

    // Total calculation
    let totalPrice = (basePrice * sizeMultiplier * layerMultiplier) + extraServices;

    // Update display
    document.getElementById('base-price').textContent = `₺${basePrice}`;
    document.getElementById('size-multiplier').textContent = `x${(sizeMultiplier * layerMultiplier).toFixed(1)}`;
    document.getElementById('extra-services').textContent = `₺${extraServices}`;
    document.getElementById('total-price').textContent = `₺${totalPrice.toFixed(0)}`;
}

function validateForm(formData) {
    const errors = [];

    // Required fields validation
    if (!formData.customerName.trim()) {
        errors.push('Müşteri adı gerekli');
    }

    if (!formData.customerPhone.trim()) {
        errors.push('Telefon numarası gerekli');
    } else if (!window.firinPastane.validatePhone(formData.customerPhone)) {
        errors.push('Geçersiz telefon numarası');
    }

    if (formData.customerEmail && !window.firinPastane.validateEmail(formData.customerEmail)) {
        errors.push('Geçersiz e-posta adresi');
    }

    if (!formData.deliveryDate) {
        errors.push('Teslim tarihi gerekli');
    } else {
        const deliveryDate = new Date(formData.deliveryDate);
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        if (deliveryDate < tomorrow) {
            errors.push('Teslim tarihi en az yarın olmalı');
        }
    }

    if (!formData.cakeCategory) {
        errors.push('Pasta kategorisi seçilmeli');
    }

    if (!formData.cakeSize) {
        errors.push('Pasta boyutu seçilmeli');
    }

    if (!formData.cakeFlavor) {
        errors.push('Pasta tadı seçilmeli');
    }

    return errors;
}

function handleOrderSubmit(event) {
    event.preventDefault();
    
    window.firinPastane.showLoading();

    // Get form data
    const formData = new FormData(event.target);
    const orderData = Object.fromEntries(formData.entries());

    // Add checkboxes manually (FormData doesn't include unchecked checkboxes)
    orderData.photoPrint = document.getElementById('photo-print').checked;
    orderData.deliveryService = document.getElementById('delivery-service').checked;

    // Validate form
    const validationErrors = validateForm(orderData);
    
    if (validationErrors.length > 0) {
        window.firinPastane.hideLoading();
        window.firinPastane.showErrorMessage(validationErrors.join(', '));
        return;
    }

    // Calculate final price
    const totalPriceText = document.getElementById('total-price').textContent;
    const totalPrice = parseFloat(totalPriceText.replace('₺', ''));

    // Create order object
    const order = {
        type: 'cake',
        customerInfo: {
            name: orderData.customerName,
            phone: orderData.customerPhone,
            email: orderData.customerEmail || ''
        },
        cakeDetails: {
            category: orderData.cakeCategory,
            size: orderData.cakeSize,
            flavor: orderData.cakeFlavor,
            layers: orderData.cakeLayers,
            message: orderData.cakeMessage || '',
            colors: orderData.cakeColors || '',
            specialRequests: orderData.specialRequests || ''
        },
        extras: {
            photoPrint: orderData.photoPrint,
            deliveryService: orderData.deliveryService
        },
        deliveryDate: orderData.deliveryDate,
        totalPrice: totalPrice,
        status: 'pending',
        orderDate: new Date().toISOString()
    };

    // Simulate order processing
    setTimeout(() => {
        try {
            // Save order
            const orderId = window.firinPastane.saveOrder(order);
            
            // Hide form and show success
            document.getElementById('cake-order-form').style.display = 'none';
            const successDiv = document.getElementById('order-success');
            successDiv.classList.remove('hidden');
            document.getElementById('order-number').textContent = orderId;

            // Hide loading
            window.firinPastane.hideLoading();

            // Show success message
            window.firinPastane.showSuccessMessage('Pasta siparişiniz başarıyla alındı!');

            // Send email notification (simulation)
            sendOrderNotification(order, orderId);

        } catch (error) {
            window.firinPastane.hideLoading();
            window.firinPastane.showErrorMessage('Sipariş gönderilirken bir hata oluştu. Lütfen tekrar deneyin.');
            console.error('Order submission error:', error);
        }
    }, 2000);
}

function sendOrderNotification(order, orderId) {
    // Simulate sending email notification
    console.log('Order notification sent:', {
        orderId: orderId,
        customer: order.customerInfo.name,
        phone: order.customerInfo.phone,
        email: order.customerInfo.email,
        deliveryDate: order.deliveryDate,
        totalPrice: order.totalPrice
    });

    // In a real application, this would make an API call to send email
    // Example: fetch('/api/send-order-notification', { method: 'POST', body: JSON.stringify(order) })
}

function resetForm() {
    if (confirm('Formu temizlemek istediğinizden emin misiniz?')) {
        document.getElementById('cake-order-form').reset();
        
        // Remove category selections
        const cards = document.querySelectorAll('.category-card');
        cards.forEach(card => card.classList.remove('selected'));
        
        // Reset price calculation
        calculatePrice();
        
        window.firinPastane.showSuccessMessage('Form temizlendi');
    }
}

function createNewOrder() {
    // Reset form and show it again
    document.getElementById('order-success').classList.add('hidden');
    document.getElementById('cake-order-form').style.display = 'block';
    
    // Reset form
    document.getElementById('cake-order-form').reset();
    
    // Remove category selections
    const cards = document.querySelectorAll('.category-card');
    cards.forEach(card => card.classList.remove('selected'));
    
    // Reset price calculation
    calculatePrice();
    
    // Scroll to top
    document.getElementById('order-form-section').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
}

// Advanced form features
function addImageUpload() {
    // Future feature: Allow customers to upload reference images
    console.log('Image upload feature - to be implemented');
}

function showPriceDetails() {
    // Show detailed price breakdown
    const modal = createPriceModal();
    document.body.appendChild(modal);
}

function createPriceModal() {
    const modal = document.createElement('div');
    modal.className = 'price-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Fiyat Detayları</h3>
                <button class="modal-close" onclick="this.closest('.price-modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                <h4>Temel Fiyatlar:</h4>
                <ul>
                    <li>Doğum Günü Pastası: ₺150 - ₺500</li>
                    <li>Düğün Pastası: ₺500 - ₺2000</li>
                    <li>Özel Gün Pastası: ₺200 - ₺800</li>
                    <li>Kurumsal Pasta: ₺300 - ₺1000</li>
                </ul>
                
                <h4>Boyut Çarpanları:</h4>
                <ul>
                    <li>Küçük (6-8 kişi): x1</li>
                    <li>Orta (10-12 kişi): x1.5</li>
                    <li>Büyük (15-20 kişi): x2</li>
                    <li>Çok Büyük (25+ kişi): x3</li>
                </ul>
                
                <h4>Ek Hizmetler:</h4>
                <ul>
                    <li>Fotoğraf Baskısı: +₺50</li>
                    <li>Teslimat Hizmeti: +₺30</li>
                    <li>2 Kat: x1.3</li>
                    <li>3 Kat: x1.6</li>
                    <li>4+ Kat: x2</li>
                </ul>
                
                <p><strong>Not:</strong> Nihai fiyat tasarım karmaşıklığına göre değişebilir.</p>
            </div>
        </div>
    `;

    // Add modal styles
    const style = document.createElement('style');
    style.textContent = `
        .price-modal {
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
        }
        .price-modal .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .price-modal .modal-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        .price-modal .modal-close {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
        }
        .price-modal .modal-body {
            padding: 2rem;
        }
        .price-modal h4 {
            color: var(--primary-color);
            margin: 1.5rem 0 1rem 0;
        }
        .price-modal ul {
            margin-bottom: 1rem;
        }
        .price-modal li {
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }
    `;
    document.head.appendChild(style);

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
            document.head.removeChild(style);
        }
    });

    return modal;
}

// Export functions for global access
window.cakeOrder = {
    selectCategory,
    calculatePrice,
    resetForm,
    createNewOrder,
    showPriceDetails
};