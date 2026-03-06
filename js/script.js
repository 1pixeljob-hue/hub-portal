document.addEventListener('DOMContentLoaded', () => {
    // 1. Clock and Greeting Logic
    const clockDisplay = document.getElementById('clock-display');
    const greetingText = document.getElementById('greeting-text');

    function updateTime() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');

        if (clockDisplay) clockDisplay.textContent = `${hours}:${minutes}`;

        let greeting = 'Good morning';
        if (hours >= 12 && hours < 18) greeting = 'Good afternoon';
        else if (hours >= 18) greeting = 'Good evening';

        if (greetingText) greetingText.textContent = `${greeting}, Alex`;
    }

    updateTime();
    setInterval(updateTime, 1000);

    // 2. Theme Toggle Logic
    const themeToggleBtn = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    // Default is dark (set in HTML). Check localStorage if available
    if (localStorage.getItem('theme') === 'light') {
        htmlElement.classList.remove('dark');
        updateThemeIcon('dark_mode');
    }

    themeToggleBtn.addEventListener('click', () => {
        if (htmlElement.classList.contains('dark')) {
            htmlElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            updateThemeIcon('dark_mode');
        } else {
            htmlElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            updateThemeIcon('light_mode');
        }
    });

    function updateThemeIcon(iconName) {
        const iconSpan = themeToggleBtn.querySelector('.material-symbols-outlined');
        if (iconSpan) iconSpan.textContent = iconName;
    }

    // 3. Action Menu (Click outside)
    document.addEventListener('click', (e) => {
        const menus = document.querySelectorAll('.action-menu');
        menus.forEach(menu => {
            // If click is outside the menu AND outside its trigger button
            if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                menu.classList.remove('active');
            }
        });
    });

    // 4. Live Preview Logic
    const titleInput = document.getElementById('link-title');
    const urlInput = document.getElementById('link-url');
    const categoryInput = document.getElementById('link-category');
    const tagsInput = document.getElementById('link-tags');

    if (titleInput) titleInput.addEventListener('input', updatePreview);
    if (urlInput) urlInput.addEventListener('input', updatePreview);
    if (categoryInput) categoryInput.addEventListener('change', updatePreview);
    if (tagsInput) tagsInput.addEventListener('input', updatePreview);

    // 5. Category Filtering
    const filterBtns = document.querySelectorAll('.category-filter');
    const linkCards = document.querySelectorAll('.filter-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active', 'bg-surface-light-highlight', 'dark:bg-surface-dark-highlight'));
            btn.classList.add('active', 'bg-surface-light-highlight', 'dark:bg-surface-dark-highlight');

            const filterValue = btn.getAttribute('data-filter');

            linkCards.forEach(card => {
                if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 50);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
        });
    });

});

// Global functions for inline HTML events
window.toggleMenu = function (menuId, event) {
    event.stopPropagation(); // Prevent document click from firing immediately
    const menu = document.getElementById(menuId);
    // Close other menus
    document.querySelectorAll('.action-menu').forEach(m => {
        if (m.id !== menuId) m.classList.remove('active');
    });
    // Toggle current
    if (menu) menu.classList.toggle('active');
};

window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icon = type === 'success' ? 'check_circle' : 'error';

    toast.innerHTML = `
        <span class="material-symbols-outlined">${icon}</span>
        <span class="title">${message}</span>
    `;

    container.appendChild(toast);

    // Auto remove
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

window.deleteLink = function (id) {
    if (!confirm("Are you sure you want to delete this link?")) return;

    fetch(`api/links.php?id=${id}`, {
        method: 'DELETE'
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Remove element from DOM
                const card = document.querySelector(`.link-card[data-id="${id}"]`);
                if (card) {
                    card.style.transform = 'scale(0.8)';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                }
                showToast('Link deleted successfully');
            } else {
                showToast(data.error || 'Failed to delete', 'error');
            }
        })
        .catch(err => {
            showToast('Network error occurred', 'error');
        });
};

window.submitForm = function (event) {
    event.preventDefault();

    const url = document.getElementById('link-url').value;
    const title = document.getElementById('link-title').value;
    const category = document.getElementById('link-category').value;
    const tagsInput = document.getElementById('link-tags').value;

    // Parse tags (simple split by comma)
    const tagsArr = tagsInput.split(',').filter(t => t.trim() !== '').map(t => {
        return { name: t.trim(), type: 'primary', color: category };
    });

    // Default tag based on category if empty
    if (tagsArr.length === 0) {
        tagsArr.push({ name: document.getElementById('link-category').options[document.getElementById('link-category').selectedIndex].text, type: 'primary', color: category });
    }

    const payload = {
        url,
        title,
        tags: tagsArr,
        theme: category // Map category to theme color 
    };

    fetch('api/links.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Link added successfully!');
                document.getElementById('add-modal').classList.remove('active');
                document.getElementById('add-link-form').reset();
                // In a real SPA, we'd inject the new card HTML here.
                // For simplicity in this PHP render setup, we just reload to fetch from server
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(data.error || 'Failed to add', 'error');
            }
        })
        .catch(err => {
            showToast('Network error occurred', 'error');
        });
};

window.submitCategoryForm = function (event) {
    event.preventDefault();

    const name = document.getElementById('cat-title').value;
    const icon = document.getElementById('cat-icon').value;
    const color = document.getElementById('cat-color').value;

    const payload = {
        name,
        icon,
        color
    };

    fetch('api/categories.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Category created successfully!');
                document.getElementById('add-category-modal').classList.remove('active');
                document.getElementById('add-category-form').reset();
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(data.error || 'Failed to create category', 'error');
            }
        })
        .catch(err => {
            showToast('Network error occurred', 'error');
        });
};

window.updatePreview = function () {
    const titleInput = document.getElementById('link-title')?.value || '';
    const urlInput = document.getElementById('link-url')?.value || '';
    const category = document.getElementById('link-category')?.value || 'indigo';
    const tagsInput = document.getElementById('link-tags')?.value || '';

    // Elements
    const pTitle = document.getElementById('preview-title');
    const pUrl = document.getElementById('preview-url');
    const pInitial = document.getElementById('preview-initial');
    const pTagsContainer = document.getElementById('preview-tags-container');
    const pGradient = document.getElementById('preview-gradient');

    if (pTitle) pTitle.textContent = titleInput || 'New Link';

    if (pUrl) {
        try {
            pUrl.textContent = urlInput ? new URL(urlInput).hostname : 'example.com';
        } catch (e) {
            pUrl.textContent = urlInput || 'example.com';
        }
    }

    if (pInitial) {
        pInitial.textContent = titleInput ? titleInput.charAt(0).toUpperCase() : 'N';
        // Update gradient color based on category
        const gradiens = {
            'indigo': 'from-indigo-500 to-indigo-600',
            'purple': 'from-purple-500 to-purple-600',
            'pink': 'from-pink-500 to-pink-600',
            'emerald': 'from-emerald-500 to-emerald-600'
        };
        const gradClass = gradiens[category] || gradiens['indigo'];
        pInitial.className = `text-2xl font-black bg-gradient-to-br ${gradClass} bg-clip-text text-transparent`;
        if (pGradient) pGradient.className = `absolute top-0 left-0 h-1.5 w-full bg-gradient-to-r ${gradClass} rounded-t-xl z-20`;
    }

    if (pTagsContainer) {
        let tagsArr = tagsInput.split(',').map(t => t.trim()).filter(Boolean);
        if (tagsArr.length === 0) {
            const catSelect = document.getElementById('link-category');
            if (catSelect) tagsArr.push(catSelect.options[catSelect.selectedIndex].text.split(' ')[0]);
        }

        const tagClasses = {
            'indigo': 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-200/50 dark:border-indigo-800/30',
            'purple': 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border-purple-200/50 dark:border-purple-800/30',
            'pink': 'bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400 border-pink-200/50 dark:border-pink-800/30',
            'emerald': 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/30'
        };

        pTagsContainer.innerHTML = tagsArr.slice(0, 3).map(tag => `
            <span class="rounded-full ${tagClasses[category]} px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide border">
                ${tag}
            </span>
        `).join('');
    }
};
