import React from 'react';
import { Head } from '@inertiajs/react';
import ArticlesDirApp from '../../platform-ui/articles-dir-app';
import '../../../css/journal-home.css';
import '../../../css/platform-home.css';
import '../../../css/articles-directory.css';
import '../../journal-ui/styles.css';

export default function PlatformArticles({ pageTitle, press, papers, paperTypes }) {
    return (
        <>
            <Head>
                <title>{pageTitle}</title>
                <meta
                    name="description"
                    content="Search and filter peer-reviewed research across all Nexara journals."
                />
            </Head>
            <ArticlesDirApp press={press} papers={papers} paperTypes={paperTypes} />
        </>
    );
}
