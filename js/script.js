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

window.updatePreview = function () {
    const urlInput = document.getElementById('link-url').value;
    const titleInput = document.getElementById('link-title').value;

    const purl = document.getElementById('preview-url-text');
    const ptitle = document.getElementById('preview-title-text');

    if (purl) {
        try {
            const domain = new URL(urlInput).hostname;
            purl.textContent = domain;
        } catch (e) {
            purl.textContent = urlInput || 'example.com';
        }
    }

    if (ptitle) {
        ptitle.textContent = titleInput || 'Example Website - The Best Resources';
    }
};
