document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtn = document.querySelector('.filter-btn');
    const carCards = document.querySelectorAll('.car-card');
    
    filterBtn.addEventListener('click', function() {
        const carType = document.getElementById('carType').value;
        const priceRange = document.getElementById('priceRange').value;
        const transmission = document.getElementById('transmission').value;
        
        carCards.forEach(card => {
            const cardType = card.dataset.type;
            const cardPrice = card.dataset.price;
            const cardTransmission = card.dataset.transmission;
            
            const typeMatch = carType === 'all' || cardType === carType;
            const priceMatch = priceRange === 'all' || cardPrice === priceRange;
            const transmissionMatch = transmission === 'all' || cardTransmission === transmission;
            
            if (typeMatch && priceMatch && transmissionMatch) {
                card.style.display = 'block';
                card.classList.add('fade-in');
            } else {
                card.style.display = 'none';
                card.classList.remove('fade-in');
            }
        });
    });
    
    // Booking modal functionality
    const modal = document.getElementById('bookingModal');
    const closeModal = document.querySelector('.close-modal');
    const bookingForm = document.getElementById('bookingForm');
    
    // Set minimum date for pickup and return
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('pickupDate').min = today;
    document.getElementById('returnDate').min = today;
    
    // Update return date minimum when pickup date changes
    document.getElementById('pickupDate').addEventListener('change', function() {
        document.getElementById('returnDate').min = this.value;
    });
    
    function bookCar(carName) {
        document.getElementById('carName').value = carName;
        modal.classList.add('show');
    }
    
    closeModal.addEventListener('click', function() {
        modal.classList.remove('show');
    });
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });
    
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(bookingForm);
        const bookingData = {};
        formData.forEach((value, key) => {
            bookingData[key] = value;
        });
        
        // Here you would typically send the booking data to your server
        console.log('Booking submitted:', bookingData);
        
        // Show success message
        showNotification('Booking submitted successfully! We\'ll contact you shortly.', 'success');
        
        // Close modal and reset form
        modal.classList.remove('show');
        bookingForm.reset();
    });
    
    // Notification system
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
    
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
    
    // Observe car cards
    carCards.forEach(card => {
        observer.observe(card);
    });
    
    // Add smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
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
}); 