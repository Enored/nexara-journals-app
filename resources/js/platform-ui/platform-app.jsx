import React, { useState } from 'react';
import { SiteHeader } from '../shared/site-header';
import { Footer } from '../journal-ui/archive-sidebar';
import {
    AuthorsCTA,
    JournalShelf,
    LatestResearch,
    NexaraNotes,
    PlatformHero,
    // SubjectsBand,
} from './platform-home';

export default function PlatformApp({ press, journals, featured, latest, posts }) {
    const [query, setQuery] = useState('');

    const doSearch = (override) => {
        const q = (typeof override === 'string' ? override : query).trim();
        const target = q ? `/?q=${encodeURIComponent(q)}#journals` : '/#journals';
        window.location.href = target;
    };

    return (
        <div className="app">
            <SiteHeader view="home" />

            <PlatformHero
                press={press}
                query={query}
                setQuery={setQuery}
                onSearch={doSearch}
                featured={featured}
            />

            <JournalShelf journals={journals} />

            {/* <SubjectsBand disciplines={disciplines} /> */}

            <LatestResearch latest={latest} />

            <NexaraNotes posts={posts} />

            <AuthorsCTA />

            <Footer />
        </div>
    );
}
