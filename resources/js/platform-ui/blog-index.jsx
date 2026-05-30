import React from 'react';
import { Search, X } from 'lucide-react';
import { SiteHeader } from '../shared/site-header';
import { Footer } from '../journal-ui/archive-sidebar';
import { PostCover } from './post-cover';

export function BlogMasthead({ categories, counts, active, onPick, query, setQuery, postCount }) {
    return (
        <section className="blog-masthead">
            <div className="container-wide">
                <div className="masthead-top">
                    <div>
                        <div className="eyebrow">From the editors · since 2009 · {postCount} posts</div>
                        <h1>Nexara Notes</h1>
                    </div>
                    <form className="notes-search" onSubmit={(e) => e.preventDefault()} role="search">
                        <span className="ic" aria-hidden>
                            <Search size={16} strokeWidth={1.5} />
                        </span>
                        <input
                            type="text"
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            placeholder="Search Notes — title, author, topic…"
                            aria-label="Search Notes"
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
                </div>
                <p className="blurb">
                    Editorials, explainers, and working notes from the people who run our journals — on open
                    access, peer review, reproducibility, and the craft of scholarly publishing.
                </p>
                <div className="cat-rail">
                    {categories.map((c) => (
                        <button
                            key={c}
                            type="button"
                            className={active === c ? 'active' : ''}
                            onClick={() => onPick(c)}
                        >
                            {c}
                            <span className="n">{counts[c] != null ? counts[c] : ''}</span>
                        </button>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function LeadPost({ post, onOpen }) {
    if (!post) {
        return null;
    }

    return (
        <section className="lead-band">
            <div className="container-wide">
                <article
                    className="lead-post"
                    onClick={() => onOpen(post)}
                    onKeyDown={(e) => e.key === 'Enter' && onOpen(post)}
                    role="button"
                    tabIndex={0}
                >
                    <div className="lead-cover">
                        <PostCover src={post.cover} alt={post.title} />
                    </div>
                    <div className="lead-body">
                        <div className="lead-tag">
                            <span>★ Latest</span>
                            <span className="pin">· {post.category}</span>
                        </div>
                        <h2>{post.title}</h2>
                        <p className="lead-excerpt">{post.excerpt}</p>
                        <div className="lead-meta">
                            <span className="by">{post.author}</span>
                            {post.authorAffiliation && (
                                <span className="role">· {post.authorAffiliation}</span>
                            )}
                            <span className="dot">·</span>
                            <span>{post.date}</span>
                            <span className="dot">·</span>
                            <span className="rt">{post.readTime} min read</span>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    );
}

export function PostCard({ post, onOpen }) {
    return (
        <article
            className="post-card"
            onClick={() => onOpen(post)}
            onKeyDown={(e) => e.key === 'Enter' && onOpen(post)}
            role="button"
            tabIndex={0}
        >
            <div className="pcover">
                <PostCover src={post.cover} alt={post.title} />
            </div>
            <div className="pcat">{post.category}</div>
            <h3>{post.title}</h3>
            <p className="pexcerpt">{post.excerpt}</p>
            <div className="pmeta">
                <span className="by">{post.author}</span>
                <span className="dot">·</span>
                <span>{post.date}</span>
                <span className="dot">·</span>
                <span className="rt">{post.readTime} min</span>
            </div>
        </article>
    );
}

export function SubscribeStrip({ onSubscribe }) {
    return (
        <section className="subscribe-strip">
            <div className="container-wide">
                <div className="inner">
                    <div>
                        <h2>New posts, when they&apos;re worth your time.</h2>
                        <p>
                            We send Notes roughly twice a month — never more. No tracking, no list-selling,
                            unsubscribe in one click.
                        </p>
                    </div>
                    <form
                        className="form"
                        onSubmit={(e) => {
                            e.preventDefault();
                            onSubscribe();
                        }}
                    >
                        <input placeholder="name@university.edu" />
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </section>
    );
}

export function BlogIndex({
    items,
    categories,
    counts,
    active,
    query,
    setQuery,
    onPick,
    onClearSearch,
    total,
    hasMore,
    loading,
    onLoadMore,
    onOpenPost,
    onToast,
}) {
    const searching = (query ?? '').trim().length > 0;

    const visibleCategories = categories.filter(
        (c) => c === 'All' || (counts[c] ?? 0) > 0 || c === active,
    );

    // The first post is featured as the lead while browsing (not while searching).
    const lead = !searching ? items[0] : null;
    const rest = lead ? items.slice(1) : items;

    let barTitle;
    if (searching) {
        barTitle = (
            <span>
                Results for <em>&ldquo;{query.trim()}&rdquo;</em>
                {active !== 'All' && <span> in {active}</span>}
            </span>
        );
    } else {
        barTitle = active === 'All' ? 'All posts' : <span>Posts in <em>{active}</em></span>;
    }

    return (
        <div className="app">
            <SiteHeader view="blogs" />
            <BlogMasthead
                categories={visibleCategories}
                counts={counts}
                active={active}
                onPick={onPick}
                query={query}
                setQuery={setQuery}
                postCount={counts.All ?? total}
            />

            {!searching && <LeadPost post={lead} onOpen={onOpenPost} />}

            <section className="posts-section">
                <div className="container-wide">
                    <div className="bar">
                        <h2>{barTitle}</h2>
                        <span className="count">
                            {total} {total === 1 ? 'post' : 'posts'}
                        </span>
                    </div>

                    {items.length === 0 && !loading && (
                        <div className="posts-empty">
                            {searching ? (
                                <span>
                                    No posts match <em>&ldquo;{query.trim()}&rdquo;</em>
                                    {active !== 'All' && <span> in {active}</span>}.{' '}
                                    <button type="button" className="link-btn" onClick={onClearSearch}>
                                        Clear search
                                    </button>
                                </span>
                            ) : (
                                'No posts in this category yet.'
                            )}
                        </div>
                    )}

                    {rest.length > 0 && (
                        <div className="post-grid">
                            {rest.map((post) => (
                                <PostCard key={post.id} post={post} onOpen={onOpenPost} />
                            ))}
                        </div>
                    )}

                    {hasMore && (
                        <div className="load-more">
                            <button
                                type="button"
                                className="btn ghost"
                                onClick={onLoadMore}
                                disabled={loading}
                            >
                                {loading ? 'Loading…' : 'Load more posts'}
                            </button>
                        </div>
                    )}

                    {!hasMore && !searching && items.length > 0 && (
                        <div className="load-more">
                            <button
                                type="button"
                                className="btn ghost"
                                onClick={() => onToast("You're all caught up — that's every post")}
                            >
                                You&apos;re all caught up
                            </button>
                        </div>
                    )}
                </div>
            </section>

            <SubscribeStrip onSubscribe={() => onToast('Subscribed (demo)')} />
            <Footer />
        </div>
    );
}
