import React, { createContext, useContext } from 'react';

const JournalDataContext = createContext(null);

export function JournalDataProvider({ value, children }) {
    return <JournalDataContext.Provider value={value}>{children}</JournalDataContext.Provider>;
}

export function useJournalData() {
    const ctx = useContext(JournalDataContext);
    if (!ctx) {
        throw new Error('useJournalData must be used inside a JournalDataProvider');
    }
    return ctx;
}
