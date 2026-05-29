import React, { useMemo, useState } from 'react';
import { Check, Search, X } from 'lucide-react';
import { SiteHeader } from '../shared/site-header';
import { Footer } from '../journal-ui/archive-sidebar';

const ART_SORTS = {
    newest: { label: 'Newest first', fn: (a, b) => new Date(b.date) - new Date(a.date) },
    oldest: { label: 'Oldest first', fn: (a, b) => new Date(a.date) - new Date(b.date) },
    cited: { label: 'Most cited', fn: (a, b) => b.citations - a.citations },
    read: { label: 'Most read', fn: (a, b) => b.downloads - a.downloads },
    discussed: { label: 'Most discussed', fn: (a, b) => b.altmetric - a.altmetric },
};

function FacetOpt({ label, count, on, onToggle }) {
    return (
        <button type="button" className={`facet-opt ${on ? 'on' : ''}`} onClick={onToggle}>
            <span className="box">
                {on && <Check size={11} strokeWidth={2.5} aria-hidden />}
            </span>
            <span>{label}</span>
            {count != null && <span className="ct">{count}</span>}
        </button>
    );
}

function formatDownloads(n) {
    if (n >= 1000) {
        return `${(n / 1000).toFixed(1)}k`;
    }
    return String(n);
}

function PaperRow({ paper }) {
    const dateLabel = new Date(paper.date).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });

    const content = (
        <>
            <div>
                <div className="pr-tags">
                    <span className="pr-jrnl">{paper.journal}</span>
                    {paper.oa && <span className="tag oa">Open access</span>}
                    <span className="tag type">{paper.type}</span>
                    <span className="tag subject">{paper.subject}</span>
                </div>
                <h3>{paper.title}</h3>
                <div className="pr-authors">{paper.authors}</div>
                <p className="pr-abstract">{paper.abstract}</p>
                <div className="pr-ref">
                    <span>
                        Vol. {paper.vol}, Iss. {paper.iss}
                    </span>
                    <span className="sep">·</span>
                    <span>{dateLabel}</span>
                    {paper.keywords?.length > 0 && (
                        <>
                            <span className="sep">·</span>
                            <span>{paper.keywords.slice(0, 3).join(' · ')}</span>
                        </>
                    )}
                </div>
            </div>
            <div className="pr-metrics">
                <div className="m alt">
                    <div className="v">{paper.altmetric}</div>
                    <div className="l">Altmetric</div>
                </div>
                <div className="m">
                    <div className="v">{paper.citations}</div>
                    <div className="l">Citations</div>
                </div>
                <div className="m">
                    <div className="v">{formatDownloads(paper.downloads)}</div>
                    <div className="l">Downloads</div>
                </div>
            </div>
        </>
    );

    if (paper.url) {
        return (
            <a href={paper.url} className="paper-row plain">
                {content}
            </a>
        );
    }

    return (
        <div className="paper-row" style={{ cursor: 'default' }}>
            {content}
        </div>
    );
}

function ArtMasthead({ press, paperCount }) {
    return (
        <section className="art-masthead">
            <div className="container-wide">
                <div className="eyebrow">
                    {press.name} · {paperCount} of {press.articles} articles · all open access
                </div>
                <h1>Articles</h1>
                <p className="blurb">
                    Search and filter peer-reviewed research across all {press.journals} Nexara
                    journals — every paper free to read, download, and reuse.
                </p>
            </div>
        </section>
    );
}

export default function ArticlesDirectory({ press, papers, paperTypes }) {
    const [query, setQuery] = useState('');
    const [types, setTypes] = useState([]);
    const [disciplines, setDisciplines] = useState([]);
    const [years, setYears] = useState([]);
    const [oaOnly, setOaOnly] = useState(false);
    const [sort, setSort] = useState('newest');

    const typeOpts = useMemo(
        () => paperTypes.map((t) => [t, papers.filter((p) => p.type === t).length]).filter(([, n]) => n > 0),
        [papers, paperTypes],
    );

    const discOpts = useMemo(() => {
        const counts = {};
        papers.forEach((p) => {
            counts[p.discipline] = (counts[p.discipline] || 0) + 1;
        });
        return Object.entries(counts).sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]));
    }, [papers]);

    const yearOpts = useMemo(() => {
        const counts = {};
        papers.forEach((p) => {
            counts[p.year] = (counts[p.year] || 0) + 1;
        });
        return Object.entries(counts).sort((a, b) => Number(b[0]) - Number(a[0]));
    }, [papers]);

    const toggle = (setter, arr, val) => {
        setter(arr.includes(val) ? arr.filter((x) => x !== val) : [...arr, val]);
    };

    const q = query.trim().toLowerCase();
    const filtered = useMemo(() => {
        const list = papers.filter((p) => {
            if (types.length && !types.includes(p.type)) {
                return false;
            }
            if (disciplines.length && !disciplines.includes(p.discipline)) {
                return false;
            }
            if (years.length && !years.includes(String(p.year))) {
                return false;
            }
            if (oaOnly && !p.oa) {
                return false;
            }
            if (
                q &&
                !(
                    p.title.toLowerCase().includes(q) ||
                    p.authors.toLowerCase().includes(q) ||
                    p.abstract.toLowerCase().includes(q) ||
                    p.journal.toLowerCase().includes(q) ||
                    p.subject.toLowerCase().includes(q) ||
                    (p.keywords || []).some((k) => k.toLowerCase().includes(q))
                )
            ) {
                return false;
            }
            return true;
        });
        return [...list].sort(ART_SORTS[sort].fn);
    }, [papers, types, disciplines, years, oaOnly, q, sort]);

    const activeFilters = [
        ...types.map((t) => ({ kind: 'type', val: t, label: t })),
        ...disciplines.map((d) => ({ kind: 'disc', val: d, label: d })),
        ...years.map((y) => ({ kind: 'year', val: y, label: y })),
        ...(oaOnly ? [{ kind: 'oa', val: true, label: 'Open access' }] : []),
    ];
    const anyFilter = activeFilters.length > 0 || query;

    const removeChip = (c) => {
        if (c.kind === 'type') {
            toggle(setTypes, types, c.val);
        } else if (c.kind === 'disc') {
            toggle(setDisciplines, disciplines, c.val);
        } else if (c.kind === 'year') {
            toggle(setYears, years, c.val);
        } else if (c.kind === 'oa') {
            setOaOnly(false);
        }
    };

    const resetAll = () => {
        setQuery('');
        setTypes([]);
        setDisciplines([]);
        setYears([]);
        setOaOnly(false);
    };

    return (
        <div className="app">
            <SiteHeader view="articles" />
            <ArtMasthead press={press} paperCount={papers.length} />

            <div className="container-wide">
                <div className="art-layout">
                    <aside className="art-sidebar">
                        <div className="facet">
                            <div className="ft">Article type</div>
                            {typeOpts.map(([t, n]) => (
                                <FacetOpt
                                    key={t}
                                    label={t}
                                    count={n}
                                    on={types.includes(t)}
                                    onToggle={() => toggle(setTypes, types, t)}
                                />
                            ))}
                        </div>
                        <div className="facet">
                            <div className="ft">Discipline</div>
                            {discOpts.map(([d, n]) => (
                                <FacetOpt
                                    key={d}
                                    label={d}
                                    count={n}
                                    on={disciplines.includes(d)}
                                    onToggle={() => toggle(setDisciplines, disciplines, d)}
                                />
                            ))}
                        </div>
                        <div className="facet">
                            <div className="ft">Year</div>
                            {yearOpts.map(([y, n]) => (
                                <FacetOpt
                                    key={y}
                                    label={y}
                                    count={n}
                                    on={years.includes(y)}
                                    onToggle={() => toggle(setYears, years, y)}
                                />
                            ))}
                        </div>
                        <div className="facet oa-row">
                            <div className="ft">Access</div>
                            <label
                                className={`oa-toggle ${oaOnly ? 'on' : ''}`}
                                onClick={() => setOaOnly(!oaOnly)}
                            >
                                <span className="sw" />
                                Open access only
                            </label>
                        </div>
                        {anyFilter && (
                            <button type="button" className="facet-reset" onClick={resetAll}>
                                ← Reset all filters
                            </button>
                        )}
                    </aside>

                    <main className="art-main">
                        <div className="art-controls">
                            <form className="art-search" onSubmit={(e) => e.preventDefault()} role="search">
                                <span className="ic" aria-hidden>
                                    <Search size={16} strokeWidth={1.5} />
                                </span>
                                <input
                                    type="text"
                                    value={query}
                                    onChange={(e) => setQuery(e.target.value)}
                                    placeholder="Search titles, authors, abstracts, keywords…"
                                    aria-label="Search articles"
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
                            <div className="art-sort">
                                <label htmlFor="article-sort">Sort</label>
                                <select
                                    id="article-sort"
                                    value={sort}
                                    onChange={(e) => setSort(e.target.value)}
                                >
                                    {Object.entries(ART_SORTS).map(([key, { label }]) => (
                                        <option key={key} value={key}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {activeFilters.length > 0 && (
                            <div className="active-chips">
                                <span className="lbl">Filters</span>
                                {activeFilters.map((c) => (
                                    <button
                                        key={c.kind + c.val}
                                        type="button"
                                        className="chip"
                                        onClick={() => removeChip(c)}
                                    >
                                        {c.label}{' '}
                                        <span className="xx">
                                            <X size={11} strokeWidth={1.5} aria-hidden />
                                        </span>
                                    </button>
                                ))}
                            </div>
                        )}

                        <div className="res-count">
                            <b>{filtered.length}</b> {filtered.length === 1 ? 'article' : 'articles'}
                            {query && (
                                <span>
                                    {' '}
                                    matching &ldquo;{query.trim()}&rdquo;
                                </span>
                            )}
                        </div>

                        {filtered.length === 0 ? (
                            <div className="art-empty">
                                <div className="big">No articles found</div>
                                <p>Try broadening your search or removing a filter.</p>
                                <button type="button" className="btn ghost" onClick={resetAll}>
                                    Reset all filters
                                </button>
                            </div>
                        ) : (
                            <div className="paper-list">
                                {filtered.map((paper) => (
                                    <PaperRow key={paper.id} paper={paper} />
                                ))}
                            </div>
                        )}
                    </main>
                </div>
            </div>

            <Footer />
        </div>
    );
}
