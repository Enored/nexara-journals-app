import React from 'react';
import JournalsDirectory from './journals-directory';

export default function JournalsDirApp({ press, journals, pagination, filters }) {
    return (
        <JournalsDirectory
            press={press}
            journals={journals}
            pagination={pagination}
            filters={filters}
        />
    );
}
