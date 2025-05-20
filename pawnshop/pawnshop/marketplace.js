// DOM Elements
document.addEventListener('DOMContentLoaded', function() {
    // Wishlist functionality
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.style.color = '#e76f51';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.style.color = '#e9c46a';
            }
        });
    });

    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            const productPrice = productCard.querySelector('.price').textContent;
            
            // Animation effect
            this.textContent = 'Added!';
            this.style.backgroundColor = '#4CAF50';
            this.style.color = 'white';
            
            // Reset button after 2 seconds
            setTimeout(() => {
                this.textContent = 'Add to Cart';
                this.style.backgroundColor = '#e9c46a';
                this.style.color = '#333';
            }, 2000);
            
            // Add to cart logic - normally would update a cart object and maybe send to server
            updateCart(productName, productPrice);
        });
    });

    // Filter functionality
    const categoryFilters = document.querySelectorAll('input[name="category"]');
    categoryFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            filterProducts(this.value);
        });
    });

    // Material filter
    const materialFilters = document.querySelectorAll('input[name="material"]');
    materialFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            filterByMaterial(this.value);
        });
    });

    // Sort functionality
    const sortSelect = document.getElementById('sort');
    sortSelect.addEventListener('change', function() {
        sortProducts(this.value);
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchProducts(this.value);
            }
        });
    });

    // Pagination
    const prevPageBtn = document.querySelector('.prev-page');
    const nextPageBtn = document.querySelector('.next-page');
    let currentPage = 1;
    
    prevPageBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            updatePageIndicator();
            loadPage(currentPage);
        }
    });
    
    nextPageBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const totalPages = 2; // In a real app, this would be dynamic
        if (currentPage < totalPages) {
            currentPage++;
            updatePageIndicator();
            loadPage(currentPage);
        }
    });
});

// Helper functions
function updateCart(productName, productPrice) {
    // This would normally update a cart object and possibly store in localStorage or send to server
    console.log(`Added to cart: ${productName} - ${productPrice}`);
    
    // You could update a cart counter in the UI
    const cartIcon = document.querySelector('.cart-icon');
    const cartCounter = document.createElement('span');
    cartCounter.classList.add('cart-counter');
    cartCounter.style.backgroundColor = 'red';
    cartCounter.style.color = 'white';
    cartCounter.style.borderRadius = '50%';
    cartCounter.style.padding = '2px 6px';
    cartCounter.style.fontSize = '0.7rem';
    cartCounter.style.position = 'absolute';
    cartCounter.style.marginLeft = '-10px';
    cartCounter.style.marginTop = '-10px';
    
    // Check if counter already exists
    const existingCounter = document.querySelector('.cart-counter');
    if (existingCounter) {
        const currentCount = parseInt(existingCounter.textContent);
        existingCounter.textContent = currentCount + 1;
    } else {
        cartCounter.textContent = '1';
        cartIcon.appendChild(cartCounter);
    }
}

function filterProducts(category) {
    const productCards = document.querySelectorAll('.product-card');
    
    if (category === 'all') {
        productCards.forEach(card => {
            card.style.display = 'block';
        });
        return;
    }
    
    productCards.forEach(card => {
        const productName = card.querySelector('h3').textContent.toLowerCase();
        if (productName.includes(category.toLowerCase())) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterByMaterial(material) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const productName = card.querySelector('h3').textContent.toLowerCase();
        if (productName.includes(material.toLowerCase().replace('-', ' '))) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function sortProducts(sortOption) {
    const productsContainer = document.querySelector('.products-container');
    const productCards = Array.from(document.querySelectorAll('.product-card'));
    
    switch(sortOption) {
        case 'price-low-high':
            productCards.sort((a, b) => {
                const priceA = parseFloat(a.querySelector('.price').textContent.replace('₱', '').replace(',', ''));
                const priceB = parseFloat(b.querySelector('.price').textContent.replace('₱', '').replace(',', ''));
                return priceA - priceB;
            });
            break;
        case 'price-high-low':
            productCards.sort((a, b) => {
                const priceA = parseFloat(a.querySelector('.price').textContent.replace('₱', '').replace(',', ''));
                const priceB = parseFloat(b.querySelector('.price').textContent.replace('₱', '').replace(',', ''));
                return priceB - priceA;
            });
            break;
        // Additional sort options could be added here
    }
    
    // Clear and re-add sorted products
    productsContainer.innerHTML = '';
    productCards.forEach(card => {
        productsContainer.appendChild(card);
    });
}

function searchProducts(query) {
    query = query.toLowerCase().trim();
    const productCards = document.querySelectorAll('.product-card');
    
    if (query === '') {
        productCards.forEach(card => {
            card.style.display = 'block';
        });
        return;
    }
    
    productCards.forEach(card => {
        const productName = card.querySelector('h3').textContent.toLowerCase();
        const productInfo = card.querySelector('.product-info').textContent.toLowerCase();
        
        if (productName.includes(query) || productInfo.includes(query)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function updatePageIndicator() {
    const pageIndicator = document.querySelector('.page-indicator');
    pageIndicator.textContent = `${currentPage}/2`; // In a real app, total pages would be dynamic
}

function loadPage(pageNumber) {
    // In a real application, this would fetch products for the specific page from the server
    console.log(`Loading page ${pageNumber}`);
    
    // For demo purposes, just toggle visibility of current products
    // In a real app, you'd replace them with new data from an API
    const productCards = document.querySelectorAll('.product-card');
    
    if (pageNumber === 1) {
        // Show first page products, hide others
        productCards.forEach((card, index) => {
            if (index < 6) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    } else if (pageNumber === 2) {
        // In a real app, these would be different products
        // For demo, we'll just pretend the same products are on page 2
        productCards.forEach(card => {
            card.style.display = 'block';
        });
    }
}