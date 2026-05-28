import React, { useEffect, useMemo, useState } from 'react';
import { ArticleList, CiteModal } from './article-list';
import { IssueArchive, Footer, Sidebar } from './archive-sidebar';
import { ArticleDetail } from './article-detail';
import { JournalMasthead, OpenAccessStory, SubNav, UtilityBar, WordmarkBar } from './journal-chrome';

export default function App() {
  const [view, setView] = useState('home'); // 'home' | 'article' | 'search'
  const [currentArticle, setCurrentArticle] = useState(null);
  const [citeArticle, setCiteArticle] = useState(null);
  const [saved, setSaved] = useState(new Set());
  const [toast, setToast] = useState(null);
  const [query, setQuery] = useState('');
  const [searchTerm, setSearchTerm] = useState('');

  // Scroll to top on view change
  useEffect(() => {
    window.scrollTo(0, 0);
  }, [view, currentArticle]);

  const showToast = (msg) => {
    setToast(msg);
    setTimeout(() => setToast(null), 1800);
  };

  const handleOpenArticle = (a) => {
    setCurrentArticle(a);
    setView('article');
  };

  const handleSave = (a) => {
    setSaved((prev) => {
      const next = new Set(prev);
      if (next.has(a.id)) {
        next.delete(a.id);
        showToast('Removed from library');
      } else {
        next.add(a.id);
        showToast('Saved to library');
      }
      return next;
    });
  };

  const handleSearch = (q) => {
    setSearchTerm(q.trim());
    setView('search');
    setCurrentArticle(null);
  };

  const handleNav = (v) => {
    setView(v);
    setCurrentArticle(null);
    setSearchTerm('');
    setQuery('');
  };

  const handleCite = (a) => setCiteArticle(a);

  // Search filter
  const searchFilter = useMemo(() => {
    if (!searchTerm) return null;
    const q = searchTerm.toLowerCase();
    return (a) => {
      return a.title.toLowerCase().includes(q) ||
      a.abstract.toLowerCase().includes(q) ||
      a.authors.some((au) => au.name.toLowerCase().includes(q)) ||
      a.subject.toLowerCase().includes(q) ||
      a.doi.toLowerCase().includes(q) ||
      a.keywords.some((k) => k.toLowerCase().includes(q));
    };
  }, [searchTerm]);

  return (
    <div className="app">
      <UtilityBar onNav={handleNav} />
      <WordmarkBar onNav={handleNav} view={view} />

      {view === 'home' &&
      <>
          <JournalMasthead onSubmit={() => showToast('Submission portal (demo)')} />
          <SubNav query={query} setQuery={setQuery} onSearch={handleSearch} view={view} onNav={handleNav} />

          {/* <OpenAccessStory /> */}

          <section className="section">
            <div className="container">
              <div className="two-col">
                <div>
                  <div className="section-head">
                    <div>
                      <div className="eyebrow" style={{ marginBottom: 8 }}>Current issue · Vol. 18 · Iss. 2 · May 2026</div>
                      <h2>Articles in this issue</h2>
                    </div>
                    <div className="meta">14 articles · published May 2026</div>
                  </div>

                  <ArticleList
                  onOpen={handleOpenArticle}
                  onCite={handleCite}
                  saved={saved}
                  onSave={handleSave} />
                

                  <div style={{ marginTop: 32, display: 'flex', gap: 16, alignItems: 'center', padding: '24px', background: '#fff', border: '1px solid var(--rule-strong)' }}>
                    <div style={{ flex: 1 }}>
                      <div style={{ fontFamily: '"JetBrains Mono", monospace', fontSize: 11, letterSpacing: '0.18em', textTransform: 'uppercase', marginBottom: 6, color: 'var(--ink-2)' }}>
                        Continue browsing
                      </div>
                      <div style={{ fontFamily: 'var(--display)', fontSize: 20, fontWeight: 500, color: 'var(--ink)' }}>
                        See all 1,247 articles in the archive
                      </div>
                    </div>
                    <button className="btn" onClick={() => showToast('Archive (demo)')} style={{ backgroundColor: "rgb(11, 26, 54)", color: "rgb(255, 255, 255)" }}>View full archive <span className="arrow">→</span></button>
                  </div>
                </div>

                <Sidebar />
              </div>
            </div>
          </section>

          <IssueArchive onOpenIssue={(iss) => showToast(`Vol. ${iss.v}, Iss. ${iss.i} (demo)`)} />
        </>
      }

      {view === 'article' && currentArticle &&
      <ArticleDetail
        article={currentArticle}
        onBack={() => handleNav('home')}
        onCite={handleCite}
        onSave={handleSave}
        saved={saved.has(currentArticle.id)} />

      }

      {view === 'search' &&
      <>
          <SubNav query={query} setQuery={setQuery} onSearch={handleSearch} view={view} onNav={handleNav} />
          <section className="section search-results">
            <div className="container">
              <div className="head">
                <h2>Search results for <em>"{searchTerm}"</em></h2>
                <span className="count">{searchFilter ? window.ARTICLES.filter(searchFilter).length : 0} matches</span>
              </div>
              {searchFilter && window.ARTICLES.filter(searchFilter).length === 0 &&
            <div style={{ padding: '48px 0', textAlign: 'center', color: 'var(--muted)', fontStyle: 'italic', fontSize: 17 }}>
                  No results. Try <a href="#" onClick={(e) => {e.preventDefault();handleSearch('memory');setQuery('memory');}}>memory</a>,
                  {' '}<a href="#" onClick={(e) => {e.preventDefault();handleSearch('predictive');setQuery('predictive');}}>predictive</a>,
                  or <a href="#" onClick={(e) => {e.preventDefault();handleSearch('Bayesian');setQuery('Bayesian');}}>Bayesian</a>.
                </div>
            }
              <div className="two-col">
                <ArticleList
                onOpen={handleOpenArticle}
                onCite={handleCite}
                saved={saved}
                onSave={handleSave}
                filter={searchFilter} />
              
                <Sidebar />
              </div>
            </div>
          </section>
        </>
      }

      <Footer />

      {citeArticle && <CiteModal article={citeArticle} onClose={() => setCiteArticle(null)} />}
      {toast && <div className="toast">✓ {toast}</div>}

    </div>);

}