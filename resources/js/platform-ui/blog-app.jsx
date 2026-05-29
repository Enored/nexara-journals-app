import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { BlogIndex } from './blog-index';

export default function BlogApp({ posts, categories }) {
    const [toast, setToast] = useState(null);

    const showToast = (msg) => {
        setToast(msg);
        setTimeout(() => setToast(null), 1800);
    };

    const openPost = (post) => {
        if (post?.url) {
            router.visit(post.url);
        }
    };

    return (
        <>
            <BlogIndex posts={posts} categories={categories} onOpenPost={openPost} onToast={showToast} />
            {toast && <div className="toast">✓ {toast}</div>}
        </>
    );
}
