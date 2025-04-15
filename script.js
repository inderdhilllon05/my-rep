document.addEventListener('DOMContentLoaded', function() {
    // Load featured products
    loadFeaturedProducts();
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

function loadFeaturedProducts() {
    // In a real application, this would fetch from an API or database
    // For demo purposes, we're using static data
    const featuredProducts = [
        {
            id: 1,
            name: 'Custom Wooden Coffee Table',
            description: 'Handcrafted wooden table with customizable dimensions and finish',
            price: 299.99,
            image: 'images/table.jpg'
        },
        {
            id: 2,
            name: 'Personalized Wall Art',
            description: 'Choose from various designs and add your personal touch',
            price: 89.99,
            image: 'images/wall-art.jpg'
        },
        {
            id: 3,
            name: 'Designer Throw Pillows',
            description: 'Premium quality pillows with customizable fabrics',
            price: 39.99,
            image: 'images/pillows.jpg'
        },
        {
            id: 4,
            name: 'Custom Curtains',
            description: 'Tailor-made curtains to fit your windows perfectly',
            price: 129.99,
            image: 'images/curtains.jpg'
        }
    ];
    
    const productGrid = document.getElementById('featuredProducts');
    
    featuredProducts.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <div class="product-img">
                <img src="${product.image}" alt="${product.name}">
            </div>
            <div class="product-info">
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <p class="price">$${product.price.toFixed(2)}</p>
                <a href="customize.php?id=${product.id}" class="btn">Customize</a>
            </div>
        `;
        productGrid.appendChild(productCard);
    });
}