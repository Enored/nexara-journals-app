import React, { useEffect, useRef, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { BlogIndex } from './blog-index';

export default function BlogApp({ posts, pagination, filters, categories, counts }) {
    const { platform } = usePage().props;
    const blogsUrl = platform.urls.blogs;

    const [items, setItems] = useState(posts);
    const [query, setQuery] = useState(filters.q ?? '');
    const [active, setActive] = useState(filters.category ?? 'All');
    const [page, setPage] = useState(pagination.page);
    const [hasMore, setHasMore] = useState(pagination.hasMore);
    const [total, setTotal] = useState(pagination.total);
    const [loading, setLoading] = useState(false);
    const [toast, setToast] = useState(null);

    const didMount = useRef(false);

    // Sync from server on page 1 only (initial visit, filter, back/forward).
    // Load-more keeps page > 1 props as just the new slice; appending is handled in fetchPage.
    useEffect(() => {
        if (pagination.page === 1) {
            setItems(posts);
        }
        setPage(pagination.page);
        setHasMore(pagination.hasMore);
        setTotal(pagination.total);
    }, [posts, pagination]);

    const fetchPage = (nextFilters, { append }) => {
        setLoading(true);
        const data = {
            q: nextFilters.q,
            category: nextFilters.category,
        };
        if (nextFilters.page > 1) {
            data.page = nextFilters.page;
        }

        router.get(
            blogsUrl,
            data,
            {
                only: ['posts', 'pagination'],
                preserveState: true,
                preserveScroll: true,
                replace: !append,
                preserveUrl: append,
                onSuccess: (pageObj) => {
                    const freshPosts = pageObj.props.posts;
                    const freshPagination = pageObj.props.pagination;
                    setItems((prev) => (append ? [...prev, ...freshPosts] : freshPosts));
                    setPage(freshPagination.page);
                    setHasMore(freshPagination.hasMore);
                    setTotal(freshPagination.total);
                },
                onFinish: () => setLoading(false),
            },
        );
    };

    // Debounced search.
    useEffect(() => {
        if (!didMount.current) {
            didMount.current = true;
            return;
        }
        const handle = setTimeout(() => {
            fetchPage({ q: query.trim(), category: active, page: 1 }, { append: false });
        }, 350);
        return () => clearTimeout(handle);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [query]);

    const pickCategory = (category) => {
        setActive(category);
        fetchPage({ q: query.trim(), category, page: 1 }, { append: false });
    };

    const loadMore = () => {
        if (!hasMore || loading) {
            return;
        }
        fetchPage({ q: query.trim(), category: active, page: page + 1 }, { append: true });
    };

    const clearSearch = () => {
        setQuery('');
        setActive('All');
        fetchPage({ q: '', category: 'All', page: 1 }, { append: false });
    };

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
            <BlogIndex
                items={items}
                categories={categories}
                counts={counts ?? {}}
                active={active}
                query={query}
                setQuery={setQuery}
                onPick={pickCategory}
                onClearSearch={clearSearch}
                total={total}
                hasMore={hasMore}
                loading={loading}
                onLoadMore={loadMore}
                onOpenPost={openPost}
                onToast={showToast}
            />
            {toast && <div className="toast">✓ {toast}</div>}
        </>
    );
}
