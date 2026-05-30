<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scopeSelect = document.getElementById('announcement-scope');
        const journalGroup = document.getElementById('announcement-journal-group');
        const journalSelect = document.getElementById('announcement-journal-id');

        if (!scopeSelect || !journalGroup || !journalSelect) {
            return;
        }

        const sync = () => {
            const perJournal = scopeSelect.value === 'per_journal';
            journalGroup.classList.toggle('d-none', !perJournal);
            journalSelect.required = perJournal;
            if (!perJournal) {
                journalSelect.value = '';
            }
        };

        scopeSelect.addEventListener('change', sync);
        sync();
    });
</script>
