// Article list with tabs, filters, saving, citation modal.

const formatAuthors = (authors) => {
  if (authors.length <= 3) return authors.map((a) => a.name).join(', ');
  return `${authors[0].name}, ${authors[1].name}, ${authors[2].name}, et al.`;
};

const ArticleRow = ({ a, index, saved, onOpen, onSave, onCite }) => {
  const corresp = a.authors.find((au) => au.corresp);
  return (
    <article className="article-row" onClick={() => onOpen(a)} style={{ backgroundColor: "rgb(255, 255, 255)" }}>
      <div className="marker">
        [{String(index + 1).padStart(2, '0')}]
      </div>

      <div className="body">
        <div className="tags">
          {a.oa && <span className="tag oa">Open access</span>}
          <span className="tag type">{a.type}</span>
          <span className="tag subject">{a.subject}</span>
          <span className="tag" style={{ color: 'var(--muted-2)' }}>· Vol. {a.volume}, Iss. {a.issue}</span>
        </div>

        <h3>{a.title}</h3>

        <div className="authors">
          {a.authors.map((au, i) =>
          <React.Fragment key={au.name}>
              {i > 0 && ', '}
              <span className={au.corresp ? 'corresp' : ''}>{au.name}</span>
              {au.corresp && <sup>✉</sup>}
            </React.Fragment>
          )}
        </div>

        <p className="abstract">{a.abstract}</p>

        <div className="ref-line">
          <span className="doi">DOI: {a.doi}</span>
          <span className="sep">|</span>
          <span>pp. {a.pages}</span>
          <span className="sep">|</span>
          <span>Published {a.publishedOn}</span>
          <span className="sep">|</span>
          <span>Received {a.receivedOn}</span>
        </div>

        <div className="row-actions" onClick={(e) => e.stopPropagation()}>
          <button onClick={() => onOpen(a)}>Read article</button>
          <button onClick={() => alert('PDF download (demo)')}>PDF</button>
          <button onClick={() => onCite(a)}>Cite</button>
          <button className={saved ? 'saved' : ''} onClick={() => onSave(a)}>{saved ? '✓ Saved' : 'Save'}</button>
          <button onClick={() => alert('Share (demo)')}>Share</button>
        </div>
      </div>

      <div className="article-metrics">
        <div className="metric">
          <div className="altmetric"><span className="v">{a.altmetric}</span></div>
          <div className="lbl">Altmetric<span className="small">attention score</span></div>
        </div>
        <div className="metric">
          <div className="num">{a.citations}</div>
          <div className="lbl">Citations<span className="small">via Crossref</span></div>
        </div>
        <div className="metric">
          <div className="num">{a.downloads}</div>
          <div className="lbl">Downloads<span className="small">since publication</span></div>
        </div>
      </div>
    </article>);

};

const ArticleList = ({ onOpen, onCite, saved, onSave, defaultSort = 'latest', filter, articles }) => {
  const [sort, setSort] = React.useState(defaultSort);
  const list = articles || window.ARTICLES;

  const sorted = React.useMemo(() => {
    const copy = [...list];
    if (sort === 'latest') return copy.sort((a, b) => new Date(b.publishedOn) - new Date(a.publishedOn));
    if (sort === 'cited') return copy.sort((a, b) => b.citations - a.citations);
    if (sort === 'downloads') return copy.sort((a, b) => parseInt(b.downloads.replace(/,/g, '')) - parseInt(a.downloads.replace(/,/g, '')));
    if (sort === 'discussed') return copy.sort((a, b) => b.altmetric - a.altmetric);
    return copy;
  }, [sort, list]);

  const filtered = filter ? sorted.filter(filter) : sorted;

  const tabs = [
  { key: 'latest', label: 'Latest published' },
  { key: 'cited', label: 'Top cited' },
  { key: 'downloads', label: 'Most downloaded' },
  { key: 'discussed', label: 'Most discussed' }];


  return (
    <div>
      <div className="tabs">
        {tabs.map((t) =>
        <button key={t.key} className={sort === t.key ? 'active' : ''} onClick={() => setSort(t.key)}>
            {t.label}<span className="count">[{filtered.length}]</span>
          </button>
        )}
      </div>

      <div>
        {filtered.map((a, i) =>
        <ArticleRow
          key={a.id}
          a={a}
          index={i}
          saved={saved.has(a.id)}
          onOpen={onOpen}
          onSave={onSave}
          onCite={onCite} />

        )}
      </div>
    </div>);

};

const CiteModal = ({ article, onClose }) => {
  const [fmt, setFmt] = React.useState('apa');
  if (!article) return null;
  const a = article;
  const authorsApa = a.authors.map((au) => au.name.replace(/^(\w)\. /, '$1., ').split(' ').reverse().join(', ')).join(', ').replace(/,([^,]*)$/, ', &$1');
  const formats = {
    apa: `${a.authors.map((au) => au.name).join(', ')} (${a.year}). ${a.title}. Journal of Computational Cognition, ${a.volume}(${a.issue}), ${a.pages}. https://doi.org/${a.doi}`,
    chicago: `${a.authors.map((au) => au.name).join(', ')}. "${a.title}." Journal of Computational Cognition ${a.volume}, no. ${a.issue} (${a.year}): ${a.pages}. https://doi.org/${a.doi}.`,
    bibtex: `@article{${a.id}${a.year},\n  title = {${a.title}},\n  author = {${a.authors.map((au) => au.name).join(' and ')}},\n  journal = {Journal of Computational Cognition},\n  volume = {${a.volume}},\n  number = {${a.issue}},\n  pages = {${a.pages}},\n  year = {${a.year}},\n  doi = {${a.doi}}\n}`,
    ris: `TY  - JOUR\nTI  - ${a.title}\n${a.authors.map((au) => `AU  - ${au.name}`).join('\n')}\nJO  - Journal of Computational Cognition\nVL  - ${a.volume}\nIS  - ${a.issue}\nSP  - ${a.pages.split('–')[0]}\nEP  - ${a.pages.split('–')[1] || ''}\nPY  - ${a.year}\nDO  - ${a.doi}\nER  -`
  };

  return (
    <div className="modal-bg" onClick={onClose}>
      <div className="modal" style={{ position: 'relative' }} onClick={(e) => e.stopPropagation()}>
        <button className="close" onClick={onClose}>×</button>
        <h3>Cite this article</h3>
        <div className="sub">{a.doi}</div>
        <div className="cite-tabs">
          {Object.keys(formats).map((k) =>
          <button key={k} className={fmt === k ? 'active' : ''} onClick={() => setFmt(k)}>{k.toUpperCase()}</button>
          )}
        </div>
        <pre className="cite-body" style={{ whiteSpace: 'pre-wrap', margin: 0, fontFamily: fmt === 'bibtex' || fmt === 'ris' ? '"JetBrains Mono", monospace' : 'var(--serif)' }}>
          {formats[fmt]}
        </pre>
        <div style={{ display: 'flex', gap: 12, marginTop: 18 }}>
          <button className="btn" onClick={() => {navigator.clipboard?.writeText(formats[fmt]);}}>Copy to clipboard</button>
          <button className="btn ghost" onClick={() => alert('Export to reference manager (demo)')}>Send to Zotero / Mendeley</button>
        </div>
      </div>
    </div>);

};

Object.assign(window, { ArticleList, ArticleRow, CiteModal, formatAuthors });