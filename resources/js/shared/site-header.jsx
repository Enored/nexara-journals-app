import React from 'react';
import { router, usePage } from '@inertiajs/react';
import { UtilityBar, WordmarkBar } from './site-chrome';

export function SiteHeader({ view = 'home', onNav }) {
  const { platform, siteContext } = usePage().props;

  const goHome = (e) => {
    if (e?.preventDefault) {
      e.preventDefault();
    }
    if (typeof onNav === 'function') {
      onNav(siteContext === 'journal' ? 'home' : e);
      return;
    }
    if (siteContext === 'journal') {
      router.visit('/');
      return;
    }
    router.visit(platform.urls.home);
  };

  return (
    <>
      <UtilityBar />
      <WordmarkBar view={view} onNav={goHome} />
    </>
  );
}

/** @deprecated Use SiteHeader */
export const JournalSiteHeader = SiteHeader;
