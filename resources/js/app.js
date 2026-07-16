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
Alpine.start();
