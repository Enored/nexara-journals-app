import React from 'react';
import JournalsDirectory from './journals-directory';

export default function JournalsDirApp({ press, journals }) {
    return <JournalsDirectory press={press} journals={journals} />;
}
