import React from 'react';
import { Bell, User } from 'lucide-react';
import { router, usePage } from '@inertiajs/react';
import { JournalAuthMenu } from '../journal-ui/journal-auth-menu';

export const UtilityBar = () => {
  const { platform, siteContext } = usePage().props;
  const name = platform?.name ?? 'Nexara';

  const goPlatformHome = (e) => {
    e.preventDefault();
    const url = platform.urls.home;
    if (siteContext === 'journal') {
      window.location.href = url;
      return;
    }
    router.visit(url);
  };

  return (
    <div className="util">
      <div className="container">
        <div className="left">
          <a href={platform.urls.home} className="plain" onClick={goPlatformHome}>
            {name} Platform
          </a>
          <span className="pipe" />
          <a href="#" className="hide-sm plain">
            Institutional access
          </a>
          <a href="#" className="hide-sm plain">
            Librarians
          </a>
          <a href="#" className="hide-sm plain">
            Help
          </a>
        </div>
        <div className="right">
          <a href={platform.urls.home} className="plain" onClick={goPlatformHome}>
            Browse all journals
          </a>
          <span className="pipe" />
          <JournalAuthMenu />
        </div>
      </div>
    </div>
  );
};

const PLATFORM_NAV = [
  { key: 'home', label: 'Home', href: (urls) => urls.home },
  { key: 'journals', label: 'Journals', href: (urls) => urls.journals },
  { key: 'articles', label: 'Articles', href: (urls) => urls.articles },
  { key: 'blogs', label: 'Blogs', href: (urls) => urls.blogs },
  { key: 'about', label: 'About', href: (urls) => urls.about },
];

const JOURNAL_NAV = [
  { key: 'home', label: 'Journal home' },
  { key: 'current', label: 'Current issue', href: '#' },
  { key: 'archive', label: 'Archive', href: '#' },
  { key: 'submit', label: 'Submit', href: '#' },
  { key: 'board', label: 'Editorial board', href: '#' },
];

export const WordmarkBar = ({ view = 'home', onNav }) => {
  const { platform, auth, siteContext, journal } = usePage().props;
  const name = platform?.name ?? 'Nexara';
  const isJournal = siteContext === 'journal';
  const wordmarkSub = isJournal && journal?.name ? journal.name : 'Journals';

  const goHome = (e) => {
    e.preventDefault();
    if (typeof onNav === 'function') {
      onNav(e);
      return;
    }
    if (isJournal) {
      router.visit('/');
      return;
    }
    router.visit(platform.urls.home);
  };

  const wordmarkHref = isJournal ? '/' : platform.urls.home;

  return (
    <div className="wordmark-bar" style={{ backgroundColor: 'rgb(255, 255, 255)' }}>
      <div className="container">
        <a href={wordmarkHref} className="wordmark plain" onClick={goHome}>
          {name} <span className="dot" />
          <span className="sub">{wordmarkSub}</span>
        </a>
        <nav className="main-nav">
          {isJournal ? (
            JOURNAL_NAV.map((item) => {
              const href = item.href ?? '/';
              const isActive = view === item.key || (item.key === 'home' && view === 'home');
              if (item.key === 'home') {
                return (
                  <a key={item.key} href="/" className={isActive ? 'active' : ''} onClick={goHome}>
                    {item.label}
                  </a>
                );
              }
              return (
                <a key={item.key} href={href} className={`plain ${isActive ? 'active' : ''}`}>
                  {item.label}
                </a>
              );
            })
          ) : (
            PLATFORM_NAV.map((item) => (
              <a
                key={item.key}
                href={item.href(platform.urls)}
                className={`plain ${view === item.key ? 'active' : ''}`}
              >
                {item.label}
              </a>
            ))
          )}
          <span className="divider" />
          <a href="#" className="plain" aria-label="Alerts">
            <Bell size={16} strokeWidth={1.5} aria-hidden />
          </a>
          {!auth?.user && (
            <a href={platform.urls.login} className="plain" aria-label="Sign in">
              <User size={16} strokeWidth={1.5} aria-hidden />
            </a>
          )}
        </nav>
      </div>
    </div>
  );
};
