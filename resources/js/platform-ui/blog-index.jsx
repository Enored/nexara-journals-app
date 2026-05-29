import React, { useMemo, useState } from 'react';
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
                        <PostCover />
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
                            <span className="role">· {post.role}</span>
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
                <PostCover />
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

export function BlogIndex({ posts, categories, onOpenPost, onToast }) {
    const [active, setActive] = useState('All');
    const [query, setQuery] = useState('');

    const sorted = useMemo(
        () => [...posts].sort((a, b) => new Date(b.published) - new Date(a.published)),
        [posts],
    );

    const counts = useMemo(() => {
        const c = { All: sorted.length };
        categories.forEach((cat) => {
            if (cat !== 'All') {
                c[cat] = sorted.filter((p) => p.category === cat).length;
            }
        });
        return c;
    }, [sorted, categories]);

    const visibleCategories = categories.filter((c) => c === 'All' || (counts[c] ?? 0) > 0);

    const byCategory = active === 'All' ? sorted : sorted.filter((p) => p.category === active);
    const q = query.trim().toLowerCase();
    const searching = q.length > 0;
    const matches = (p) =>
        p.title.toLowerCase().includes(q) ||
        p.excerpt.toLowerCase().includes(q) ||
        p.author.toLowerCase().includes(q) ||
        p.category.toLowerCase().includes(q) ||
        (p.tags || []).some((t) => t.toLowerCase().includes(q)) ||
        (p.content || []).some((para) => para.toLowerCase().includes(q));

    const filtered = searching ? byCategory.filter(matches) : byCategory;
    const lead = searching ? null : filtered[0];
    const rest = searching ? filtered : filtered.slice(1);

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
                onPick={setActive}
                query={query}
                setQuery={setQuery}
                postCount={sorted.length}
            />

            {!searching && <LeadPost post={lead} onOpen={onOpenPost} />}

            <section className="posts-section">
                <div className="container-wide">
                    <div className="bar">
                        <h2>{barTitle}</h2>
                        <span className="count">
                            {filtered.length} {filtered.length === 1 ? 'post' : 'posts'}
                        </span>
                    </div>

                    {filtered.length === 0 && (
                        <div className="posts-empty">
                            {searching ? (
                                <span>
                                    No posts match <em>&ldquo;{query.trim()}&rdquo;</em>
                                    {active !== 'All' && <span> in {active}</span>}.{' '}
                                    <button
                                        type="button"
                                        className="link-btn"
                                        onClick={() => {
                                            setQuery('');
                                            setActive('All');
                                        }}
                                    >
                                        Clear search
                                    </button>
                                </span>
                            ) : (
                                'No posts in this category yet.'
                            )}
                        </div>
                    )}

                    {filtered.length > 0 && rest.length === 0 && (
                        <div className="posts-empty">This is the only post in this category so far.</div>
                    )}

                    {rest.length > 0 && (
                        <div className="post-grid">
                            {rest.map((post) => (
                                <PostCard key={post.id} post={post} onOpen={onOpenPost} />
                            ))}
                        </div>
                    )}

                    {!searching && active === 'All' && rest.length > 0 && (
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
