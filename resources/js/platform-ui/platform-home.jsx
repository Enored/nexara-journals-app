import React from 'react';
import { ArrowRight, Search } from 'lucide-react';
import { usePage } from '@inertiajs/react';

export function PlatformHero({ press, query, setQuery, onSearch, featured }) {

    return (
        <section className="phero">
            <div className="container-wide">
                <div className="grid">
                    <div>
                        <div className="eyebrow">
                            Non-profit · University-funded · Est. {press.founded}
                        </div>
                        <h1>
                            Open research in the science of <em>mind, brain & behaviour.</em>
                        </h1>
                        <p className="lead">
                            {press.journals} peer-reviewed journals. {press.articles} articles.{' '}
                            <span className="oa-word">Free for everyone to read — and free for everyone to publish.</span>
                        </p>

                        <form
                            className="megasearch"
                            onSubmit={(e) => {
                                e.preventDefault();
                                onSearch();
                            }}
                        >
                            <input
                                value={query}
                                onChange={(e) => setQuery(e.target.value)}
                                placeholder="Search articles across every journal…"
                            />
                            <button type="submit">
                                <Search size={18} strokeWidth={1.5} aria-hidden />
                                Search
                            </button>
                        </form>
                        <div className="search-suggest">
                            <span>Try:</span>
                            <a
                                href="#"
                                className="plain"
                                onClick={(e) => {
                                    e.preventDefault();
                                    setQuery('predictive coding');
                                    onSearch('predictive coding');
                                }}
                            >
                                predictive coding
                            </a>
                            <a
                                href="#"
                                className="plain"
                                onClick={(e) => {
                                    e.preventDefault();
                                    setQuery('reproducibility');
                                    onSearch('reproducibility');
                                }}
                            >
                                reproducibility
                            </a>
                        </div>

                        <div className="phero-stats">
                            <div className="s">
                                <span className="v">{press.journals}</span>
                                <span className="l">Journals</span>
                            </div>
                            <div className="s">
                                <span className="v">{press.downloads12mo}</span>
                                <span className="l">Downloads · 12 mo</span>
                            </div>
                            <div className="s">
                                <span className="v">{press.countries}</span>
                                <span className="l">Countries reached</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div
                            className="feat-card"
                            style={{ cursor: featured.url ? 'pointer' : 'default', backgroundColor: '#fff' }}
                            onClick={() => featured.url && (window.location.href = featured.url)}
                            onKeyDown={(e) => e.key === 'Enter' && featured.url && (window.location.href = featured.url)}
                            role={featured.url ? 'link' : undefined}
                            tabIndex={featured.url ? 0 : undefined}
                        >
                            <div className="ftag">
                                <span>★ Featured research</span>
                                <span className="fjournal">· {featured.journal}</span>
                            </div>
                            <h2>{featured.title}</h2>
                            {featured.authors && <div className="fauthors">{featured.authors}</div>}
                            <p className="fdek">{featured.dek}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export function JournalShelf({ journals }) {
    const { platform } = usePage().props;
    const count = journals.length;

    return (
        <section className="psection psection-journals" id="journals">
            <div className="container-wide">
                <div className="head">
                    <div className="sub" style={{ marginBottom: 10 }}>
                        {count} {count === 1 ? 'journal' : 'journals'} · all diamond open access
                    </div>
                    <div className="head-title-row">
                        <h2>Featured journals</h2>
                        <a
                            href={platform.urls.journals}
                            className="plain head-link"
                        >
                            Browse all {count}
                            <ArrowRight size={16} strokeWidth={1.5} aria-hidden />
                        </a>
                    </div>
                </div>

                <div className="journal-grid">
                    {journals.slice(0, 12).map((j, i) => (
                        <a
                            key={j.id}
                            href={j.url}
                            className={`jcard plain ${i % 5 === 3 ? 'light' : ''}`}
                            style={{ textDecoration: 'none', color: 'inherit' }}
                        >
                            <div className="jcover">
                                {j.flagship && <span className="flagship-pill">Flagship</span>}
                                <div className="jtop">
                                    <div className="toprule" />
                                    <div className="abbr">{j.abbr}</div>
                                </div>
                                <div className="jbottom">
                                    <div className="jname jname-x">{j.name}</div>
                                    <div className="jfield">{j.field}</div>
                                    <div className="joa">Open access · est. {j.est}</div>
                                </div>
                            </div>
                            <div className="jmeta">
                                <span className="if">
                                    {j.impact} <span>Impact</span>
                                </span>
                                <span className="arts">
                                    {j.articles.toLocaleString()} {j.articles === 1 ? 'article' : 'articles'}
                                </span>
                            </div>
                        </a>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function SubjectsBand({ disciplines }) {
    return (
        <section className="subjects-band">
            <div className="container-wide">
                <div
                    className="head"
                    style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'end',
                        borderBottom: '1px solid var(--ink)',
                        paddingBottom: 18,
                        marginBottom: 36,
                        gap: 24,
                    }}
                >
                    <div>
                        <div
                            className="sub"
                            style={{
                                fontFamily: '"JetBrains Mono", monospace',
                                fontSize: 11,
                                letterSpacing: '0.16em',
                                textTransform: 'uppercase',
                                color: 'var(--muted)',
                                marginBottom: 10,
                            }}
                        >
                            Browse by discipline
                        </div>
                        <h2
                            style={{
                                fontFamily: 'var(--display)',
                                fontSize: 'clamp(28px, 2.8vw, 40px)',
                                fontWeight: 500,
                                letterSpacing: '-0.012em',
                                margin: 0,
                            }}
                        >
                            Find your field
                        </h2>
                    </div>
                </div>
                <div className="subject-grid">
                    {disciplines.map((d) => (
                        <button key={d.name} type="button" className="subject-cell">
                            <span className="sname">{d.name}</span>
                            <span className="scount">
                                {d.count} {d.count === 1 ? 'journal' : 'journals'}
                            </span>
                        </button>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function LatestResearch({ latest }) {
    return (
        <section className="psection">
            <div className="container-wide">
                <div className="head">
                    <div>
                        <div className="sub" style={{ marginBottom: 10 }}>
                            Across all journals · updated daily
                        </div>
                        <h2>Latest research</h2>
                    </div>
                </div>
                <div>
                    {latest.length === 0 && (
                        <p style={{ color: 'var(--muted)', fontStyle: 'italic' }}>No published articles yet.</p>
                    )}
                    {latest.map((r) => (
                        <article
                            key={r.id}
                            className="latest-row"
                            style={{ backgroundColor: '#fff', cursor: r.url ? 'pointer' : 'default' }}
                            onClick={() => r.url && (window.location.href = r.url)}
                            onKeyDown={(e) => e.key === 'Enter' && r.url && (window.location.href = r.url)}
                            role={r.url ? 'link' : undefined}
                            tabIndex={r.url ? 0 : undefined}
                        >
                            <div>
                                <div className="tags">
                                    <span className="tag oa">Open access</span>
                                    <span className="tag type">{r.type}</span>
                                    <span className="tag subject">{r.subject}</span>
                                </div>
                                <h3>{r.title}</h3>
                                <div className="authors">{r.authors}</div>
                            </div>
                            <div className="side">
                                <span className="jrnl">{r.jrnl}</span>
                                <span className="when">{r.when}</span>
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function NexaraNotes({ posts }) {
    const { platform } = usePage().props;
    const recent = [...posts]
        .sort((a, b) => new Date(b.published) - new Date(a.published))
        .slice(0, 3);

    return (
        <section className="notes-band">
            <div className="container-wide">
                <div className="head">
                    <div className="sub">From the editors · the Nexara blog</div>
                    <div className="head-title-row">
                        <h2>Recent posts</h2>
                        <a href={platform.urls.blogs} className="plain head-link">
                            All posts
                            <ArrowRight size={16} strokeWidth={1.5} aria-hidden />
                        </a>
                    </div>
                </div>

                <div className="notes-grid">
                    {recent.map((post) => (
                        <a key={post.id} href={post.url} className="note">
                            <div className="ncat">{post.category}</div>
                            <h3>{post.title}</h3>
                            <p className="ndek">{post.excerpt}</p>
                            <div className="nmeta">
                                <span className="nauthor">{post.author}</span>
                                <span className="ndot">·</span>
                                <span>{post.date}</span>
                                <span className="ndot">·</span>
                                <span className="nread">{post.readTime} min read</span>
                            </div>
                        </a>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function AuthorsCTA() {
    const { platform, auth } = usePage().props;
    const submitUrl = auth?.user ? platform.urls.dashboard : platform.urls.login;

    return (
        <section className="authors-cta">
            <div className="container-wide">
                <div className="box">
                    <div>
                        <div
                            className="sub"
                            style={{
                                fontFamily: '"JetBrains Mono", monospace',
                                fontSize: 11,
                                letterSpacing: '0.16em',
                                textTransform: 'uppercase',
                                color: 'var(--muted)',
                                marginBottom: 14,
                            }}
                        >
                            Publish with Nexara
                        </div>
                        <h2>Your work, read widely — at no cost to you or your readers.</h2>
                        <p>
                            Every Nexara journal is diamond open access. We never charge article processing fees,
                            and your paper is free to read from the day it is published under a CC BY 4.0 licence.
                        </p>
                        <div className="ctas">
                            <a href={submitUrl} className="btn plain">
                                Submit a manuscript
                                <ArrowRight size={18} strokeWidth={1.5} aria-hidden />
                            </a>
                        </div>
                    </div>
                    <div className="checks">
                        <div className="c">
                            <span className="k">$0</span>
                            <span>No article processing charge, no submission fee — ever.</span>
                        </div>
                        <div className="c">
                            <span className="k">31d</span>
                            <span>Median time to first editorial decision across the Press.</span>
                        </div>
                        <div className="c">
                            <span className="k">CC BY</span>
                            <span>You keep copyright; readers keep access.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
