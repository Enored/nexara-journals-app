import React from 'react';
import { Head } from '@inertiajs/react';
import { SiteHeader } from '../../shared/site-header';
import { Footer } from '../../journal-ui/archive-sidebar';
import AboutPage from '../../platform-ui/about-page';
import '../../../css/journal-home.css';
import '../../../css/platform-home.css';
import '../../../css/about.css';
import '../../journal-ui/styles.css';

export default function PlatformAbout({ pageTitle, about }) {
    return (
        <>
            <Head>
                <title>{pageTitle}</title>
                <meta
                    name="description"
                    content="About Nexara Research Press — a non-profit, diamond open-access publisher for the cognitive, brain, and behavioural sciences."
                />
            </Head>
            <div className="app">
                <SiteHeader view="about" />
                <AboutPage about={about} />
                <Footer />
            </div>
        </>
    );
}
