// Helper: Chỉ lấy text thuần của một element, bỏ qua text bên trong thẻ con (icon)
window.getOptionLabel = function (el) {
    let text = '';
    el.childNodes.forEach(node => {
        if (node.nodeType === Node.TEXT_NODE) text += node.textContent;
    });
    return text.trim();
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. Clock and Greeting Logic
    const clockDisplay = document.getElementById('clock-display');
    const greetingText = document.getElementById('greeting-text');

    function updateTime() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');

        if (clockDisplay) clockDisplay.textContent = `${hours}:${minutes}`;

        let greeting = 'Chào buổi sáng';
        if (hours >= 12 && hours < 18) greeting = 'Chào buổi chiều';
        else if (hours >= 18) greeting = 'Chào buổi tối';

        if (greetingText) greetingText.textContent = `${greeting}, Sếp`;
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
            if (window.showToast) window.showToast('Giao diện Sáng đã bật', 'success');
        } else {
            htmlElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            updateThemeIcon('light_mode');
            if (window.showToast) window.showToast('Giao diện Tối đã bật', 'success');
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

    // 4. Live Preview & Smart Input Logic
    const titleInput = document.getElementById('link-title');
    const urlInput = document.getElementById('link-url');
    const categoryInput = document.getElementById('link-category');
    const tagsInput = document.getElementById('link-tags');

    if (titleInput) titleInput.addEventListener('input', updatePreview);

    if (urlInput) {
        urlInput.addEventListener('input', updatePreview);
        // Auto add https:// on blur
        urlInput.addEventListener('blur', function (e) {
            let val = e.target.value.trim();
            if (val && !/^https?:\/\//i.test(val)) {
                e.target.value = 'https://' + val;
                updatePreview();
            }
        });
    }

    // Custom Category Select Logic
    const catSelectBtn = document.getElementById('category-select-btn');
    const catSelectMenu = document.getElementById('category-select-menu');
    const catSelectText = document.getElementById('category-select-text');
    const catSelectIcon = document.getElementById('category-select-icon');
    const catHiddenInput = document.getElementById('link-category');
    const catOptions = document.querySelectorAll('.custom-select-option');

    if (catSelectBtn && catSelectMenu && catHiddenInput) {
        let prevCategoryValue = catHiddenInput.value;

        // Khởi tạo text hiển thị theo giá trị mặc định lúc load
        const activeOption = document.querySelector(`.custom-select-option[data-value="${prevCategoryValue}"]`);
        if (activeOption) {
            catSelectText.textContent = getOptionLabel(activeOption);
        }

        function openSelectMenu() {
            catSelectMenu.classList.remove('opacity-0', 'invisible', '-translate-y-2');
            catSelectMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
            if (catSelectIcon) catSelectIcon.style.transform = 'rotate(180deg)';
        }

        function closeSelectMenu() {
            catSelectMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
            catSelectMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            if (catSelectIcon) catSelectIcon.style.transform = 'rotate(0deg)';
        }

        // Toggle menu
        catSelectBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = catSelectMenu.classList.contains('opacity-100');
            if (isOpen) closeSelectMenu();
            else openSelectMenu();
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!catSelectBtn.contains(e.target) && !catSelectMenu.contains(e.target)) {
                closeSelectMenu();
            }
        });

        // Option click
        catOptions.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                const label = getOptionLabel(option);
                const color = option.getAttribute('data-color') || 'indigo';

                // Update value
                catHiddenInput.value = value;
                catHiddenInput.setAttribute('data-color', color);
                catSelectText.textContent = label;
                prevCategoryValue = value;
                updatePreview();

                closeSelectMenu();
            });
        });
    }

    // Custom Color Select Logic (Add Category Modal)
    const colorSelectBtn = document.getElementById('color-select-btn');
    const colorSelectMenu = document.getElementById('color-select-menu');
    const colorSelectDisplay = document.getElementById('color-select-display');
    const colorSelectIcon = document.getElementById('color-select-icon');
    const colorHiddenInput = document.getElementById('cat-color');
    const colorOptions = document.querySelectorAll('.custom-color-option');

    if (colorSelectBtn && colorSelectMenu && colorHiddenInput) {

        function openColorMenu() {
            colorSelectMenu.classList.remove('opacity-0', 'invisible', '-translate-y-2');
            colorSelectMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
            if (colorSelectIcon) colorSelectIcon.style.transform = 'rotate(180deg)';
        }

        function closeColorMenu() {
            colorSelectMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
            colorSelectMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            if (colorSelectIcon) colorSelectIcon.style.transform = 'rotate(0deg)';
        }

        // Toggle menu
        colorSelectBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = colorSelectMenu.classList.contains('opacity-100');
            if (isOpen) closeColorMenu();
            else openColorMenu();
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!colorSelectBtn.contains(e.target) && !colorSelectMenu.contains(e.target)) {
                closeColorMenu();
            }
        });

        // Option click
        colorOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                // Ignore clicks originating from input[type="color"] to avoid double triggers
                if (e.target.tagName.toLowerCase() === 'input') return;

                const value = option.getAttribute('data-value');
                // Use a clean display content depending on whether it's custom or predefined
                if (value.startsWith('#')) {
                    colorSelectDisplay.innerHTML = `<div class="w-4 h-4 rounded-full border border-gray-300 pointer-events-none transition-colors" style="background-color: ${value};"></div><span class="pointer-events-none flex-1">Tùy Chỉnh Màu</span>`;
                } else {
                    colorSelectDisplay.innerHTML = option.innerHTML;
                }
                colorHiddenInput.value = value;
                closeColorMenu();
            });
        });

        const customColorPicker = document.getElementById('custom-color-picker');
        const customColorPreview = document.getElementById('custom-color-preview');
        const customColorLi = document.getElementById('custom-color-li');

        if (customColorPicker) {
            customColorPicker.addEventListener('input', (e) => {
                const hexColor = e.target.value;
                if (customColorPreview) customColorPreview.style.backgroundColor = hexColor;
                if (customColorLi) customColorLi.setAttribute('data-value', hexColor);

                // Update display immediately while user chooses
                colorSelectDisplay.innerHTML = `<div class="w-4 h-4 rounded-full border border-gray-300 pointer-events-none transition-colors" style="background-color: ${hexColor};"></div><span class="pointer-events-none flex-1">Tùy Chỉnh Màu</span>`;
                colorHiddenInput.value = hexColor;
            });
            // Auto close menu when user clicks outside picker
            customColorPicker.addEventListener('change', () => {
                closeColorMenu();
            });
        }
    }

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
            const filterName = btn.innerText.replace(/[0-9]+$/, '').trim(); // Lấy tên text và loại bỏ số count

            linkCards.forEach(card => {
                if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                    card.style.display = 'block';
                    setTimeout(() => card.style.opacity = '1', 50);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });

            if (window.showToast) window.showToast(`Lọc theo: ${filterName}`, 'success');
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

window.copyUrl = function (url) {
    navigator.clipboard.writeText(url).then(() => {
        if (window.showToast) window.showToast('Đã sao chép liên kết!');
    }).catch(err => {
        if (window.showToast) window.showToast('Sao chép thất bại', 'error');
    });
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
    if (!confirm("Bạn có chắc chắn muốn xóa liên kết này không?")) return;

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
                showToast('Xóa liên kết thành công');
            } else {
                showToast(data.error || 'Xóa thất bại', 'error');
            }
        })
        .catch(err => {
            showToast('Lỗi kết nối mạng', 'error');
        });
};

window.editLink = function (link, event) {
    if (event) event.stopPropagation();

    // Đổ dữ liệu vào form
    document.getElementById('link-id').value = link.id;
    document.getElementById('link-title').value = link.title;
    document.getElementById('link-url').value = link.url;

    // Cập nhật category
    const catInput = document.getElementById('link-category');
    const catText = document.getElementById('category-select-text');
    catInput.value = link.theme;
    const activeOption = document.querySelector(`.custom-select-option[data-value="${link.theme}"]`);
    if (activeOption) {
        catText.textContent = getOptionLabel(activeOption);
        catInput.setAttribute('data-color', activeOption.getAttribute('data-color') || 'indigo');
    }

    // Cập nhật tags (join lại thành string)
    let tagsStr = '';
    if (Array.isArray(link.tags)) {
        tagsStr = link.tags.map(t => typeof t === 'object' ? t.name : t).join(', ');
    }
    document.getElementById('link-tags').value = tagsStr;

    // Đổi tiêu đề Form
    document.getElementById('modal-title').textContent = 'Chỉnh Sửa Liên Kết';
    document.getElementById('submit-btn-text').textContent = 'Cập Nhật';

    // Update live preview
    window.updatePreview();

    // Ẩn dropdown menu hiện hành
    document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('active'));

    // Hiển thị modal
    document.getElementById('add-modal').classList.add('active');
};

window.deleteCategory = function (id, name, event) {
    if (event) event.stopPropagation();

    if (!confirm(`Bạn có chắc chắn muốn xóa danh mục: "${name}" không?`)) return;

    fetch(`api/categories.php?id=${id}`, {
        method: 'DELETE'
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Xóa danh mục thành công');
                setTimeout(() => window.location.reload(), 500);
            } else {
                showToast(data.error || 'Xóa danh mục thất bại', 'error');
            }
        })
        .catch(err => {
            showToast('Lỗi kết nối mạng', 'error');
        });
};

window.openAddLinkModal = function () {
    document.getElementById('link-id').value = '';
    document.getElementById('add-link-form').reset();
    document.getElementById('modal-title').textContent = 'Thêm Liên Kết Mới';
    document.getElementById('submit-btn-text').textContent = 'Lưu Liên Kết';

    // Check active category filter in sidebar
    const activeFilter = document.querySelector('.category-filter.active');
    let targetCategoryValue = null;

    if (activeFilter && activeFilter.getAttribute('data-filter') !== 'all') {
        targetCategoryValue = activeFilter.getAttribute('data-filter');
    }

    // Set category based on active filter or default to first option
    let targetOption = null;
    if (targetCategoryValue) {
        targetOption = document.querySelector(`.custom-select-option[data-value="${targetCategoryValue}"]`);
    }
    if (!targetOption) {
        targetOption = document.querySelector('.custom-select-option');
    }

    if (targetOption) {
        document.getElementById('link-category').value = targetOption.getAttribute('data-value');
        document.getElementById('link-category').setAttribute('data-color', targetOption.getAttribute('data-color') || 'indigo');
        const catDisplayText = document.getElementById('category-select-text');
        if (catDisplayText) catDisplayText.textContent = getOptionLabel(targetOption);
    }

    window.updatePreview();
    document.getElementById('add-modal').classList.add('active');
};

window.submitForm = function (event) {
    event.preventDefault();

    let id = document.getElementById('link-id').value.trim();
    let url = document.getElementById('link-url').value.trim();
    if (url && !/^https?:\/\//i.test(url)) {
        url = 'https://' + url;
        document.getElementById('link-url').value = url;
    }

    const title = document.getElementById('link-title').value;
    const categoryInput = document.getElementById('link-category');
    const categoryId = categoryInput ? categoryInput.value : 'indigo';
    const tagsInput = document.getElementById('link-tags').value;

    // Parse tags (simple split by comma)
    const tagsArr = tagsInput.split(',').filter(t => t.trim() !== '').map(t => {
        return { name: t.trim(), type: 'primary', color: categoryId };
    });

    // Default tag based on category if empty
    if (tagsArr.length === 0) {
        const catText = document.getElementById('category-select-text');
        const catColor = categoryInput ? (categoryInput.getAttribute('data-color') || 'indigo') : 'indigo';
        if (catText) tagsArr.push({ name: catText.textContent.trim(), type: 'primary', color: catColor });
    }

    const payload = {
        id: id || undefined,
        url,
        title,
        tags: tagsArr,
        theme: categoryId
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
                showToast(id ? 'Cập nhật liên kết thành công!' : 'Thêm liên kết mới thành công!');
                document.getElementById('add-modal').classList.remove('active');
                document.getElementById('add-link-form').reset();
                // In a real SPA, we'd inject the new card HTML here.
                // For simplicity in this PHP render setup, we just reload to fetch from server
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(data.error || (id ? 'Cập nhật thất bại' : 'Thêm liên kết thất bại'), 'error');
            }
        })
        .catch(err => {
            showToast('Lỗi kết nối mạng', 'error');
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
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.error || 'Server returned ' + res.status);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                showToast('Tạo danh mục mới thành công!');
                document.getElementById('add-category-modal').classList.remove('active');
                document.getElementById('add-category-form').reset();
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(data.error || 'Tạo danh mục thất bại', 'error');
            }
        })
        .catch(err => {
            console.error('Submit Error:', err);
            showToast(err.message || 'Thao tác không thành công', 'error');
        });
};

window.updatePreview = function () {
    const titleInput = document.getElementById('link-title')?.value || '';
    const urlInput = document.getElementById('link-url')?.value || '';
    const categoryInput = document.getElementById('link-category');
    const categoryId = categoryInput?.value || 'indigo';
    const categoryColor = categoryInput?.getAttribute('data-color') || 'indigo';
    const tagsInput = document.getElementById('link-tags')?.value || '';

    // Elements
    const pTitle = document.getElementById('preview-title');
    const pUrl = document.getElementById('preview-url');
    const pInitial = document.getElementById('preview-initial');
    const pLogo = document.getElementById('preview-logo');
    const pTagsContainer = document.getElementById('preview-tags-container');
    const pGradient = document.getElementById('preview-gradient');

    if (pTitle) pTitle.textContent = titleInput || 'Liên Kết Mới';

    let hostname = '';
    if (pUrl) {
        let tempUrl = urlInput;
        if (tempUrl && !/^https?:\/\//i.test(tempUrl)) {
            tempUrl = 'https://' + tempUrl;
        }
        try {
            hostname = tempUrl ? new URL(tempUrl).hostname : '';
            pUrl.textContent = hostname || 'example.com';
        } catch (e) {
            pUrl.textContent = urlInput || 'example.com';
        }
    }

    if (pInitial) {
        const gradiens = {
            'indigo': 'from-indigo-500 to-indigo-600',
            'purple': 'from-purple-500 to-purple-600',
            'pink': 'from-pink-500 to-pink-600',
            'emerald': 'from-emerald-500 to-emerald-600'
        };
        const gradClass = gradiens[categoryColor] || gradiens['indigo'];

        if (hostname && pLogo) {
            pLogo.src = `https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://${hostname}&size=128`;
            pLogo.classList.remove('hidden');
            if (categoryColor.startsWith('#')) {
                pInitial.className = `text-2xl font-black bg-clip-text text-transparent hidden`;
                pInitial.style.backgroundImage = `linear-gradient(to bottom right, ${categoryColor}, ${categoryColor})`;
            } else {
                pInitial.className = `text-2xl font-black bg-gradient-to-br ${gradClass} bg-clip-text text-transparent hidden`;
                pInitial.style.backgroundImage = '';
            }
        } else {
            if (pLogo) pLogo.classList.add('hidden');
            pInitial.textContent = titleInput ? titleInput.charAt(0).toUpperCase() : 'N';
            if (categoryColor.startsWith('#')) {
                pInitial.className = `text-2xl font-black bg-clip-text text-transparent`;
                pInitial.style.backgroundImage = `linear-gradient(to bottom right, ${categoryColor}, ${categoryColor})`;
            } else {
                pInitial.className = `text-2xl font-black bg-gradient-to-br ${gradClass} bg-clip-text text-transparent`;
                pInitial.style.backgroundImage = '';
            }
        }

        if (pGradient) {
            if (categoryColor.startsWith('#')) {
                pGradient.className = `absolute top-0 left-0 h-1.5 w-full rounded-t-xl z-20`;
                pGradient.style.background = categoryColor;
            } else {
                pGradient.className = `absolute top-0 left-0 h-1.5 w-full bg-gradient-to-r ${gradClass} rounded-t-xl z-20`;
                pGradient.style.background = '';
            }
        }
    }

    if (pTagsContainer) {
        let tagsArr = tagsInput.split(',').map(t => t.trim()).filter(Boolean);
        if (tagsArr.length === 0) {
            const catText = document.getElementById('category-select-text');
            if (catText) tagsArr.push(catText.innerText.trim().split(' ')[0]);
        }

        const tagClasses = {
            'indigo': 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-200/50 dark:border-indigo-800/30',
            'purple': 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border-purple-200/50 dark:border-purple-800/30',
            'pink': 'bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400 border-pink-200/50 dark:border-pink-800/30',
            'emerald': 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/30'
        };

        pTagsContainer.innerHTML = tagsArr.slice(0, 3).map(tag => {
            if (categoryColor.startsWith('#')) {
                // Fallback style inside loop using inline CSS for custom HEX
                return `<span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide border" style="color: ${categoryColor}; border-color: ${categoryColor}40; background-color: ${categoryColor}10;">${tag}</span>`;
            } else {
                return `<span class="rounded-full ${tagClasses[categoryColor] || tagClasses['indigo']} px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide border">${tag}</span>`;
            }
        }).join('');
    }
};
