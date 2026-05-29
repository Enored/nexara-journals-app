// Main app — view routing, state, tweaks panel wiring.

const { useState, useEffect, useMemo } = React;

const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{
  "accent": "#0b1a36",
  "accentWarm": "#8a1c1c",
  "density": "regular",
  "typeface": "source-serif",
  "showOpenAccess": true
} /*EDITMODE-END*/;

const ACCENT_PALETTES = [
["#0b1a36", "#8a1c1c"], // navy + cinnabar (default)
["#0a2540", "#1f8a5b"], // navy + forest
["#1a1a2e", "#b8742a"], // ink + ochre
["#262626", "#7a1a4f"], // charcoal + plum
["#0e3b3b", "#a04510"] // deep teal + rust
];

function App() {
  const [t, setTweak] = useTweaks(TWEAK_DEFAULTS);
  const [view, setView] = useState('home'); // 'home' | 'article' | 'search'
  const [currentArticle, setCurrentArticle] = useState(null);
  const [citeArticle, setCiteArticle] = useState(null);
  const [saved, setSaved] = useState(new Set());
  const [toast, setToast] = useState(null);
  const [query, setQuery] = useState('');
  const [searchTerm, setSearchTerm] = useState('');

  // Apply CSS vars from tweaks
  useEffect(() => {
    const root = document.documentElement;
    root.dataset.density = t.density;
    root.dataset.type = t.typeface;
    root.style.setProperty('--ink', t.accent);
    root.style.setProperty('--accent', t.accentWarm);
    root.style.setProperty('--link', t.accent);
    root.style.setProperty('--link-hover', t.accentWarm);
  }, [t]);

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

          {t.showOpenAccess && <OpenAccessStory />}

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
                

                  <div style={{ marginTop: 32, display: 'flex', gap: 16, alignItems: 'center', padding: '24px', background: 'var(--paper-2)', border: '1px solid var(--rule-strong)', backgroundColor: "rgb(11, 26, 54)" }}>
                    <div style={{ flex: 1 }}>
                      <div style={{ fontFamily: '"JetBrains Mono", monospace', fontSize: 11, letterSpacing: '0.18em', textTransform: 'uppercase', marginBottom: 6, color: "rgb(0, 0, 0)" }}>
                        Continue browsing
                      </div>
                      <div style={{ fontFamily: 'var(--display)', fontSize: 20, fontWeight: 500, color: "rgb(0, 0, 0)" }}>
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

      <TweaksPanel>
        <TweakSection label="Theme" />
        <TweakColor
          label="Palette"
          value={[t.accent, t.accentWarm]}
          options={ACCENT_PALETTES}
          onChange={(v) => setTweak({ accent: v[0], accentWarm: v[1] })} />
        
        <TweakSection label="Typography" />
        <TweakRadio
          label="Typeface"
          value={t.typeface}
          options={[
          { value: 'source-serif', label: 'Source Serif' },
          { value: 'spectral', label: 'Spectral' },
          { value: 'newsreader', label: 'Newsreader' }]
          }
          onChange={(v) => setTweak('typeface', v)} />
        
        <TweakSection label="Density" />
        <TweakRadio
          label="Spacing"
          value={t.density}
          options={['compact', 'regular', 'comfy']}
          onChange={(v) => setTweak('density', v)} />
        
        <TweakSection label="Sections" />
        <TweakToggle
          label="Open-access story"
          value={t.showOpenAccess}
          onChange={(v) => setTweak('showOpenAccess', v)} />
        
      </TweaksPanel>
    </div>);

}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);