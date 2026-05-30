<script>
    (() => {
        const syncScope = (scopeSelect) => {
            const container = scopeSelect.closest('form') || document;
            const group = container.querySelector('[data-announcement-journal-group]');
            const journalSelect = container.querySelector('[data-announcement-journal-select]');

            if (!group || !journalSelect) {
                return;
            }

            const perJournal = scopeSelect.value === 'per_journal';
            group.classList.toggle('d-none', !perJournal);
            journalSelect.required = perJournal;
            if (!perJournal) {
                journalSelect.value = '';
            }
        };

        // Delegated change handler — works for statically rendered and AJAX-injected forms.
        document.addEventListener('change', (event) => {
            const scopeSelect = event.target.closest('[data-announcement-scope]');
            if (scopeSelect) {
                syncScope(scopeSelect);
            }
        });

        // Initial sync for any scope selects already present on load.
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-announcement-scope]').forEach(syncScope);
        });
    })();
</script>
