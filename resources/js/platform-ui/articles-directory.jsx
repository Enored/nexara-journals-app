import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Check, ChevronLeft, ChevronRight, Search, X } from 'lucide-react';
import { SiteHeader } from '../shared/site-header';
import { Footer } from '../journal-ui/archive-sidebar';

const SORT_LABELS = {
    newest: 'Newest first',
    oldest: 'Oldest first',
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

function PaperRow({ paper }) {
    const dateLabel = new Date(paper.date).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });

    const content = (
        <>
            <div className="pr-tags">
                <span className="pr-jrnl">{paper.journal}</span>
                {paper.oa && <span className="tag oa">Open access</span>}
                <span className="tag type">{paper.type}</span>
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

function ArtMasthead({ press }) {
    const articleWord = press.articles === '1' ? 'Article' : 'Articles';

    return (
        <section className="art-masthead">
            <div className="container-wide">
                <div className="eyebrow">
                    {press.name} · {press.articles} {articleWord} · all open access
                </div>
                <h1>Articles</h1>
                <p className="blurb">
                    Search and filter peer-reviewed research across all {press.journals} {press.name}
                    {' '}journals — every paper free to read, download, and reuse.
                </p>
            </div>
        </section>
    );
}

/** Compact, windowed page list with ellipses, e.g. 1 … 4 5 [6] 7 8 … 20. */
function pageWindow(current, last) {
    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1);
    }

    const pages = new Set([1, last, current, current - 1, current + 1]);
    const sorted = [...pages].filter((p) => p >= 1 && p <= last).sort((a, b) => a - b);

    const out = [];
    let prev = 0;
    for (const p of sorted) {
        if (p - prev > 1) {
            out.push('…');
        }
        out.push(p);
        prev = p;
    }
    return out;
}

function Pagination({ page, lastPage, hrefFor }) {
    if (lastPage <= 1) {
        return null;
    }

    const items = pageWindow(page, lastPage);

    return (
        <nav className="art-pagination" aria-label="Article pages">
            {page > 1 ? (
                <Link
                    href={hrefFor(page - 1)}
                    rel="prev"
                    className="pg-arrow"
                    only={['papers', 'pagination', 'filters']}
                    preserveState
                    aria-label="Previous page"
                >
                    <ChevronLeft size={16} strokeWidth={1.5} aria-hidden />
                </Link>
            ) : (
                <span className="pg-arrow disabled" aria-hidden>
                    <ChevronLeft size={16} strokeWidth={1.5} />
                </span>
            )}

            {items.map((item, i) =>
                item === '…' ? (
                    // eslint-disable-next-line react/no-array-index-key
                    <span key={`gap-${i}`} className="pg-gap">
                        …
                    </span>
                ) : item === page ? (
                    <span key={item} className="pg-num active" aria-current="page">
                        {item}
                    </span>
                ) : (
                    <Link
                        key={item}
                        href={hrefFor(item)}
                        className="pg-num"
                        only={['papers', 'pagination', 'filters']}
                        preserveState
                    >
                        {item}
                    </Link>
                ),
            )}

            {page < lastPage ? (
                <Link
                    href={hrefFor(page + 1)}
                    rel="next"
                    className="pg-arrow"
                    only={['papers', 'pagination', 'filters']}
                    preserveState
                    aria-label="Next page"
                >
                    <ChevronRight size={16} strokeWidth={1.5} aria-hidden />
                </Link>
            ) : (
                <span className="pg-arrow disabled" aria-hidden>
                    <ChevronRight size={16} strokeWidth={1.5} />
                </span>
            )}
        </nav>
    );
}

export default function ArticlesDirectory({ press, papers, pagination, filters, facets }) {
    const { platform } = usePage().props;
    const articlesUrl = platform.urls.articles;

    const typeFacets = facets?.types ?? [];
    const yearFacets = facets?.years ?? [];

    const [query, setQuery] = useState(filters.q ?? '');
    const didMount = useRef(false);

    // Server is the source of truth for active filters; build params from it.
    const buildParams = (next, page) => {
        const params = {};
        if (next.q) {
            params.q = next.q;
        }
        if (next.types.length) {
            params.types = next.types;
        }
        if (next.years.length) {
            params.years = next.years;
        }
        if (next.sort && next.sort !== 'newest') {
            params.sort = next.sort;
        }
        if (page > 1) {
            params.page = page;
        }
        return params;
    };

    const navigate = (next, page = 1, { preserveScroll = false } = {}) => {
        router.get(articlesUrl, buildParams(next, page), {
            only: ['papers', 'pagination', 'filters'],
            preserveState: true,
            preserveScroll,
            replace: true,
        });
    };

    // Keep the search box in sync when filters change elsewhere (chip removal, reset).
    useEffect(() => {
        setQuery(filters.q ?? '');
    }, [filters.q]);

    // Debounced search → page 1, without scrolling the user away while typing.
    useEffect(() => {
        if (!didMount.current) {
            didMount.current = true;
            return;
        }
        const handle = setTimeout(() => {
            const trimmed = query.trim();
            if (trimmed !== (filters.q ?? '')) {
                navigate({ ...filters, q: trimmed }, 1, { preserveScroll: true });
            }
        }, 350);
        return () => clearTimeout(handle);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [query]);

    const toggleType = (value) => {
        const types = filters.types.includes(value)
            ? filters.types.filter((t) => t !== value)
            : [...filters.types, value];
        navigate({ ...filters, types }, 1);
    };

    const toggleYear = (value) => {
        const years = filters.years.includes(value)
            ? filters.years.filter((y) => y !== value)
            : [...filters.years, value];
        navigate({ ...filters, years }, 1);
    };

    const changeSort = (sort) => navigate({ ...filters, sort }, 1);

    const resetAll = () => {
        setQuery('');
        navigate({ q: '', types: [], years: [], sort: 'newest' }, 1);
    };

    const typeLabel = useMemo(() => {
        const map = {};
        typeFacets.forEach((t) => {
            map[t.value] = t.label;
        });
        return map;
    }, [typeFacets]);

    // Active filter chips — search & sort included alongside the facets.
    const chips = [
        ...(filters.q ? [{ key: 'q', label: `“${filters.q}”`, onRemove: () => navigate({ ...filters, q: '' }, 1) }] : []),
        ...(filters.sort !== 'newest'
            ? [{ key: 'sort', label: `Sort: ${SORT_LABELS[filters.sort] ?? filters.sort}`, onRemove: () => changeSort('newest') }]
            : []),
        ...filters.types.map((t) => ({ key: `type-${t}`, label: typeLabel[t] ?? t, onRemove: () => toggleType(t) })),
        ...filters.years.map((y) => ({ key: `year-${y}`, label: String(y), onRemove: () => toggleYear(y) })),
    ];

    const hrefFor = (page) => {
        const params = buildParams(filters, page);
        const sp = new URLSearchParams();
        Object.entries(params).forEach(([k, v]) => {
            if (Array.isArray(v)) {
                v.forEach((item) => sp.append(`${k}[]`, item));
            } else {
                sp.append(k, v);
            }
        });
        const qs = sp.toString();
        return qs ? `${articlesUrl}?${qs}` : articlesUrl;
    };

    const total = pagination.total;

    return (
        <div className="app">
            <SiteHeader view="articles" />
            <ArtMasthead press={press} />

            <div className="container-wide">
                <div className="art-layout">
                    <aside className="art-sidebar">
                        {typeFacets.length > 0 && (
                            <div className="facet">
                                <div className="ft">Article type</div>
                                {typeFacets.map((t) => (
                                    <FacetOpt
                                        key={t.value}
                                        label={t.label}
                                        count={t.count}
                                        on={filters.types.includes(t.value)}
                                        onToggle={() => toggleType(t.value)}
                                    />
                                ))}
                            </div>
                        )}
                        {yearFacets.length > 0 && (
                            <div className="facet">
                                <div className="ft">Year</div>
                                {yearFacets.map((y) => (
                                    <FacetOpt
                                        key={y.value}
                                        label={String(y.value)}
                                        count={y.count}
                                        on={filters.years.includes(y.value)}
                                        onToggle={() => toggleYear(y.value)}
                                    />
                                ))}
                            </div>
                        )}
                        {chips.length > 0 && (
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
                                    value={filters.sort}
                                    onChange={(e) => changeSort(e.target.value)}
                                >
                                    {Object.entries(SORT_LABELS).map(([key, label]) => (
                                        <option key={key} value={key}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {chips.length > 0 && (
                            <div className="active-chips">
                                <span className="lbl">Filters</span>
                                {chips.map((c) => (
                                    <button key={c.key} type="button" className="chip" onClick={c.onRemove}>
                                        {c.label}{' '}
                                        <span className="xx">
                                            <X size={11} strokeWidth={1.5} aria-hidden />
                                        </span>
                                    </button>
                                ))}
                            </div>
                        )}

                        <div className="res-count">
                            <b>{total}</b> {total === 1 ? 'article' : 'articles'}
                            {filters.q && (
                                <span>
                                    {' '}
                                    matching &ldquo;{filters.q}&rdquo;
                                </span>
                            )}
                        </div>

                        {papers.length === 0 ? (
                            <div className="art-empty">
                                <div className="big">No articles found</div>
                                <p>Try broadening your search or removing a filter.</p>
                                {chips.length > 0 && (
                                    <button type="button" className="btn ghost" onClick={resetAll}>
                                        Reset all filters
                                    </button>
                                )}
                            </div>
                        ) : (
                            <>
                                <div className="paper-list">
                                    {papers.map((paper) => (
                                        <PaperRow key={paper.id} paper={paper} />
                                    ))}
                                </div>
                                <Pagination
                                    page={pagination.page}
                                    lastPage={pagination.lastPage}
                                    hrefFor={hrefFor}
                                />
                            </>
                        )}
                    </main>
                </div>
            </div>

            <Footer />
        </div>
    );
}
