import React from 'react';
import ArticlesDirectory from './articles-directory';

export default function ArticlesDirApp({ press, papers, paperTypes }) {
    return <ArticlesDirectory press={press} papers={papers} paperTypes={paperTypes} />;
}
