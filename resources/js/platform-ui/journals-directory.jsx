import React, { useEffect, useRef, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { LayoutGrid, List, Search, X } from 'lucide-react';
import { SiteHeader } from '../shared/site-header';
import { Footer } from '../journal-ui/archive-sidebar';

const SORTS = {
    az: 'A–Z',
    za: 'Z–A',
    newest: 'Newest',
    oldest: 'Oldest',
};

function DirMasthead({ press }) {
    return (
        <section className="dir-masthead">
            <div className="container-wide">
                <div className="eyebrow">
                    {press.name} · {press.journals} journals · all diamond open access
                </div>
                <h1>Journals</h1>
                <p className="blurb">
                    Every {press.name} journal is free to read and free to publish in. Browse the full
                    collection — search by name and scope, or sort the list to your liking.
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
        <a href={journal.url} className={`jcard plain ${index % 5 === 3 ? 'light' : ''}`}>
            <div className={`jcover ${journal.cover ? 'has-cover' : ''}`}>
                {journal.cover && (
                    <img className="jcover-img" src={journal.cover} alt="" loading="lazy" aria-hidden />
                )}
                <div className="jbottom">
                    <div className="jname jname-x">{journal.name}</div>
                    <div className="jfield">{journal.field}</div>
                    <div className="joa">Open access · est. {journal.est}</div>
                </div>
            </div>
            <div className="jmeta">
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
                <div className="jr-name">{journal.name}</div>
                <div className="jr-blurb">{journal.blurb}</div>
            </div>
            <div className="jr-arts">
                <div className="v">{journal.articles.toLocaleString()}</div>
                <div className="l">Articles</div>
            </div>
        </a>
    );
}

export default function JournalsDirectory({ press, journals, pagination, filters }) {
    const { platform } = usePage().props;
    const journalsUrl = platform.urls.journals;

    const [items, setItems] = useState(journals);
    const [query, setQuery] = useState(filters.q ?? '');
    const [sort, setSort] = useState(filters.sort ?? 'az');
    const [view, setView] = useState('grid');
    const [page, setPage] = useState(pagination.page);
    const [hasMore, setHasMore] = useState(pagination.hasMore);
    const [total, setTotal] = useState(pagination.total);
    const [loading, setLoading] = useState(false);

    const didMount = useRef(false);

    // Sync from server on page 1 (initial visit, filter, sort, back/forward).
    // Load-more keeps page > 1 props as just the new slice; appending is handled in fetchPage.
    useEffect(() => {
        if (pagination.page === 1) {
            setItems(journals);
        }
        setPage(pagination.page);
        setHasMore(pagination.hasMore);
        setTotal(pagination.total);
    }, [journals, pagination]);

    const fetchPage = (next, { append }) => {
        setLoading(true);
        const data = {};
        if (next.q) {
            data.q = next.q;
        }
        if (next.sort && next.sort !== 'az') {
            data.sort = next.sort;
        }
        if (next.page > 1) {
            data.page = next.page;
        }

        router.get(journalsUrl, data, {
            only: ['journals', 'pagination'],
            preserveState: true,
            preserveScroll: true,
            replace: !append,
            preserveUrl: append,
            onSuccess: (pageObj) => {
                const fresh = pageObj.props.journals;
                const freshPagination = pageObj.props.pagination;
                setItems((prev) => (append ? [...prev, ...fresh] : fresh));
                setPage(freshPagination.page);
                setHasMore(freshPagination.hasMore);
                setTotal(freshPagination.total);
            },
            onFinish: () => setLoading(false),
        });
    };

    // Debounced search → page 1.
    useEffect(() => {
        if (!didMount.current) {
            didMount.current = true;
            return;
        }
        const handle = setTimeout(() => {
            fetchPage({ q: query.trim(), sort, page: 1 }, { append: false });
        }, 350);
        return () => clearTimeout(handle);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [query]);

    const changeSort = (value) => {
        setSort(value);
        fetchPage({ q: query.trim(), sort: value, page: 1 }, { append: false });
    };

    const loadMore = () => {
        if (!hasMore || loading) {
            return;
        }
        fetchPage({ q: query.trim(), sort, page: page + 1 }, { append: true });
    };

    const hasFilters = Boolean(query.trim());
    const clearAll = () => {
        setQuery('');
        setSort('az');
        fetchPage({ q: '', sort: 'az', page: 1 }, { append: false });
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
                                onChange={(e) => changeSort(e.target.value)}
                            >
                                {Object.entries(SORTS).map(([key, label]) => (
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
                </div>
            </div>

            <section className="dir-results">
                <div className="container-wide">
                    <div className="resbar">
                        <div className="rc">
                            <b>{total}</b> {total === 1 ? 'journal' : 'journals'}
                            {query.trim() && (
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

                    {items.length === 0 && !loading ? (
                        <div className="dir-empty">
                            <div className="big">No journals found</div>
                            <p>Try a different search term or clear your filters.</p>
                            {hasFilters && (
                                <button type="button" className="btn ghost" onClick={clearAll}>
                                    Clear all filters
                                </button>
                            )}
                        </div>
                    ) : view === 'grid' ? (
                        <div className="dir-grid">
                            {items.map((journal, i) => (
                                <DirCard key={journal.id} journal={journal} index={i} />
                            ))}
                        </div>
                    ) : (
                        <div className="dir-list">
                            {items.map((journal) => (
                                <DirRow key={journal.id} journal={journal} />
                            ))}
                        </div>
                    )}

                    {hasMore && (
                        <div className="dir-loadmore">
                            <button
                                type="button"
                                className="btn ghost"
                                onClick={loadMore}
                                disabled={loading}
                            >
                                {loading ? 'Loading…' : 'Load more journals'}
                            </button>
                        </div>
                    )}
                </div>
            </section>

            <Footer />
        </div>
    );
}
