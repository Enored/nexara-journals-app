import React from 'react';
import { Head } from '@inertiajs/react';
import App from '../../journal-ui/app';
import '../../../css/journal-home.css';
import '../../journal-ui/styles.css';

export default function JournalHome({ pageTitle, journal, articles, issues, subjects }) {
    return (
        <>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={journal.tagline} />
            </Head>
            <App
                journal={journal}
                articles={articles}
                issues={issues}
                subjects={subjects}
            />
        </>
    );
}
