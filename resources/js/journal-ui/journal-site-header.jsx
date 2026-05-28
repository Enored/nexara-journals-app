import React from 'react';
import { router, usePage } from '@inertiajs/react';
import { UtilityBar, WordmarkBar } from './journal-chrome';

export function JournalSiteHeader({ view = 'home', onNav }) {
    const { platform } = usePage().props;

    const goHome = (e) => {
        if (e?.preventDefault) {
            e.preventDefault();
        }
        if (typeof onNav === 'function') {
            onNav('home');
            return;
        }
        router.visit('/');
    };

    return (
        <>
            <UtilityBar platformName={platform.name} onNav={goHome} />
            <WordmarkBar platformName={platform.name} onNav={goHome} view={view} />
        </>
    );
}
