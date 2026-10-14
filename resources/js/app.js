import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import DOMPurify from 'dompurify';
import { marked } from 'marked';

marked.setOptions({
    breaks: true,
    gfm: true,
});

window.renderStellaMarkdown = (content = '') => DOMPurify.sanitize(
    marked.parse(String(content), { async: false }),
    {
        USE_PROFILES: { html: true },
        ADD_ATTR: ['target', 'rel'],
    },
);

window.Alpine = Alpine;

Alpine.plugin(collapse);

Alpine.store('detailModal', {
    on: false,
    data: {},
    open(item) {
        this.data = item || {};
        this.on = true;
    },
    close() {
        this.on = false;
        this.data = {};
    },
});

window.eraporReferenceCombobox = (config) => ({
    open: false,
    query: '',
    loading: false,
    options: [],
    selectedId: config.selectedId ? String(config.selectedId) : '',
    selectedLabel: config.selectedLabel || '',
    requestSequence: 0,

    async toggle() {
        this.open = !this.open;

        if (!this.open) {
            return;
        }

        await this.search();
        this.$nextTick(() => this.$refs.searchInput?.focus());
    },

    async search() {
        const sequence = ++this.requestSequence;
        this.loading = true;

        try {
            const url = new URL(config.endpoint, window.location.origin);
            url.searchParams.set('q', this.query);

            if (this.selectedId) {
                url.searchParams.set('selected_id', this.selectedId);
            }

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Gagal memuat referensi e-Rapor.');
            }

            const payload = await response.json();

            if (sequence === this.requestSequence) {
                this.options = payload.data || [];
            }
        } catch (error) {
            if (sequence === this.requestSequence) {
                this.options = [];
            }
        } finally {
            if (sequence === this.requestSequence) {
                this.loading = false;
            }
        }
    },

    choose(option) {
        this.selectedId = String(option.id);
        this.selectedLabel = option.label;
        this.open = false;
        this.query = '';
    },
});

window.eraporCurriculumCombobox = window.eraporReferenceCombobox;

Alpine.start();
