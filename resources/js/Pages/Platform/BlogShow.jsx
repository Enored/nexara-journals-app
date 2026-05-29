import React from 'react';
import { Head } from '@inertiajs/react';
import BlogPostApp from '../../platform-ui/blog-post-app';
import '../../../css/journal-home.css';
import '../../../css/platform-home.css';
import '../../../css/blog.css';
import '../../../css/blog-post.css';
import '../../journal-ui/styles.css';

export default function PlatformBlogShow({ pageTitle, post, posts }) {
    const description = post?.excerpt ?? 'Editorials and working notes from Nexara Research Press.';

    return (
        <>
            <Head>
                <title>{pageTitle}</title>
                <meta name="description" content={description} />
            </Head>
            <BlogPostApp post={post} posts={posts} />
        </>
    );
}
