import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

/**
 * Turn a plain text input into a Tagify tag editor. The original input keeps a
 * comma-separated value so server-side validation/splitting stays unchanged.
 */
export function initTagInput(input, options = {}) {
    if (!input || input.dataset.tagifyReady === 'true') {
        return null;
    }

    const tagify = new Tagify(input, {
        originalInputValueFormat: (values) => values.map((item) => item.value).join(','),
        dropdown: { enabled: 0 },
        ...options,
    });

    input.dataset.tagifyReady = 'true';

    return tagify;
}

/**
 * Initialise every `[data-tag-input]` field found under `root`.
 */
export function initTagInputs(root = document) {
    root.querySelectorAll('[data-tag-input]').forEach((input) => initTagInput(input));
}
