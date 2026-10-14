{{-- Realtime Sidebar Menu Search Form --}}
<div x-data="sidebarMenuSearch()" 
     @click.outside="isOpen = false"
     class="flex-shrink-0 relative transition-all duration-200"
     :class="sidebarCollapsed ? 'px-2 pb-2' : 'px-4 pb-2.5'">

    <!-- Mode Collapsed (Mini Sidebar): Icon Button untuk membuka & mencari -->
    <div x-show="sidebarCollapsed" class="flex justify-center">
        <button type="button" 
                @click="toggleSidebar(); setTimeout(() => focusInput(), 150)"
                title="Cari Menu (Ctrl+K)"
                class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all shadow-sm group">
            <svg class="w-4 h-4 text-white/80 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
    </div>

    <!-- Mode Normal (Expanded Sidebar): Form Pencarian Lengkap -->
    <div x-show="!sidebarCollapsed" class="relative">
        <div class="relative flex items-center">
            <!-- Icon Kaca Pembesar -->
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white/70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Input Pencarian Realtime -->
            <input x-ref="searchInput"
                   x-model="query"
                   @input="handleInput()"
                   @focus="if(query.trim().length > 0) isOpen = true"
                   @keydown.escape="clearSearch()"
                   @keydown.arrow-down.prevent="selectNext()"
                   @keydown.arrow-up.prevent="selectPrev()"
                   @keydown.enter.prevent="openSelected()"
                   type="text"
                   placeholder="Cari menu... (Ctrl+K)"
                   autocomplete="off"
                   class="w-full bg-black/25 text-white placeholder-white/60 border border-white/20 rounded-xl text-xs py-2 pl-9 pr-8 outline-none focus:outline-none focus:ring-0 focus:border-white/20 focus:bg-black/25 shadow-inner" />

            <!-- Tombol Clear (X) saat ada teks -->
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                <button x-show="query.length > 0"
                        x-cloak
                        @click="clearSearch()"
                        type="button"
                        class="text-white/70 hover:text-white focus:text-white p-1 rounded-full hover:bg-black/20 transition-colors"
                        title="Hapus pencarian (Esc)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Dropdown Hasil Pencarian Realtime di Bawah Form -->
        <div x-show="isOpen && query.trim().length > 0"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
             class="absolute top-full left-0 right-0 mt-2 z-50 rounded-2xl bg-slate-950/95 backdrop-blur-xl border border-white/20 shadow-2xl overflow-hidden text-white flex flex-col max-h-72">
             
            <!-- Header Hasil -->
            <div class="px-3 py-2 border-b border-white/10 flex items-center justify-between text-[11px] font-bold text-white/80 bg-white/5">
                <span class="flex items-center gap-1.5">
                    <span>Menu Ditemukan</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-red-500/40 text-red-100 text-[10px] font-black"
                      x-text="results.length + ' hasil'"></span>
            </div>

            <!-- List Menu Hasil Pencarian -->
            <div class="overflow-y-auto sidebar-scroll p-1.5 space-y-1">
                <template x-for="(item, index) in results" :key="item.href + '-' + index">
                    <a :href="item.href"
                       :class="selectedIndex === index ? 'bg-white/20 text-white' : 'hover:bg-white/10 text-white/90'"
                       @mouseenter="selectedIndex = index"
                       class="flex items-center gap-2.5 p-2 rounded-xl transition-colors group/res">
                        
                        <!-- Icon Menu -->
                        <div class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center shrink-0 text-white [&>svg]:w-3.5 [&>svg]:h-3.5"
                             x-html="item.iconHtml">
                        </div>

                        <!-- Judul Menu & Modul / Grup Induk -->
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold leading-tight truncate text-white" x-html="highlightMatch(item.title)"></p>
                            <p class="text-[10px] text-white/60 font-medium truncate leading-tight mt-0.5" x-text="item.group || item.section || 'Menu'"></p>
                        </div>

                        <!-- Badge Menu (GPS, BARU, PKL, dll) -->
                        <template x-if="item.badge">
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full bg-white/20 text-white shrink-0"
                                  x-text="item.badge"></span>
                        </template>

                        <!-- Panah Navigasi -->
                        <svg class="w-3.5 h-3.5 text-white/40 group-hover/res:text-white group-hover/res:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </template>

                <!-- State Kosong Jika Tidak Ada Yang Cocok -->
                <div x-show="results.length === 0" class="py-5 px-3 text-center">
                    <p class="text-xs font-bold text-white/90">Menu tidak ditemukan</p>
                    <p class="text-[10px] text-white/50 mt-1">Tidak ada menu yang cocok dengan kata kunci "<span class="text-white/80 font-bold" x-text="query"></span>"</p>
                </div>
            </div>

            <!-- Footer Panduan Shortcut Keyboard -->
            <div class="px-3 py-1.5 bg-black/40 border-t border-white/10 flex items-center justify-between text-[9px] text-white/50">
                <span>Pilih: <kbd class="px-1 py-0.5 bg-white/10 rounded font-mono">↑</kbd> <kbd class="px-1 py-0.5 bg-white/10 rounded font-mono">↓</kbd></span>
                <span>Buka: <kbd class="px-1 py-0.5 bg-white/10 rounded font-mono">Enter</kbd></span>
                <span>Tutup: <kbd class="px-1 py-0.5 bg-white/10 rounded font-mono">Esc</kbd></span>
            </div>
        </div>
    </div>
</div>

<script>
    function sidebarMenuSearch() {
        return {
            query: '',
            menus: [],
            results: [],
            isOpen: false,
            selectedIndex: -1,
            defaultIconHtml: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>',

            init() {
                // Tunggu DOM selesai dirender sebelum menyusun index menu
                this.$nextTick(() => {
                    setTimeout(() => this.buildMenuIndex(), 250);
                });

                // Shortcut keyboard global Ctrl+K atau Cmd+K untuk fokus pencarian
                window.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault();
                        if (this.$data && typeof this.sidebarCollapsed !== 'undefined' && this.sidebarCollapsed) {
                            if (typeof this.toggleSidebar === 'function') this.toggleSidebar();
                        }
                        setTimeout(() => this.focusInput(), 100);
                    }
                });
            },

            focusInput() {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                    this.$refs.searchInput.select();
                }
            },

            buildMenuIndex() {
                const nav = document.getElementById('role-navigation');
                if (!nav) return;

                const indexed = [];
                let currentSection = 'Menu';

                Array.from(nav.children).forEach(node => {
                    if (node.hidden || node.style.display === 'none') return;

                    // Judul Bagian / Section
                    if (node.classList?.contains('section-title')) {
                        currentSection = node.textContent.trim() || 'Menu';
                        return;
                    }

                    if (node.tagName !== 'LI') return;
                    if (node.hidden || node.style.display === 'none') return;

                    // Menu Accordion / Dropdown
                    if (node.classList?.contains('submenu-dropdown')) {
                        const btnText = node.querySelector('button .nav-text')?.textContent.trim() || '';
                        const btnIcon = node.querySelector('button .nav-icon-container')?.innerHTML || this.defaultIconHtml;
                        
                        // Ekstrak child menu link dari .submenu-card
                        const subLinks = node.querySelectorAll('.submenu-card a.submenu-item');
                        subLinks.forEach(a => {
                            const clone = a.cloneNode(true);
                            clone.querySelectorAll('.submenu-dot, .nav-badge').forEach(el => el.remove());
                            const title = clone.textContent.trim();
                            const href = a.getAttribute('href');
                            const badge = a.querySelector('.nav-badge')?.textContent.trim() || '';

                            if (href && href !== '#' && title) {
                                indexed.push({
                                    title,
                                    href,
                                    group: btnText,
                                    section: currentSection,
                                    iconHtml: btnIcon,
                                    badge,
                                    domNode: a,
                                    parentDropdown: node
                                });
                            }
                        });
                        return;
                    }

                    // Menu Standalone Langsung
                    const directLink = node.querySelector('a.nav-link');
                    if (directLink) {
                        const clone = directLink.cloneNode(true);
                        clone.querySelectorAll('.nav-badge, .nav-icon-container').forEach(el => el.remove());
                        const title = clone.textContent.trim();
                        const href = directLink.getAttribute('href');
                        const iconHtml = directLink.querySelector('.nav-icon-container')?.innerHTML || this.defaultIconHtml;
                        const badge = directLink.querySelector('.nav-badge')?.textContent.trim() || '';

                        if (href && href !== '#' && title) {
                            indexed.push({
                                title,
                                href,
                                group: currentSection,
                                section: currentSection,
                                iconHtml,
                                badge,
                                domNode: node,
                                parentDropdown: null
                            });
                        }
                    }
                });

                this.menus = indexed;
            },

            handleInput() {
                const q = this.query.trim().toLowerCase();
                this.selectedIndex = -1;

                if (!q) {
                    this.results = [];
                    this.isOpen = false;
                    this.resetSidebarFilter();
                    return;
                }

                if (this.menus.length === 0) {
                    this.buildMenuIndex();
                }

                // Filter menu yang cocok secara realtime
                this.results = this.menus.filter(m => {
                    const matchTitle = m.title.toLowerCase().includes(q);
                    const matchGroup = m.group && m.group.toLowerCase().includes(q);
                    const matchSection = m.section && m.section.toLowerCase().includes(q);
                    const matchBadge = m.badge && m.badge.toLowerCase().includes(q);
                    return matchTitle || matchGroup || matchSection || matchBadge;
                });

                this.isOpen = true;
                if (this.results.length > 0) {
                    this.selectedIndex = 0;
                }

                // Sinkronisasikan penyaringan tampilan langsung pada navigasi sidebar
                this.filterSidebarDOM(q);
            },

            filterSidebarDOM(query) {
                const nav = document.getElementById('role-navigation');
                if (!nav) return;

                const q = query.toLowerCase();
                let currentSectionNode = null;
                let sectionHasMatches = false;

                Array.from(nav.children).forEach(node => {
                    if (node.classList?.contains('section-title')) {
                        if (currentSectionNode) {
                            currentSectionNode.style.display = sectionHasMatches ? '' : 'none';
                        }
                        currentSectionNode = node;
                        sectionHasMatches = false;
                        return;
                    }

                    if (node.tagName !== 'LI') return;

                    if (node.classList?.contains('submenu-dropdown')) {
                        const btnText = node.querySelector('button .nav-text')?.textContent.toLowerCase() || '';
                        const subLinks = node.querySelectorAll('.submenu-card a.submenu-item');
                        let dropdownHasMatch = btnText.includes(q);

                        subLinks.forEach(sub => {
                            const subText = sub.textContent.toLowerCase();
                            const subMatch = subText.includes(q);
                            if (subMatch) {
                                sub.style.display = '';
                                dropdownHasMatch = true;
                            } else {
                                sub.style.display = btnText.includes(q) ? '' : 'none';
                            }
                        });

                        if (dropdownHasMatch) {
                            node.style.display = '';
                            sectionHasMatches = true;
                            // Buka otomatis accordion yang memiliki item cocok
                            if (node._x_dataStack && node._x_dataStack[0]) {
                                node._x_dataStack[0].expanded = true;
                            }
                        } else {
                            node.style.display = 'none';
                        }
                        return;
                    }

                    // Item menu biasa
                    const text = node.textContent.toLowerCase();
                    if (text.includes(q)) {
                        node.style.display = '';
                        sectionHasMatches = true;
                    } else {
                        node.style.display = 'none';
                    }
                });

                if (currentSectionNode) {
                    currentSectionNode.style.display = sectionHasMatches ? '' : 'none';
                }
            },

            resetSidebarFilter() {
                const nav = document.getElementById('role-navigation');
                if (!nav) return;

                Array.from(nav.children).forEach(node => {
                    node.style.display = '';
                    if (node.classList?.contains('submenu-dropdown')) {
                        const subLinks = node.querySelectorAll('.submenu-card a.submenu-item');
                        subLinks.forEach(sub => sub.style.display = '');
                    }
                });
            },

            clearSearch() {
                this.query = '';
                this.results = [];
                this.isOpen = false;
                this.selectedIndex = -1;
                this.resetSidebarFilter();
                this.focusInput();
            },

            selectNext() {
                if (this.results.length === 0) return;
                this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
            },

            selectPrev() {
                if (this.results.length === 0) return;
                this.selectedIndex = (this.selectedIndex - 1 + this.results.length) % this.results.length;
            },

            openSelected() {
                if (this.results.length > 0 && this.selectedIndex >= 0 && this.selectedIndex < this.results.length) {
                    window.location.href = this.results[this.selectedIndex].href;
                }
            },

            highlightMatch(text) {
                if (!this.query.trim()) return text;
                const div = document.createElement('div');
                div.textContent = text;
                const escaped = div.innerHTML;

                const safeQuery = this.query.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp(`(${safeQuery})`, 'gi');
                return escaped.replace(regex, '<span class="bg-amber-300 text-slate-900 px-0.5 rounded font-black">$1</span>');
            }
        };
    }
</script>
