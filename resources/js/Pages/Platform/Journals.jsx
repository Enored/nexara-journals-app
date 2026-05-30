import React from 'react';
import { Head } from '@inertiajs/react';
import JournalsDirApp from '../../platform-ui/journals-dir-app';
import '../../../css/journal-home.css';
import '../../../css/platform-home.css';
import '../../../css/journals-directory.css';
import '../../journal-ui/styles.css';

export default function PlatformJournals({ pageTitle, press, journals, pagination, filters }) {
    return (
        <>
            <Head>
                <title>{pageTitle}</title>
                <meta
                    name="description"
                    content="Browse all diamond open-access journals from Nexara Research Press."
                />
            </Head>
            <JournalsDirApp
                press={press}
                journals={journals}
                pagination={pagination}
                filters={filters}
            />
        </>
    );
}
