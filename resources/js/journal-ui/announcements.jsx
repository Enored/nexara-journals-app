import React, { useEffect, useMemo, useState } from 'react';
import { ArrowRight } from 'lucide-react';

const fmtDate = (iso) =>
    new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });

function isActiveAnnouncement(announcement) {
    if (!announcement.expiresAt) {
        return true;
    }

    return new Date(announcement.expiresAt).getTime() > Date.now();
}

function activeAnnouncements(list) {
    return list.filter(isActiveAnnouncement);
}

function sortAnnouncements(list) {
    return [...list].sort((a, b) => new Date(b.published) - new Date(a.published));
}

export function AnnouncementsSection({ announcements, onOpenAll, onOpenOne }) {
    const visible = useMemo(() => activeAnnouncements(announcements), [announcements]);
    const recent = useMemo(() => sortAnnouncements(visible).slice(0, 3), [visible]);

    if (visible.length === 0) {
        return null;
    }

    return (
        <section className="ann-band">
            <div className="container">
                <div className="head">
                    <div>
                        <div className="sub">From the editors</div>
                        <h2>
                            Announcements{' '}
                            <span className="n">{visible.length} active</span>
                        </h2>
                    </div>
                    <button type="button" className="view-all" onClick={onOpenAll}>
                        View all announcements
                        <ArrowRight size={16} strokeWidth={1.5} aria-hidden />
                    </button>
                </div>

                <div className="ann-grid" data-count={recent.length}>
                    {recent.map((a) => (
                        <button
                            type="button"
                            key={a.id}
                            className={`ann-card ann-card--${a.type}`}
                            onClick={() => onOpenOne(a)}
                        >
                            <div className="ann-meta">
                                <span className={`ann-type ${a.type}`}>{a.category}</span>
                            </div>
                            <h3>{a.title}</h3>
                            <p className="ann-excerpt">{a.excerpt}</p>
                            <div className="ann-foot">
                                <span className="date">Posted {fmtDate(a.published)}</span>
                            </div>
                        </button>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function AnnouncementsDialog({ journalName, announcements, initialId, onClose }) {
    const [filter, setFilter] = useState('All');
    const visible = useMemo(() => activeAnnouncements(announcements), [announcements]);

    const categories = useMemo(() => {
        const counts = {};
        visible.forEach((a) => {
            counts[a.category] = (counts[a.category] || 0) + 1;
        });

        return ['All', ...Object.keys(counts).sort((a, b) => counts[b] - counts[a])];
    }, [visible]);

    const sorted = useMemo(() => sortAnnouncements(visible), [visible]);
    const filtered = filter === 'All' ? sorted : sorted.filter((a) => a.category === filter);

    useEffect(() => {
        if (!initialId) {
            return;
        }
        const el = document.getElementById(`ann-${initialId}`);
        if (el) {
            el.scrollIntoView({ block: 'start', behavior: 'instant' });
        }
    }, [initialId, filter]);

    return (
        <div className="modal-bg" onClick={onClose}>
            <div className="modal ann-dialog" onClick={(e) => e.stopPropagation()}>
                <button type="button" className="close" onClick={onClose} aria-label="Close">
                    ×
                </button>

                <div className="dlg-head">
                    <div>
                        <div className="sub">{journalName}</div>
                        <h3>All announcements</h3>
                    </div>
                </div>

                <div className="ann-filters">
                    <span className="lbl">Filter</span>
                    {categories.map((t) => (
                        <button
                            type="button"
                            key={t}
                            className={`ann-fpill ${filter === t ? 'active' : ''}`}
                            onClick={() => setFilter(t)}
                        >
                            {t}
                        </button>
                    ))}
                </div>

                <div className="ann-list">
                    {filtered.length === 0 ? (
                        <div className="ann-empty">No announcements in this category.</div>
                    ) : (
                        filtered.map((a) => (
                            <article key={a.id} id={`ann-${a.id}`} className={`ann-item ann-item--${a.type}`}>
                                <div className="ann-meta">
                                    <span className={`ann-type ${a.type}`}>{a.category}</span>
                                    <span className="date">Posted {fmtDate(a.published)}</span>
                                </div>
                                <h4>{a.title}</h4>
                                <p className="ann-excerpt-full">{a.excerpt}</p>
                                <div className="ann-body">
                                    {a.body.map((para, i) => (
                                        <p key={i}>{para}</p>
                                    ))}
                                </div>
                                {a.url && (
                                    <p className="ann-link">
                                        <a href={a.url} target="_blank" rel="noopener noreferrer">
                                            Read more →
                                        </a>
                                    </p>
                                )}
                            </article>
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
