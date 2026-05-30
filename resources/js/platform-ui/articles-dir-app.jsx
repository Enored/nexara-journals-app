import React from 'react';
import ArticlesDirectory from './articles-directory';

export default function ArticlesDirApp({ press, papers, pagination, filters, facets }) {
    return (
        <ArticlesDirectory
            press={press}
            papers={papers}
            pagination={pagination}
            filters={filters}
            facets={facets}
        />
    );
}
