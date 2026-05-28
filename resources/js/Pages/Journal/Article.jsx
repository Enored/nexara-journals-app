import React, { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { ArticleDetail } from '../../journal-ui/article-detail';
import { CiteModal } from '../../journal-ui/article-list';
import { Footer } from '../../journal-ui/archive-sidebar';
import { JournalSiteHeader } from '../../journal-ui/journal-site-header';
import '../../../css/journal-home.css';
import '../../journal-ui/styles.css';

export default function JournalArticle({ pageTitle, journal, article }) {
    const [citeArticle, setCiteArticle] = useState(null);
    const [saved, setSaved] = useState(false);
    const [toast, setToast] = useState(null);

    useEffect(() => {
        window.scrollTo(0, 0);
    }, []);

    const showToast = (msg) => {
        setToast(msg);
        setTimeout(() => setToast(null), 1800);
    };

    const handleSave = () => {
        setSaved((prev) => {
            const next = !prev;
            showToast(next ? 'Saved to library' : 'Removed from library');
            return next;
        });
    };

    return (
        <>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={article.abstract.slice(0, 160)} />
            </Head>
            <div className="app">
                <JournalSiteHeader view="article" />

                <ArticleDetail
                    article={article}
                    onBack={() => router.visit('/')}
                    onCite={(a) => setCiteArticle(a)}
                    onSave={handleSave}
                    saved={saved}
                />

                <Footer />

                {citeArticle && <CiteModal article={citeArticle} onClose={() => setCiteArticle(null)} />}
                {toast && <div className="toast">✓ {toast}</div>}
            </div>
        </>
    );
}
