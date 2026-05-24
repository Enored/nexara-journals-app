import { initDashboardToasts } from './dashboard-toast';
import { initAjaxModal } from './admin-ajax-modal';
import { initConfirmModals } from './admin-confirm-modal';
import { initDashListPartials } from './dashboard-list-partial';

document.addEventListener('DOMContentLoaded', () => {
    initDashboardToasts();
    initConfirmModals();
    initDashListPartials();

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
