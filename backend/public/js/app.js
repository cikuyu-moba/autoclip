/*
 * ARHANA - Client-side Interaction JavaScript
 * Dynamic micro-interactions, mobile menu toggles, alert dismissals, and image upload previews.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Menu Toggle for Public Site
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');

    function closeAllDropdowns() {
        document.querySelectorAll('.nav-item-dropdown.active').forEach(function (item) {
            item.classList.remove('active');
            const toggle = item.querySelector('.dropdown-toggle');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function setMobileToggleIcon(isOpen) {
        if (!mobileToggle) return;
        mobileToggle.innerHTML = isOpen 
            ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`
            : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>`;
    }

    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const willOpen = !navMenu.classList.contains('active');
            navMenu.classList.toggle('active', willOpen);
            setMobileToggleIcon(willOpen);

            if (!willOpen) {
                closeAllDropdowns();
            }
        });

        // Close public mobile nav if clicking outside
        document.addEventListener('click', function (event) {
            if (navMenu.classList.contains('active')) {
                const isClickInsideNav = navMenu.contains(event.target);
                const isClickInsideToggle = mobileToggle.contains(event.target) || (event.target.closest && event.target.closest('#mobileToggle'));
                
                if (!isClickInsideNav && !isClickInsideToggle) {
                    navMenu.classList.remove('active');
                    setMobileToggleIcon(false);
                    closeAllDropdowns();
                }
            }
        });
    }

    // 2. Submenu Dropdown Click Handler (for Mobile & Touch / Click)
    const dropdownToggles = document.querySelectorAll('.nav-item-dropdown .dropdown-toggle');
    dropdownToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const parentDropdown = toggle.closest('.nav-item-dropdown');
            if (!parentDropdown) return;

            const isCurrentlyOpen = parentDropdown.classList.contains('active');

            // Close other open dropdowns
            document.querySelectorAll('.nav-item-dropdown.active').forEach(function (item) {
                if (item !== parentDropdown) {
                    item.classList.remove('active');
                    const otherToggle = item.querySelector('.dropdown-toggle');
                    if (otherToggle) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            // Toggle current dropdown
            if (isCurrentlyOpen) {
                parentDropdown.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                parentDropdown.classList.add('active');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // Close open dropdowns when clicking outside any dropdown
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-item-dropdown')) {
            closeAllDropdowns();
        }
    });

    // 3. Mobile Menu Toggle for Admin Panel
    const adminNavbarToggle = document.getElementById('adminNavbarToggle');
    const adminSidebar = document.getElementById('adminSidebar');

    if (adminNavbarToggle && adminSidebar) {
        adminNavbarToggle.addEventListener('click', function () {
            adminSidebar.classList.toggle('active');
        });
        
        // Close sidebar if clicking outside on mobile
        document.addEventListener('click', function (event) {
            const isClickInsideSidebar = adminSidebar.contains(event.target);
            const isClickInsideToggle = adminNavbarToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickInsideToggle && adminSidebar.classList.contains('active')) {
                adminSidebar.classList.remove('active');
            }
        });
    }

    // 4. Auto-Dismiss Alert Success/Error Messages
    const alerts = document.querySelectorAll('.alert-admin, .toast-alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(10px)';
            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 4000);
    });

    // 5. Client-side Active Menu Nav Highlighter
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link:not(.dropdown-toggle)');
    
    navLinks.forEach(function (link) {
        const href = link.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('javascript:')) {
            if (href === currentPath || (currentPath.startsWith(href) && href !== '/')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        }
    });
});

/**
 * 5. Dynamic Image Upload Live Preview
 * Instantly previews the selected image in the specified <img> element.
 * @param {HTMLInputElement} inputEl The input file element
 * @param {string} previewImgId The ID of the image element to update
 * @param {string} placeholderIconId The ID of the placeholder icon SVG to hide
 */
function previewImageUpload(inputEl, previewImgId, placeholderIconId) {
    if (inputEl.files && inputEl.files[0]) {
        const reader = new FileReader();
        const previewImg = document.getElementById(previewImgId);
        const placeholderIcon = document.getElementById(placeholderIconId);
        
        reader.onload = function (e) {
            if (previewImg) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            }
            if (placeholderIcon) {
                placeholderIcon.style.display = 'none';
            }
        };
        
        reader.readAsDataURL(inputEl.files[0]);
    }
}

/**
 * 6. openProductModal
 * Dynamic Tokopedia-like modal gallery for products.
 * @param {HTMLElement} cardEl The clicked card element
 */
function openProductModal(cardEl) {
    const name = cardEl.getAttribute('data-name');
    const description = cardEl.getAttribute('data-description');
    const price = cardEl.getAttribute('data-price');
    const link = cardEl.getAttribute('data-link');
    
    // Extract up to 5 images
    const images = [];
    for (let i = 1; i <= 5; i++) {
        const img = cardEl.getAttribute(`data-img${i}`);
        if (img) {
            images.push(img);
        }
    }
    
    // Set standard texts
    document.getElementById('modalProductName').textContent = name;
    document.getElementById('modalProductPrice').textContent = price;
    document.getElementById('modalProductDesc').textContent = description || 'Tidak ada deskripsi produk.';
    document.getElementById('modalShopLink').setAttribute('href', link);
    
    // Prepare Main active image
    const mainImg = document.getElementById('modalMainImg');
    const thumbnailsRow = document.getElementById('modalThumbnailsRow');
    
    thumbnailsRow.innerHTML = ''; // Reset thumbnails
    
    if (images.length > 0) {
        mainImg.src = images[0];
        mainImg.style.display = 'block';
        
        // Generate thumbnails with Tokopedia-like interaction
        images.forEach((imgSrc, idx) => {
            const thumb = document.createElement('div');
            thumb.style.width = '55px';
            thumb.style.height = '55px';
            thumb.style.borderRadius = 'var(--radius-sm)';
            thumb.style.border = idx === 0 ? '2px solid var(--accent-sky)' : '1px solid var(--border-color)';
            thumb.style.overflow = 'hidden';
            thumb.style.cursor = 'pointer';
            thumb.style.transition = 'var(--transition-fast)';
            thumb.style.display = 'flex';
            thumb.style.alignItems = 'center';
            thumb.style.justifyContent = 'center';
            thumb.style.background = 'rgba(0,0,0,0.02)';
            
            const img = document.createElement('img');
            img.src = imgSrc;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            
            thumb.appendChild(img);
            
            // Hover effect
            thumb.addEventListener('mouseenter', () => {
                mainImg.style.opacity = '0.4';
                setTimeout(() => {
                    mainImg.src = imgSrc;
                    mainImg.style.opacity = '1';
                }, 80);
                
                // Reset active borders
                Array.from(thumbnailsRow.children).forEach(child => {
                    child.style.border = '1px solid var(--border-color)';
                });
                thumb.style.border = '2px solid var(--accent-sky)';
            });
            
            thumbnailsRow.appendChild(thumb);
        });
    } else {
        // Fallback placeholder
        mainImg.style.display = 'none';
    }
    
    // Show Modal with smooth animation
    const modal = document.getElementById('productDetailModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
        modal.querySelector('.modal-container').style.transform = 'scale(1)';
    }, 10);
    
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
}

/**
 * 7. closeProductModal
 * Closes the product detail modal smoothly.
 */
function closeProductModal() {
    const modal = document.getElementById('productDetailModal');
    if (modal) {
        modal.style.opacity = '0';
        modal.querySelector('.modal-container').style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
            // Restore scrolling
            document.body.style.overflow = '';
        }, 300);
    }
}

// Close modal if clicking backdrop
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('productDetailModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeProductModal();
            }
        });
    }
});
