// Wait for DOM content to be fully loaded before executing code
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            
            // Change icon based on menu state
            const icon = this.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (navLinks && navLinks.classList.contains('active') && 
            !event.target.closest('.nav-links') && 
            !event.target.closest('.mobile-menu-btn')) {
            navLinks.classList.remove('active');
            
            // Reset the icon
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });

    // Sticky Header on Scroll
    const header = document.querySelector('header');
    const heroSection = document.querySelector('.hero');
    
    if (header && heroSection) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.style.background = '#fff';
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = 'none';
            }
        });
    }

    // Active Navigation Link on Scroll
    const sections = document.querySelectorAll('section[id]');
    const navItems = document.querySelectorAll('.nav-links a');
    
    function highlightNavOnScroll() {
        const scrollY = window.pageYOffset;
        
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navItems.forEach(item => {
                    item.classList.remove('active');
                    if (item.getAttribute('href') === `#${sectionId}`) {
                        item.classList.add('active');
                    }
                });
            }
        });
    }
    
    // Call this function on scroll
    window.addEventListener('scroll', highlightNavOnScroll);

    // Back to Top Button
    const backToTopBtn = document.querySelector('.back-to-top');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                backToTopBtn.classList.add('active');
            } else {
                backToTopBtn.classList.remove('active');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Form Submission Handling
    const contactForm = document.getElementById('contactForm');
    const formStatus = document.getElementById('form-status');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio normal do formulário
            
            // Form validation
            const requiredFields = ['name', 'email', 'phone', 'subject', 'message'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value || input.value.trim() === '') {
                    input.style.borderColor = 'red';
                    isValid = false;
                } else {
                    input.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                showNotification('Por favor, preencha todos os campos obrigatórios', 'error');
                return;
            }
            
            // Mostrar mensagem de carregamento
            formStatus.innerHTML = '<div class="loading">Enviando mensagem...</div>';
            formStatus.style.display = 'block';
            
            // Obter dados do formulário
            const formData = new FormData(contactForm);
            
            // Enviar via fetch API
            fetch(contactForm.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    formStatus.innerHTML = '<div class="success">' + data.message + '</div>';
                    contactForm.reset();
                    showNotification(data.message, 'success');
                } else {
                    formStatus.innerHTML = '<div class="error">' + data.message + '</div>';
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                formStatus.innerHTML = '<div class="error">Erro ao enviar formulário. Por favor, tente novamente.</div>';
                showNotification('Erro ao enviar formulário. Por favor, tente novamente.', 'error');
                console.error('Erro:', error);
            });
        });
    }

    // Newsletter Form Handling
    const newsletterForm = document.getElementById('newsletterForm');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const emailInput = newsletterForm.querySelector('input[type="email"]');
            const emailValue = emailInput.value.trim();
            
            if (!emailValue || !isValidEmail(emailValue)) {
                emailInput.style.borderColor = 'red';
                showNotification('Please enter a valid email address', 'error');
                return;
            }
            
            // Simulate form submission
            showNotification('Thank you for subscribing to our newsletter!', 'success');
            newsletterForm.reset();
        });
    }

    // Facility Gallery Image Zoom Effect
    const facilityImages = document.querySelectorAll('.facility img');
    
    facilityImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(event) {
            const targetId = this.getAttribute('href');
            
            // Skip if it's just "#" or has no target element
            if (targetId === '#' || !document.querySelector(targetId)) return;
            
            event.preventDefault();
            
            // Close mobile menu if it's open
            if (navLinks && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
            
            const targetPosition = document.querySelector(targetId).offsetTop - 80;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        });
    });

    // Animation on Scroll
    // Elements will animate when they come into view
    const animatedElements = document.querySelectorAll(
        '.highlight-cards .card, .about-image, .about-text, ' +
        '.level, .teacher, .facility, .event, ' +
        '.contact-form, .contact-info'
    );
    
    function checkIfInView() {
        const windowHeight = window.innerHeight;
        const windowTopPosition = window.scrollY;
        const windowBottomPosition = windowTopPosition + windowHeight;
        
        animatedElements.forEach(element => {
            const elementHeight = element.offsetHeight;
            const elementTopPosition = element.offsetTop;
            const elementBottomPosition = elementTopPosition + elementHeight;
            
            // Check if element is in viewport
            if (
                elementBottomPosition >= windowTopPosition && 
                elementTopPosition <= windowBottomPosition
            ) {
                element.classList.add('animated');
            }
        });
    }
    
    // Initial check on page load
    window.addEventListener('load', checkIfInView);
    
    // Check on scroll
    window.addEventListener('scroll', checkIfInView);

    // Utility Functions
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        // Append to body
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Hide and remove after 4 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 4000);
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Add CSS for notifications
    const notificationStyle = document.createElement('style');
    notificationStyle.textContent = `
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            transform: translateY(100px);
            opacity: 0;
            transition: transform 0.3s, opacity 0.3s;
            z-index: 9999;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .notification.success {
            background-color: #28a745;
        }
        
        .notification.error {
            background-color: #dc3545;
        }
        
        /* Animation classes */
        .animated {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(notificationStyle);

    // Initialize animations on load
    animatedElements.forEach(element => {
        element.style.opacity = '0';
    });
});
