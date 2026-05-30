import '../css/blog-rich-content.css';
import { initDashboardToasts } from './dashboard-toast';
import { initAjaxModal } from './admin-ajax-modal';
import { initConfirmModals } from './admin-confirm-modal';
import { initDashListPartials } from './dashboard-list-partial';
import { initBlogEditor } from './blog-editor';
import { initTagInputs } from './tag-input';

function initDashboardThemeToggle() {
    const button = document.querySelector('[data-dash-theme-toggle]');

    if (!button) {
        return;
    }

    const syncIcons = () => {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        button.querySelector('.dash-theme-icon-light')?.classList.toggle('d-none', isDark);
        button.querySelector('.dash-theme-icon-dark')?.classList.toggle('d-none', ! isDark);
        button.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        button.setAttribute('title', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    };

    syncIcons();

    button.addEventListener('click', () => {
        window.__dashThemeToggle?.();
        syncIcons();

        if (typeof window.lucide !== 'undefined') {
            window.lucide.createIcons();
        }
    });

    document.addEventListener('dash-theme-changed', syncIcons);
}

document.addEventListener('DOMContentLoaded', () => {
    initDashboardThemeToggle();
    initDashboardToasts();
    initConfirmModals();
    initDashListPartials();
    initBlogEditor();
    initTagInputs();

    initAjaxModal({
        id: 'volume-create-modal',
        openAttribute: 'data-volume-create-open',
        submitForm: 'volume-create-form',
    });

    initAjaxModal({
        id: 'edition-create-modal',
        openAttribute: 'data-edition-create-open',
        submitForm: 'edition-create-form',
    });

    initAjaxModal({
        id: 'user-roles-modal',
        openAttribute: 'data-user-roles-open',
        submitForm: 'user-roles-form',
    });

    initAjaxModal({
        id: 'edition-edit-modal',
        openAttribute: 'data-edition-edit-open',
        submitForm: 'edition-edit-form',
    });

    initAjaxModal({
        id: 'edition-add-article-modal',
        openAttribute: 'data-edition-add-article-open',
        submitForm: 'edition-add-article-form',
    });

    initAjaxModal({
        id: 'edition-publish-modal',
        openAttribute: 'data-edition-publish-open',
        submitForm: 'edition-publish-form',
    });
});
