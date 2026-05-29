import React, { useMemo, useState } from 'react';
import { LayoutGrid, List, Search, X } from 'lucide-react';
import { SiteHeader } from '../shared/site-header';
import { Footer } from '../journal-ui/archive-sidebar';

const SORTS = {
    impact: {
        label: 'Impact factor',
        fn: (a, b) => parseImpact(b.impact) - parseImpact(a.impact),
    },
    articles: { label: 'Most articles', fn: (a, b) => b.articles - a.articles },
    az: { label: 'A–Z', fn: (a, b) => a.name.localeCompare(b.name) },
    newest: { label: 'Newest', fn: (a, b) => b.est - a.est },
    oldest: { label: 'Established', fn: (a, b) => a.est - b.est },
};

function parseImpact(value) {
    const n = parseFloat(value);
    return Number.isNaN(n) ? -1 : n;
}

function DirMasthead({ press }) {
    return (
        <section className="dir-masthead">
            <div className="container-wide">
                <div className="eyebrow">
                    {press.name} · {press.journals} journals · all diamond open access
                </div>
                <h1>Journals</h1>
                <p className="blurb">
                    Every Nexara journal is free to read and free to publish in. Browse the full
                    collection — filter by discipline, sort by impact, or search by name and scope.
                </p>
                <div className="ms-stats">
                    <div className="s">
                        <span className="v">{press.journals}</span>
                        <span className="l">Journals</span>
                    </div>
                    <div className="s">
                        <span className="v">{press.articles}</span>
                        <span className="l">Articles</span>
                    </div>
                    <div className="s">
                        <span className="v">{press.authors}</span>
                        <span className="l">Authors</span>
                    </div>
                    <div className="s">
                        <span className="v">{press.downloads12mo}</span>
                        <span className="l">Downloads · 12mo</span>
                    </div>
                </div>
            </div>
        </section>
    );
}

function DirCard({ journal, index }) {
    return (
        <a
            href={journal.url}
            className={`jcard plain ${index % 5 === 3 ? 'light' : ''}`}
        >
            <div className="jcover">
                {journal.flagship && <span className="flagship-pill">Flagship</span>}
                <div className="jtop">
                    <div className="toprule" />
                    <div className="abbr">{journal.abbr}</div>
                </div>
                <div className="jbottom">
                    <div className="jname jname-x">{journal.name}</div>
                    <div className="jfield">{journal.field}</div>
                    <div className="joa">Open access · est. {journal.est}</div>
                </div>
            </div>
            <div className="jmeta">
                <span className="if">
                    {journal.impact} <span>Impact</span>
                </span>
                <span className="arts">
                    {journal.articles.toLocaleString()}{' '}
                    {journal.articles === 1 ? 'article' : 'articles'}
                </span>
            </div>
        </a>
    );
}

function DirRow({ journal }) {
    return (
        <a href={journal.url} className="jrow plain">
            <div className="jr-spine">
                <div className="ab">{journal.abbr}</div>
                <div className="yr">&apos;{String(journal.est).slice(2)}</div>
            </div>
            <div className="jr-main">
                <div className="jr-name">
                    {journal.name}
                    {journal.flagship && <span className="jr-flag">Flagship</span>}
                </div>
                <div className="jr-blurb">{journal.blurb}</div>
            </div>
            <div className="jr-disc">{journal.discipline}</div>
            <div className="jr-if">
                <div className="v">{journal.impact}</div>
                <div className="l">Impact factor</div>
            </div>
            <div className="jr-arts">
                <div className="v">{journal.articles.toLocaleString()}</div>
                <div className="l">Articles</div>
            </div>
        </a>
    );
}

export default function JournalsDirectory({ press, journals }) {
    const [query, setQuery] = useState('');
    const [discipline, setDiscipline] = useState('All');
    const [sort, setSort] = useState('impact');
    const [view, setView] = useState('grid');
    const [oaOnly, setOaOnly] = useState(false);

    const disciplines = useMemo(() => {
        const counts = {};
        journals.forEach((j) => {
            counts[j.discipline] = (counts[j.discipline] || 0) + 1;
        });
        return Object.entries(counts).sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]));
    }, [journals]);

    const q = query.trim().toLowerCase();
    const filtered = useMemo(() => {
        let list = journals.filter((j) => {
            if (discipline !== 'All' && j.discipline !== discipline) {
                return false;
            }
            if (oaOnly) {
                return false;
            }
            if (
                q &&
                !(
                    j.name.toLowerCase().includes(q) ||
                    j.field.toLowerCase().includes(q) ||
                    j.blurb.toLowerCase().includes(q) ||
                    j.abbr.toLowerCase().includes(q) ||
                    j.discipline.toLowerCase().includes(q)
                )
            ) {
                return false;
            }
            return true;
        });
        return [...list].sort(SORTS[sort].fn);
    }, [journals, discipline, q, sort, oaOnly]);

    const hasFilters = Boolean(query || discipline !== 'All' || oaOnly);
    const clearAll = () => {
        setQuery('');
        setDiscipline('All');
        setOaOnly(false);
    };

    return (
        <div className="app">
            <SiteHeader view="journals" />
            <DirMasthead press={press} />

            <div className="dir-toolbar">
                <div className="container-wide">
                    <div className="row1">
                        <form className="dir-search" onSubmit={(e) => e.preventDefault()} role="search">
                            <span className="ic" aria-hidden>
                                <Search size={16} strokeWidth={1.5} />
                            </span>
                            <input
                                type="text"
                                value={query}
                                onChange={(e) => setQuery(e.target.value)}
                                placeholder="Search journals by name, scope, or field…"
                                aria-label="Search journals"
                            />
                            {query && (
                                <button
                                    type="button"
                                    className="clear"
                                    onClick={() => setQuery('')}
                                    aria-label="Clear search"
                                >
                                    <X size={14} strokeWidth={1.5} />
                                </button>
                            )}
                        </form>
                        <div className="dir-sort">
                            <label htmlFor="journal-sort">Sort</label>
                            <select
                                id="journal-sort"
                                value={sort}
                                onChange={(e) => setSort(e.target.value)}
                            >
                                {Object.entries(SORTS).map(([key, { label }]) => (
                                    <option key={key} value={key}>
                                        {label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="view-toggle">
                            <button
                                type="button"
                                className={view === 'grid' ? 'active' : ''}
                                onClick={() => setView('grid')}
                                aria-label="Grid view"
                            >
                                <LayoutGrid size={16} strokeWidth={1.5} />
                            </button>
                            <button
                                type="button"
                                className={view === 'list' ? 'active' : ''}
                                onClick={() => setView('list')}
                                aria-label="List view"
                            >
                                <List size={16} strokeWidth={1.5} />
                            </button>
                        </div>
                    </div>
                    <div className="dir-filters">
                        <span className="flabel">Discipline</span>
                        <button
                            type="button"
                            className={`fpill ${discipline === 'All' ? 'active' : ''}`}
                            onClick={() => setDiscipline('All')}
                        >
                            All <span className="pn">{journals.length}</span>
                        </button>
                        {disciplines.map(([name, n]) => (
                            <button
                                key={name}
                                type="button"
                                className={`fpill ${discipline === name ? 'active' : ''}`}
                                onClick={() => setDiscipline(name)}
                            >
                                {name} <span className="pn">{n}</span>
                            </button>
                        ))}
                        <label
                            className={`oa-toggle ${oaOnly ? 'on' : ''}`}
                            onClick={() => setOaOnly(!oaOnly)}
                        >
                            <span className="sw" />
                            Open access only
                        </label>
                    </div>
                </div>
            </div>

            <section className="dir-results">
                <div className="container-wide">
                    <div className="resbar">
                        <div className="rc">
                            <b>{filtered.length}</b> {filtered.length === 1 ? 'journal' : 'journals'}
                            {discipline !== 'All' && <span> · {discipline}</span>}
                            {query && (
                                <span>
                                    {' '}
                                    · matching &ldquo;{query.trim()}&rdquo;
                                </span>
                            )}
                        </div>
                        {hasFilters && (
                            <button type="button" className="clear-all" onClick={clearAll}>
                                Clear filters ×
                            </button>
                        )}
                    </div>

                    {filtered.length === 0 ? (
                        <div className="dir-empty">
                            <div className="big">No journals found</div>
                            <p>Try a different search term or clear your filters.</p>
                            <button type="button" className="btn ghost" onClick={clearAll}>
                                Clear all filters
                            </button>
                        </div>
                    ) : view === 'grid' ? (
                        <div className="dir-grid">
                            {filtered.map((journal, i) => (
                                <DirCard key={journal.id} journal={journal} index={i} />
                            ))}
                        </div>
                    ) : (
                        <div className="dir-list">
                            {filtered.map((journal) => (
                                <DirRow key={journal.id} journal={journal} />
                            ))}
                        </div>
                    )}
                </div>
            </section>

            <Footer />
        </div>
    );
}
