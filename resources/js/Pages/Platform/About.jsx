import React from 'react';
import { Head } from '@inertiajs/react';
import { SiteHeader } from '../../shared/site-header';
import { Footer } from '../../journal-ui/archive-sidebar';
import '../../../css/journal-home.css';
import '../../../css/platform-home.css';
import '../../journal-ui/styles.css';

export default function PlatformAbout({ pageTitle }) {
    return (
        <>
            <Head>
                <title>{pageTitle}</title>
            </Head>
            <div className="app">
                <SiteHeader view="about" />
                <section className="psection">
                    <div className="container-wide">
                        <h2 style={{ fontFamily: 'var(--display)', fontWeight: 500, margin: 0 }}>About</h2>
                        <p style={{ color: 'var(--muted)', marginTop: 16, maxWidth: '60ch' }}>
                            Nexara Research Press is a non-profit, university-funded publisher of diamond open-access
                            journals in the cognitive, behavioural, and computational sciences.
                        </p>
                    </div>
                </section>
                <Footer />
            </div>
        </>
    );
}
