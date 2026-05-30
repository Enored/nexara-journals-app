import React, { useEffect, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Bookmark, Link2, Share2 } from 'lucide-react';
import { SiteHeader } from '../shared/site-header';
import { Footer } from '../journal-ui/archive-sidebar';
import { PostCard } from './blog-index';
import { PostCover } from './post-cover';

function ShareButtons({ onShare, className = '' }) {
    return (
        <div className={className}>
            <button type="button" onClick={() => onShare('link')} title="Copy link" aria-label="Copy link">
                <Link2 size={18} strokeWidth={1.5} />
            </button>
            <button type="button" onClick={() => onShare('share')} title="Share" aria-label="Share">
                <Share2 size={18} strokeWidth={1.5} />
            </button>
            <button type="button" onClick={() => onShare('save')} title="Save" aria-label="Save">
                <Bookmark size={18} strokeWidth={1.5} />
            </button>
        </div>
    );
}

function ShareRail({ onShare }) {
    const [sticky, setSticky] = useState(false);

    useEffect(() => {
        const onScroll = () => {
            const body = document.querySelector('.post-body');
            if (!body) {
                return;
            }
            const r = body.getBoundingClientRect();
            setSticky(r.top < 120 && r.bottom > 320);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    return (
        <div className={`share-rail${sticky ? ' sticky' : ''}`}>
            <span className="lab">Share</span>
            <ShareButtons onShare={onShare} />
        </div>
    );
}

function PostArticle({ post, onShare }) {
    const { platform } = usePage().props;
    const body = post.content?.length ? post.content : ['Full post coming soon.'];
    const pullquote =
        body.length >= 4
            ? `${body[body.length - 1].split('. ')[0].replace(/\.$/, '')}.`
            : null;

    return (
        <>
            <section className="post-head">
                <div className="container-wide">
                    <Link href={platform.urls.blogs} className="crumb">
                        <ArrowLeft size={14} strokeWidth={1.5} aria-hidden />
                        Nexara Notes
                    </Link>
                    <div className="ph-inner">
                        <div className="ph-cat">{post.category}</div>
                        <h1>{post.title}</h1>
                        <p className="standfirst">{post.excerpt}</p>
                        <div className="post-byline">
                            <div className="avatar">{post.authorInitials}</div>
                            <div className="who">
                                <span className="nm">{post.author}</span>
                                <span className="meta">
                                    {post.authorAffiliation && (
                                        <>
                                            <span>{post.authorAffiliation}</span>
                                            <span className="dot">·</span>
                                        </>
                                    )}
                                    <span>{post.date}</span>
                                    {post.updated && (
                                        <>
                                            <span className="dot">·</span>
                                            <span>Updated {post.updated}</span>
                                        </>
                                    )}
                                    <span className="dot">·</span>
                                    <span>{post.readTime} min read</span>
                                </span>
                            </div>
                            <ShareButtons onShare={onShare} className="share" />
                        </div>
                    </div>
                </div>
            </section>

            <section className="post-hero">
                <div className="container-wide">
                    <div className="hero-frame">
                        <PostCover src={post.cover} alt={post.coverCaption || post.title} />
                    </div>
                    {post.coverCaption && (
                        <div className="cap">
                            <span className="src">Cover</span>
                            <span>{post.coverCaption}</span>
                        </div>
                    )}
                </div>
            </section>

            <section className="post-body">
                <div className="container-wide">
                    <div className="measure">
                        <ShareRail onShare={onShare} />
                        {body.map((para, i) => (
                            <React.Fragment key={i}>
                                <p>{para}</p>
                                {i === 1 && pullquote && (
                                    <blockquote className="pullquote">{pullquote}</blockquote>
                                )}
                            </React.Fragment>
                        ))}
                    </div>

                    <div className="post-endmatter">
                        {post.tags?.length > 0 && (
                            <div className="post-tags">
                                {post.tags.map((tg) => (
                                    <span key={tg} className="tg">
                                        #{tg}
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="author-card">
                        <div className="av">{post.authorInitials}</div>
                        <div>
                            <div className="ac-label">Written by</div>
                            <h3 className="ac-name">{post.author}</h3>
                            {post.authorAffiliation && (
                                <div className="ac-role">{post.authorAffiliation}</div>
                            )}
                            {post.authorBio && <p className="ac-bio">{post.authorBio}</p>}
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}

function RelatedPosts({ related }) {
    const { platform } = usePage().props;

    if (!related || related.length === 0) {
        return null;
    }

    const openPost = (p) => {
        if (p.url) {
            router.visit(p.url);
        }
    };

    return (
        <section className="related">
            <div className="container-wide">
                <div className="bar">
                    <h2>More from Nexara Notes</h2>
                    <Link href={platform.urls.blogs}>All posts →</Link>
                </div>
                <div className="post-grid">
                    {related.map((p) => (
                        <PostCard key={p.id} post={p} onOpen={openPost} />
                    ))}
                </div>
            </div>
        </section>
    );
}

function PostMissing() {
    const { platform } = usePage().props;

    return (
        <div className="container-wide">
            <div className="post-missing">
                <h1>Post not found</h1>
                <p>We couldn&apos;t find that post. It may have moved, or the link may be incomplete.</p>
                <Link href={platform.urls.blogs} className="btn">
                    ← Back to Nexara Notes
                </Link>
            </div>
        </div>
    );
}

export default function BlogPostPage({ post, related, onShare }) {
    return (
        <div className="app">
            <SiteHeader view="blogs" />
            {post ? (
                <>
                    <PostArticle post={post} onShare={onShare} />
                    <RelatedPosts related={related} />
                </>
            ) : (
                <PostMissing />
            )}
            <Footer />
        </div>
    );
}
