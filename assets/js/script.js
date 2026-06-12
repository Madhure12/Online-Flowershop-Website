// 1. Copy Product Link (Share Button)
function copyLink(productId) {
    const baseUrl = window.location.origin + window.location.pathname;
    const shareUrl = `${baseUrl}?product=${productId}`;
    navigator.clipboard.writeText(shareUrl).then(() => {
        alert('Product link copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        prompt('Copy this link:', shareUrl);
    });
}

// 2. Checkout Page - Delivery & Payment Toggle
function showCal() {
    document.getElementById('cal').style.display = 'block';
}
function hideCal() {
    document.getElementById('cal').style.display = 'none';
}
function showTran() {
    document.getElementById('tran').style.display = 'block';
}
function hideTran() {
    document.getElementById('tran').style.display = 'none';
}

// 3. Mobile Menu Toggle (if needed)
document.addEventListener('DOMContentLoaded', function () {
    const toggler = document.getElementById('toggler');
    const navbar = document.querySelector('header .navbar');

    if (toggler) {
        toggler.addEventListener('change', function () {
            if (this.checked) {
                navbar.style.clipPath = 'polygon(0 0, 100% 0, 100% 100%, 0% 100%)';
            } else {
                navbar.style.clipPath = 'polygon(0 0, 100% 0, 100% 0, 0 0)';
            }
        });
    }
});

// 4. Add to Cart with Quantity 
function addToCart(productId, qtyInput) {
    const qty = qtyInput.value;
    if (qty < 1) {
        alert('Please select at least 1 item.');
        return;
    }

    fetch('products.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `add_cart=1&pid=${productId}&qty=${qty}`
    })
    .then(response => response.text())
    .then(() => {
        alert('Added to cart!');
        location.reload(); // Simple reload
    })
    .catch(err => console.error(err));
}

// 5. Wishlist Heart Toggle (AJAX)
function toggleWishlist(productId, heartIcon) {
    fetch(`?wishlist=${productId}`, { method: 'GET' })
    .then(() => {
        heartIcon.style.color = heartIcon.style.color === 'red' ? '#e84393' : 'red';
        alert('Wishlist updated!');
    });
}