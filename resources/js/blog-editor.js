import { initTagInput } from './tag-input';

export function initBlogEditor() {
    const form = document.querySelector('[data-blog-editor-form]');

    if (!form) {
        return;
    }

    initBlogTags(form);
    initBlogPublishSwitch(form);

    const editorHost = form.querySelector('[data-blog-editor]');
    const contentInput = form.querySelector('[data-blog-content-input]');
    const preview = form.querySelector('[data-blog-preview]');
    const tabButtons = form.querySelectorAll('[data-blog-tab]');
    const panes = {
        editor: form.querySelector('[data-blog-pane="editor"]'),
        preview: form.querySelector('[data-blog-pane="preview"]'),
    };

    if (!editorHost || !contentInput || !preview || !window.Quill) {
        return;
    }

    const initialHtml = contentInput.value || '';

    const quill = new window.Quill(editorHost, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'code-block'],
                ['link'],
                ['clean'],
            ],
        },
    });

    if (initialHtml.trim() !== '') {
        quill.clipboard.dangerouslyPasteHTML(initialHtml);
    }

    const syncContent = () => {
        contentInput.value = quill.root.innerHTML;
        renderPreview(preview, contentInput.value);
    };

    quill.on('text-change', syncContent);
    syncContent();

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const tab = button.dataset.blogTab;

            tabButtons.forEach((btn) => {
                const active = btn === button;
                btn.classList.toggle('active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            const isPreview = tab === 'preview';
            panes.editor?.classList.toggle('d-none', isPreview);
            panes.preview?.classList.toggle('d-none', !isPreview);

            if (isPreview) {
                renderPreview(preview, contentInput.value);
            }
        });
    });
}

function initBlogPublishSwitch(form) {
    const input = form.querySelector('#blog-is-published');
    const label = form.querySelector('label[for="blog-is-published"]');

    if (!input || !label) {
        return;
    }

    const sync = () => {
        label.textContent = input.checked ? 'Published' : 'Draft';
    };

    input.addEventListener('change', sync);
    sync();
}

function initBlogTags(form) {
    initTagInput(form.querySelector('[data-blog-tags]'));
}

function renderPreview(previewEl, html) {
    const content = (html || '').trim();

    previewEl.innerHTML = content !== ''
        ? content
        : '<p class="text-muted mb-0">Nothing to preview yet. Start writing in the editor tab.</p>';
}
