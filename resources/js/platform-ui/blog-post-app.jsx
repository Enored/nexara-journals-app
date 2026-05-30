import React, { useEffect, useState } from 'react';
import BlogPostPage from './blog-post';

export default function BlogPostApp({ post, related }) {
    const [toast, setToast] = useState(null);
    const [progress, setProgress] = useState(0);

    useEffect(() => {
        const onScroll = () => {
            const h = document.documentElement;
            const max = h.scrollHeight - h.clientHeight;
            setProgress(max > 0 ? Math.min(100, (h.scrollTop / max) * 100) : 0);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    const showToast = (msg) => {
        setToast(msg);
        setTimeout(() => setToast(null), 1800);
    };

    const onShare = async (kind) => {
        if (kind === 'link' && navigator.clipboard?.writeText) {
            try {
                await navigator.clipboard.writeText(window.location.href);
                showToast('Link copied');
                return;
            } catch {
                // fall through to demo toast
            }
        }
        const labels = {
            link: 'Link copied',
            share: 'Share sheet (demo)',
            save: 'Saved to your library',
        };
        showToast(labels[kind] || 'Done');
    };

    return (
        <>
            <div className="read-progress" aria-hidden>
                <div className="fill" style={{ width: `${progress}%` }} />
            </div>
            <BlogPostPage post={post} related={related} onShare={onShare} />
            {toast && <div className="toast">✓ {toast}</div>}
        </>
    );
}
