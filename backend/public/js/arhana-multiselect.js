/**
 * ARHANA - Dynamic Multi-Select Checkbox Component
 * Premium dark glassmorphic multiselect dropdown matching the custom Zinc Professional Theme.
 */
class ArhanaMultiSelect {
    constructor(elementId, placeholderText = 'Pilih Kategori...') {
        this.container = document.getElementById(elementId);
        if (!this.container) return;

        this.placeholderText = placeholderText;
        this.init();
    }

    init() {
        // Find existing checkboxes & labels inside
        const originalLabels = Array.from(this.container.querySelectorAll('label'));
        this.items = originalLabels.map(label => {
            const input = label.querySelector('input[type="checkbox"]');
            return {
                id: input.value,
                name: label.textContent.trim(),
                checked: input.checked,
                element: label,
                input: input
            };
        });

        // Hide original container content but keep it in DOM so form submit works
        this.container.style.display = 'none';

        // Create new interactive layout wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'arhana-multiselect-wrapper';
        this.wrapper.style.cssText = `
            position: relative;
            width: 100%;
            font-family: var(--font-admin);
        `;

        // Create the Select Trigger Input Header
        this.trigger = document.createElement('div');
        this.trigger.className = 'arhana-multiselect-trigger';
        this.trigger.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 44px;
            width: 100%;
            background-color: var(--bg-admin-card);
            border: 1px solid var(--border-admin);
            border-radius: var(--radius-sm);
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: var(--transition-admin);
            gap: 0.5rem;
            user-select: none;
        `;

        // Visual selection tags area inside trigger
        this.tagsArea = document.createElement('div');
        this.tagsArea.style.cssText = `
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            flex: 1;
            align-items: center;
        `;

        this.arrow = document.createElement('div');
        this.arrow.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.25s ease; color: var(--text-admin-secondary);">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        `;

        this.trigger.appendChild(this.tagsArea);
        this.trigger.appendChild(this.arrow);
        this.wrapper.appendChild(this.trigger);

        // Create the Dropdown List Card
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'arhana-multiselect-dropdown';
        this.dropdown.style.cssText = `
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background-color: var(--bg-admin-sidebar);
            border: 1px solid var(--border-admin-hover);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-admin);
            z-index: 999;
            max-height: 320px;
            overflow-y: auto;
            flex-direction: column;
            animation: arhanaFadeIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        `;

        // Search Box header inside dropdown
        this.searchContainer = document.createElement('div');
        this.searchContainer.style.cssText = `
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-admin);
            position: sticky;
            top: 0;
            background-color: var(--bg-admin-sidebar);
            z-index: 10;
            display: flex;
            gap: 0.5rem;
            align-items: center;
        `;

        this.searchInput = document.createElement('input');
        this.searchInput.type = 'text';
        this.searchInput.placeholder = 'Cari kategori...';
        this.searchInput.style.cssText = `
            width: 100%;
            background-color: var(--bg-admin-card);
            border: 1px solid var(--border-admin);
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.75rem 0.5rem 2.2rem;
            color: var(--text-admin-primary);
            font-size: 0.85rem;
            outline: none;
            transition: var(--transition-admin);
        `;
        
        // Search icon
        const searchIcon = document.createElement('div');
        searchIcon.style.cssText = `
            position: absolute;
            left: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-admin-muted);
            pointer-events: none;
        `;
        searchIcon.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        `;

        this.searchContainer.appendChild(searchIcon);
        this.searchContainer.appendChild(this.searchInput);
        this.dropdown.appendChild(this.searchContainer);

        // Dropdown Items list
        this.listArea = document.createElement('div');
        this.listArea.style.cssText = `
            padding: 0.25rem 0;
        `;
        
        this.dropdown.appendChild(this.listArea);
        this.wrapper.appendChild(this.dropdown);
        this.container.parentNode.insertBefore(this.wrapper, this.container.nextSibling);

        // Inject dropdown fade keyframes if not exists
        if (!document.getElementById('arhana-multiselect-styles')) {
            const style = document.createElement('style');
            style.id = 'arhana-multiselect-styles';
            style.innerHTML = `
                @keyframes arhanaFadeIn {
                    from { opacity: 0; transform: translateY(-8px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .arhana-multiselect-dropdown::-webkit-scrollbar {
                    width: 6px;
                }
                .arhana-multiselect-dropdown::-webkit-scrollbar-track {
                    background: transparent;
                }
                .arhana-multiselect-dropdown::-webkit-scrollbar-thumb {
                    background: var(--border-admin-hover);
                    border-radius: 10px;
                }
                .arhana-multiselect-dropdown::-webkit-scrollbar-thumb:hover {
                    background: var(--text-admin-muted);
                }
            `;
            document.head.appendChild(style);
        }

        // Render & Bind events
        this.renderItems();
        this.updateTriggerTags();
        this.bindEvents();
    }

    renderItems() {
        this.listArea.innerHTML = '';
        
        if (this.items.length === 0) {
            const empty = document.createElement('div');
            empty.style.cssText = `
                padding: 1.5rem 1rem;
                text-align: center;
                color: var(--text-admin-muted);
                font-size: 0.85rem;
            `;
            empty.textContent = 'Tidak ada kategori tersedia.';
            this.listArea.appendChild(empty);
            return;
        }

        this.items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'arhana-multiselect-item';
            row.style.cssText = `
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.65rem 1rem;
                cursor: pointer;
                transition: var(--transition-admin);
                background-color: ${item.checked ? 'rgba(56, 189, 248, 0.04)' : 'transparent'};
                border-left: 3px solid ${item.checked ? 'var(--accent-blue)' : 'transparent'};
            `;

            const checkbox = document.createElement('div');
            checkbox.style.cssText = `
                width: 18px;
                height: 18px;
                border-radius: 4px;
                border: 2px solid ${item.checked ? 'var(--accent-blue)' : 'var(--text-admin-muted)'};
                background-color: ${item.checked ? 'var(--accent-blue)' : 'transparent'};
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.15s ease;
                flex-shrink: 0;
            `;
            checkbox.innerHTML = item.checked ? `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            ` : '';

            const text = document.createElement('span');
            text.textContent = item.name;
            text.style.cssText = `
                font-size: 0.875rem;
                color: ${item.checked ? 'var(--text-admin-primary)' : 'var(--text-admin-secondary)'};
                font-weight: ${item.checked ? '600' : '500'};
                user-select: none;
            `;

            row.appendChild(checkbox);
            row.appendChild(text);

            row.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleItem(item);
            });

            row.addEventListener('mouseenter', () => {
                if (!item.checked) {
                    row.style.backgroundColor = 'rgba(255, 255, 255, 0.02)';
                }
            });
            row.addEventListener('mouseleave', () => {
                if (!item.checked) {
                    row.style.backgroundColor = 'transparent';
                }
            });

            // Store DOM element inside item
            item.domElement = row;
            this.listArea.appendChild(row);
        });
    }

    toggleItem(item) {
        item.checked = !item.checked;
        
        // Update original checkbox
        item.input.checked = item.checked;
        // Trigger original change event just in case
        item.input.dispatchEvent(new Event('change', { bubbles: true }));

        // Re-render
        this.renderItems();
        this.updateTriggerTags();
    }

    updateTriggerTags() {
        this.tagsArea.innerHTML = '';
        const selected = this.items.filter(item => item.checked);

        if (selected.length === 0) {
            const emptyText = document.createElement('span');
            emptyText.textContent = this.placeholderText;
            emptyText.style.cssText = `
                color: var(--text-admin-muted);
                font-size: 0.875rem;
                font-weight: 500;
            `;
            this.tagsArea.appendChild(emptyText);
            return;
        }

        selected.forEach(item => {
            const tag = document.createElement('div');
            tag.style.cssText = `
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                background-color: var(--accent-blue);
                color: #000;
                font-size: 0.75rem;
                font-weight: 700;
                padding: 0.2rem 0.5rem;
                border-radius: 4px;
                user-select: none;
                transition: opacity 0.15s;
            `;
            tag.textContent = item.name;

            const closeBtn = document.createElement('span');
            closeBtn.innerHTML = `
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            `;
            closeBtn.style.cssText = `
                cursor: pointer;
                display: flex;
                align-items: center;
                opacity: 0.7;
                transition: opacity 0.15s;
            `;
            closeBtn.addEventListener('mouseenter', () => closeBtn.style.opacity = '1');
            closeBtn.addEventListener('mouseleave', () => closeBtn.style.opacity = '0.7');
            
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleItem(item);
            });

            tag.appendChild(closeBtn);
            this.tagsArea.appendChild(tag);
        });
    }

    bindEvents() {
        // Toggle dropdown open/close
        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = this.dropdown.style.display === 'flex';
            this.closeAllDropdowns();
            if (!isOpen) {
                this.openDropdown();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });

        // Prevent closing when clicking inside dropdown
        this.dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Search filtering logic
        this.searchInput.addEventListener('input', () => {
            const query = this.searchInput.value.toLowerCase().trim();
            this.items.forEach(item => {
                const matches = item.name.toLowerCase().includes(query);
                if (item.domElement) {
                    item.domElement.style.display = matches ? 'flex' : 'none';
                }
            });
        });
    }

    openDropdown() {
        this.dropdown.style.display = 'flex';
        this.trigger.style.borderColor = 'var(--accent-blue)';
        this.trigger.style.boxShadow = '0 0 0 3px rgba(56, 189, 248, 0.15)';
        this.arrow.querySelector('svg').style.transform = 'rotate(180deg)';
        setTimeout(() => this.searchInput.focus(), 50);
    }

    closeDropdown() {
        this.dropdown.style.display = 'none';
        this.trigger.style.borderColor = 'var(--border-admin)';
        this.trigger.style.boxShadow = 'none';
        this.arrow.querySelector('svg').style.transform = 'rotate(0deg)';
        this.searchInput.value = '';
        this.items.forEach(item => {
            if (item.domElement) item.domElement.style.display = 'flex';
        });
    }

    closeAllDropdowns() {
        document.querySelectorAll('.arhana-multiselect-dropdown').forEach(dd => {
            dd.style.display = 'none';
        });
        document.querySelectorAll('.arhana-multiselect-trigger').forEach(tr => {
            tr.style.borderColor = 'var(--border-admin)';
            tr.style.boxShadow = 'none';
        });
        document.querySelectorAll('.arhana-multiselect-trigger svg').forEach(svg => {
            svg.style.transform = 'rotate(0deg)';
        });
    }
}
