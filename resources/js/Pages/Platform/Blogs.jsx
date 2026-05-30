import React from 'react';
import { Head } from '@inertiajs/react';
import BlogApp from '../../platform-ui/blog-app';
import '../../../css/journal-home.css';
import '../../../css/platform-home.css';
import '../../../css/blog.css';
import '../../journal-ui/styles.css';

export default function PlatformBlogs({ pageTitle, posts, pagination, filters, categories, counts }) {
    return (
        <>
            <Head>
                <title>{pageTitle}</title>
                <meta
                    name="description"
                    content="Editorials, explainers, and working notes from Nexara Research Press."
                />
            </Head>
            <BlogApp
                posts={posts}
                pagination={pagination}
                filters={filters}
                categories={categories}
                counts={counts}
            />
        </>
    );
}
