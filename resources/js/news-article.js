import { marked } from 'marked';
import DOMPurify from 'dompurify';
import hljs from 'highlight.js/lib/core';
import bash from 'highlight.js/lib/languages/bash';
import cpp from 'highlight.js/lib/languages/cpp';
import csharp from 'highlight.js/lib/languages/csharp';
import css from 'highlight.js/lib/languages/css';
import dart from 'highlight.js/lib/languages/dart';
import java from 'highlight.js/lib/languages/java';
import javascript from 'highlight.js/lib/languages/javascript';
import json from 'highlight.js/lib/languages/json';
import kotlin from 'highlight.js/lib/languages/kotlin';
import markdown from 'highlight.js/lib/languages/markdown';
import php from 'highlight.js/lib/languages/php';
import plaintext from 'highlight.js/lib/languages/plaintext';
import python from 'highlight.js/lib/languages/python';
import sql from 'highlight.js/lib/languages/sql';
import typescript from 'highlight.js/lib/languages/typescript';
import xml from 'highlight.js/lib/languages/xml';
import yaml from 'highlight.js/lib/languages/yaml';
import 'highlight.js/styles/github-dark.css';
import '../css/news-article.css';

[
    ['bash', bash],
    ['cpp', cpp],
    ['csharp', csharp],
    ['css', css],
    ['dart', dart],
    ['java', java],
    ['javascript', javascript],
    ['json', json],
    ['kotlin', kotlin],
    ['markdown', markdown],
    ['php', php],
    ['plaintext', plaintext],
    ['python', python],
    ['sql', sql],
    ['typescript', typescript],
    ['xml', xml],
    ['yaml', yaml],
].forEach(([name, definition]) => hljs.registerLanguage(name, definition));

hljs.registerAliases(['js'], { languageName: 'javascript' });
hljs.registerAliases(['ts'], { languageName: 'typescript' });
hljs.registerAliases(['py'], { languageName: 'python' });
hljs.registerAliases(['html'], { languageName: 'xml' });
hljs.registerAliases(['shell'], { languageName: 'bash' });

const languageLabels = {
    bash: 'Bash',
    c: 'C',
    cpp: 'C++',
    csharp: 'C#',
    css: 'CSS',
    dart: 'Dart',
    html: 'HTML',
    java: 'Java',
    javascript: 'JavaScript',
    js: 'JavaScript',
    json: 'JSON',
    kotlin: 'Kotlin',
    markdown: 'Markdown',
    php: 'PHP',
    plaintext: 'Text',
    python: 'Python',
    py: 'Python',
    shell: 'Shell',
    sql: 'SQL',
    typescript: 'TypeScript',
    ts: 'TypeScript',
    xml: 'XML',
    yaml: 'YAML',
};

const escapeHtml = (value) => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const renderer = new marked.Renderer();

renderer.code = (token, legacyLanguage) => {
    const code = typeof token === 'object' ? token.text : token;
    const languageHint = typeof token === 'object' ? token.lang : legacyLanguage;
    const requestedLanguage = String(languageHint || '').trim().split(/\s+/)[0].toLowerCase();
    const canHighlightRequested = requestedLanguage && hljs.getLanguage(requestedLanguage);
    const highlighted = canHighlightRequested
        ? hljs.highlight(code, { language: requestedLanguage })
        : hljs.highlightAuto(code);
    const detectedLanguage = canHighlightRequested
        ? requestedLanguage
        : (highlighted.language || 'plaintext');
    const label = languageLabels[detectedLanguage] || detectedLanguage.toUpperCase();

    return `
        <section class="code-editor" data-code-editor>
            <header class="code-editor__bar">
                <span class="code-editor__dots" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="code-editor__language">${escapeHtml(label)}</span>
                <button class="code-editor__copy" type="button" data-copy-code aria-label="Salin kode">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-2M6 7h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/></svg>
                    <span>Salin</span>
                </button>
            </header>
            <pre><code class="hljs language-${escapeHtml(detectedLanguage)}">${highlighted.value}</code></pre>
        </section>`;
};

const renderArticle = () => {
    const source = document.querySelector('[data-article-source]');
    const target = document.querySelector('[data-article-content]');
    if (!source || !target) return;

    const parsed = marked.parse(source.value, {
        breaks: true,
        gfm: true,
        renderer,
    });
    target.innerHTML = DOMPurify.sanitize(parsed, {
        ADD_ATTR: ['target'],
    });
};

const copyCode = async (button) => {
    const code = button.closest('[data-code-editor]')?.querySelector('code')?.textContent;
    if (!code) return;

    await navigator.clipboard.writeText(code);
    const label = button.querySelector('span');
    const previous = label.textContent;
    label.textContent = 'Tersalin';
    button.classList.add('is-copied');
    window.setTimeout(() => {
        label.textContent = previous;
        button.classList.remove('is-copied');
    }, 1800);
};

const initializeNavigation = () => {
    const toggle = document.querySelector('[data-news-menu-toggle]');
    const menu = document.querySelector('[data-news-mobile-menu]');
    toggle?.addEventListener('click', () => {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isOpen));
        menu?.toggleAttribute('hidden', isOpen);
    });
};

const initializeReplies = () => {
    const parentInput = document.querySelector('[data-comment-parent]');
    const replyState = document.querySelector('[data-reply-state]');
    const replyName = document.querySelector('[data-reply-name]');
    const cancel = document.querySelector('[data-cancel-reply]');
    const form = document.querySelector('#comment-form');

    document.querySelectorAll('[data-reply-to]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!parentInput || !replyState || !replyName) return;
            parentInput.value = button.dataset.replyTo;
            replyName.textContent = button.dataset.replyName;
            replyState.hidden = false;
            form?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.setTimeout(() => form?.querySelector('textarea')?.focus(), 450);
        });
    });

    cancel?.addEventListener('click', () => {
        parentInput.value = '';
        replyState.hidden = true;
    });
};

document.addEventListener('DOMContentLoaded', () => {
    renderArticle();
    initializeNavigation();
    initializeReplies();

    document.addEventListener('click', (event) => {
        const copyButton = event.target.closest('[data-copy-code]');
        if (copyButton) copyCode(copyButton);
    });
});
