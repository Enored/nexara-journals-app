// === Bundled Nexara components ===

// ---- src/data.jsx ----
// Data for Nexara — Journal of Computational Cognition
// All names, titles, DOIs, metrics are fictional.

const JOURNAL = {
  name: "Journal of Computational Cognition",
  short: "J. Comput. Cognition",
  founded: 2008,
  issn_online: "2845-1739",
  issn_print: "2845-1720",
  doiPrefix: "10.31472/jcc",
  frequency: "Quarterly · 4 issues / year",
  impact: "6.42",
  acceptance: "18%",
  citeScore: "9.1",
  timeToFirstDecision: "31 days",
  tagline: "A peer-reviewed, fully open-access venue for theoretical, computational, and empirical work at the intersection of cognition, learning systems, and neural computation.",
  currentVolume: 18,
  currentIssue: 2,
  currentDate: "May 2026",
  editorChief: { name: "Helena Vásquez", aff: "MIT, Cambridge", init: "HV" },
  editors: [
    { name: "Marek Tóth", role: "Deputy Editor", aff: "ETH Zürich", init: "MT" },
    { name: "Ayanna Okafor", role: "Methods Editor", aff: "UCL", init: "AO" },
    { name: "Rohan Iyer", role: "Statistics Editor", aff: "Stanford University", init: "RI" },
    { name: "Sofía Castellanos", role: "Reviews Editor", aff: "Max Planck Inst.", init: "SC" },
  ],
};

const SUBJECTS = [
  "Reinforcement Learning",
  "Neural Computation",
  "Memory & Recall",
  "Decision Making",
  "Bayesian Cognition",
  "Language & Symbols",
  "Computational Psychiatry",
  "Reviews & Tutorials",
];

const ARTICLES = [
  {
    id: "a1",
    type: "Research Article",
    oa: true,
    subject: "Reinforcement Learning",
    title: "Successor representations emerge from sparse predictive coding in recurrent networks",
    authors: [
      { name: "L. Marchetti", corresp: true, aff: 1 },
      { name: "P. Adebayo", aff: 1 },
      { name: "K. Schäfer", aff: 2 },
      { name: "H. Vásquez", aff: 3 },
    ],
    affiliations: [
      "Department of Computational Neuroscience, ETH Zürich",
      "Bernstein Center, Humboldt-Universität zu Berlin",
      "MIT Department of Brain & Cognitive Sciences",
    ],
    abstract: "We show that sparse predictive coding objectives, when paired with mild structural constraints on recurrent connectivity, give rise to representations functionally equivalent to the successor representation. Networks trained on naturalistic trajectories develop place-cell-like and grid-cell-like populations without explicit reward signals, suggesting a unifying account of spatial and reward-prediction coding.",
    doi: "10.31472/jcc.2026.0418",
    pages: "104–138",
    volume: 18,
    issue: 2,
    year: 2026,
    publishedOn: "May 14, 2026",
    receivedOn: "Nov 9, 2025",
    citations: 12,
    downloads: "3,184",
    altmetric: 142,
    altmetricBreakdown: { news: 4, twitter: 38, blogs: 6, policy: 1 },
    keywords: ["successor representation", "predictive coding", "hippocampus", "RL"],
  },
  {
    id: "a2",
    type: "Review",
    oa: true,
    subject: "Bayesian Cognition",
    title: "Resource-rational analysis at ten years: what we got right, what we missed, and what comes next",
    authors: [
      { name: "F. Lieberman", corresp: true, aff: 1 },
      { name: "A. Okafor", aff: 2 },
    ],
    affiliations: [
      "Princeton Neuroscience Institute",
      "Institute of Cognitive Neuroscience, UCL",
    ],
    abstract: "A decade after the introduction of resource-rational analysis, we audit its predictions against the empirical record, identify three classes of phenomena it has consistently underpredicted, and propose a reformulation in which the cost of computation is endogenous to the agent's policy. The reformulation absorbs prospect-theoretic anomalies as a special case.",
    doi: "10.31472/jcc.2026.0414",
    pages: "61–103",
    volume: 18,
    issue: 2,
    year: 2026,
    publishedOn: "May 10, 2026",
    receivedOn: "Dec 1, 2025",
    citations: 7,
    downloads: "5,902",
    altmetric: 318,
    altmetricBreakdown: { news: 11, twitter: 86, blogs: 14, policy: 2 },
    keywords: ["bounded rationality", "review", "decision making"],
  },
  {
    id: "a3",
    type: "Research Article",
    oa: true,
    subject: "Memory & Recall",
    title: "A two-process model of episodic encoding under uncertainty, validated against 11 fMRI datasets",
    authors: [
      { name: "S. Castellanos", corresp: true, aff: 1 },
      { name: "J. Park", aff: 1 },
      { name: "R. Iyer", aff: 2 },
      { name: "T. Mwangi", aff: 3 },
      { name: "B. Klein", aff: 4 },
    ],
    affiliations: [
      "Max Planck Institute for Human Cognitive and Brain Sciences",
      "Stanford Department of Statistics",
      "African Institute for Mathematical Sciences",
      "Hebrew University of Jerusalem",
    ],
    abstract: "We propose a hierarchical two-process model in which fast pattern separation and slow gist consolidation compete for representational capacity as a function of perceived encoding uncertainty. Across eleven public fMRI datasets (n = 1,841), the model recovers individual differences in subsequent-memory effects with a held-out R² of 0.41.",
    doi: "10.31472/jcc.2026.0409",
    pages: "22–60",
    volume: 18,
    issue: 2,
    year: 2026,
    publishedOn: "May 4, 2026",
    receivedOn: "Sep 22, 2025",
    citations: 9,
    downloads: "2,471",
    altmetric: 88,
    altmetricBreakdown: { news: 2, twitter: 24, blogs: 3, policy: 0 },
    keywords: ["episodic memory", "fMRI", "uncertainty", "hierarchical model"],
  },
  {
    id: "a4",
    type: "Short Report",
    oa: true,
    subject: "Neural Computation",
    title: "Phase coding survives 60-fold network dilution in liquid state machines",
    authors: [
      { name: "Y. Tanaka", corresp: true, aff: 1 },
      { name: "C. Rasmussen", aff: 2 },
    ],
    affiliations: [
      "RIKEN Center for Brain Science",
      "Niels Bohr Institute, University of Copenhagen",
    ],
    abstract: "Contrary to recent claims, we find that phase-coded readouts in liquid state machines remain accurate at dilution levels well beyond what was previously thought possible, provided the readout layer is allowed to compensate via a single learned phase offset per channel.",
    doi: "10.31472/jcc.2026.0401",
    pages: "12–21",
    volume: 18,
    issue: 2,
    year: 2026,
    publishedOn: "Apr 28, 2026",
    receivedOn: "Feb 3, 2026",
    citations: 3,
    downloads: "1,206",
    altmetric: 41,
    altmetricBreakdown: { news: 0, twitter: 18, blogs: 2, policy: 0 },
    keywords: ["liquid state machine", "phase coding", "reservoir computing"],
  },
  {
    id: "a5",
    type: "Research Article",
    oa: true,
    subject: "Computational Psychiatry",
    title: "Computational signatures of compulsive checking across 4,200 participants and three diagnostic boundaries",
    authors: [
      { name: "N. Aldridge", corresp: true, aff: 1 },
      { name: "E. Fernández", aff: 2 },
      { name: "M. Tóth", aff: 3 },
    ],
    affiliations: [
      "Institute of Psychiatry, King's College London",
      "Universidad Autónoma de Madrid",
      "ETH Zürich",
    ],
    abstract: "We fit a partially-observable Markov decision process to data from 4,200 participants spanning subclinical, clinical OCD, and remitted OCD groups. Two parameters — termination noise and belief-update gain — separate clinical from remitted participants with AUC 0.84, suggesting concrete targets for closed-loop intervention.",
    doi: "10.31472/jcc.2026.0395",
    pages: "1–11",
    volume: 18,
    issue: 2,
    year: 2026,
    publishedOn: "Apr 21, 2026",
    receivedOn: "Oct 14, 2025",
    citations: 18,
    downloads: "7,330",
    altmetric: 612,
    altmetricBreakdown: { news: 31, twitter: 142, blogs: 19, policy: 4 },
    keywords: ["OCD", "POMDP", "computational psychiatry"],
  },
  {
    id: "a6",
    type: "Tutorial",
    oa: true,
    subject: "Reviews & Tutorials",
    title: "A practitioner's guide to identifiability in cognitive model fitting",
    authors: [
      { name: "R. Iyer", corresp: true, aff: 1 },
      { name: "L. Marchetti", aff: 2 },
    ],
    affiliations: [
      "Stanford Department of Statistics",
      "ETH Zürich",
    ],
    abstract: "Half of published cognitive-model parameters fail standard identifiability checks. We provide a decision tree, runnable code, and four worked examples that together cover the diagnostic landscape most experimentalists will actually encounter.",
    doi: "10.31472/jcc.2026.0388",
    pages: "139–172",
    volume: 18,
    issue: 1,
    year: 2026,
    publishedOn: "Feb 18, 2026",
    receivedOn: "Aug 30, 2025",
    citations: 28,
    downloads: "11,402",
    altmetric: 287,
    altmetricBreakdown: { news: 6, twitter: 71, blogs: 18, policy: 0 },
    keywords: ["model fitting", "identifiability", "tutorial"],
  },
  {
    id: "a7",
    type: "Research Article",
    oa: true,
    subject: "Decision Making",
    title: "Anchoring effects vanish when participants are paid for calibration, not accuracy",
    authors: [
      { name: "E. Fernández", corresp: true, aff: 1 },
      { name: "P. Adebayo", aff: 2 },
    ],
    affiliations: [
      "Universidad Autónoma de Madrid",
      "Humboldt-Universität zu Berlin",
    ],
    abstract: "Across six preregistered experiments (n = 2,206), the canonical anchoring effect is eliminated when participants are scored on a proper calibration rule rather than on point accuracy. We argue that anchoring reflects a sensible response to ambiguous incentives, not a bias.",
    doi: "10.31472/jcc.2026.0371",
    pages: "73–112",
    volume: 18,
    issue: 1,
    year: 2026,
    publishedOn: "Feb 2, 2026",
    receivedOn: "May 17, 2025",
    citations: 41,
    downloads: "8,915",
    altmetric: 521,
    altmetricBreakdown: { news: 24, twitter: 118, blogs: 11, policy: 3 },
    keywords: ["anchoring", "calibration", "preregistration"],
  },
  {
    id: "a8",
    type: "Research Article",
    oa: true,
    subject: "Language & Symbols",
    title: "Compositional generalization in transformer language models predicts no behavioral signature in adults",
    authors: [
      { name: "B. Klein", corresp: true, aff: 1 },
      { name: "Y. Tanaka", aff: 2 },
      { name: "F. Lieberman", aff: 3 },
    ],
    affiliations: [
      "Hebrew University of Jerusalem",
      "RIKEN Center for Brain Science",
      "Princeton Neuroscience Institute",
    ],
    abstract: "We adapt three benchmark probes of compositional generalization from the NLP literature to human participants and find no behavioral signature of the model–human gap reported in synthetic studies. Implications for the use of LMs as cognitive models are discussed.",
    doi: "10.31472/jcc.2026.0362",
    pages: "33–72",
    volume: 18,
    issue: 1,
    year: 2026,
    publishedOn: "Jan 24, 2026",
    receivedOn: "Jul 8, 2025",
    citations: 22,
    downloads: "6,124",
    altmetric: 209,
    altmetricBreakdown: { news: 4, twitter: 61, blogs: 9, policy: 1 },
    keywords: ["compositionality", "language models", "behavior"],
  },
];

const ISSUES = [
  { v: 18, i: 2, year: 2026, month: "May", topic: "Predictive models of cognition", current: true, articles: 14 },
  { v: 18, i: 1, year: 2026, month: "Feb", topic: "Identifiability & inference", current: false, articles: 11 },
  { v: 17, i: 4, year: 2025, month: "Nov", topic: "Computational psychiatry", current: false, articles: 9 },
  { v: 17, i: 3, year: 2025, month: "Aug", topic: "Memory in noisy systems", current: false, articles: 12 },
  { v: 17, i: 2, year: 2025, month: "May", topic: "Language & abstraction", current: false, articles: 10 },
  { v: 17, i: 1, year: 2025, month: "Feb", topic: "Open issue", current: false, articles: 13 },
];

window.JOURNAL = JOURNAL;
window.SUBJECTS = SUBJECTS;
window.ARTICLES = ARTICLES;
window.ISSUES = ISSUES;


// ---- src/icons.jsx ----
// Tiny icon set, no external deps. All inline SVG.
const Icon = ({ name, size = 16, stroke = "currentColor", strokeWidth = 1.5, ...rest }) => {
  const p = { width: size, height: size, viewBox: "0 0 24 24", fill: "none", stroke, strokeWidth, strokeLinecap: "round", strokeLinejoin: "round", ...rest };
  switch (name) {
    case "search":  return <svg {...p}><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>;
    case "bell":    return <svg {...p}><path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9Z"/><path d="M10 21a2 2 0 0 0 4 0"/></svg>;
    case "user":    return <svg {...p}><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>;
    case "arrow":   return <svg {...p}><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>;
    case "back":    return <svg {...p}><path d="M19 12H5"/><path d="m11 18-6-6 6-6"/></svg>;
    case "download":return <svg {...p}><path d="M12 3v13"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>;
    case "quote":   return <svg {...p}><path d="M7 7h4v4H7a2 2 0 0 1 0-4Z"/><path d="M13 7h4v4h-4a2 2 0 0 1 0-4Z"/><path d="M7 11v3a3 3 0 0 0 3 3"/><path d="M13 11v3a3 3 0 0 0 3 3"/></svg>;
    case "save":    return <svg {...p}><path d="M6 3h12v18l-6-4-6 4Z"/></svg>;
    case "saved":   return <svg {...p} fill="currentColor" stroke="none"><path d="M6 3h12v18l-6-4-6 4Z"/></svg>;
    case "share":   return <svg {...p}><circle cx="6" cy="12" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="18" cy="18" r="2"/><path d="m8 11 8-4"/><path d="m8 13 8 4"/></svg>;
    case "open":    return <svg {...p}><circle cx="12" cy="14" r="4"/><path d="M16 11V8a4 4 0 0 0-8 0v3"/></svg>;
    case "ext":     return <svg {...p}><path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M20 14v6H4V4h6"/></svg>;
    case "lock":    return <svg {...p}><rect x="5" y="11" width="14" height="10" rx="1"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>;
    case "x":       return <svg {...p}><path d="M6 6 18 18"/><path d="M18 6 6 18"/></svg>;
    case "check":   return <svg {...p}><path d="m5 12 5 5L20 7"/></svg>;
    case "alert":   return <svg {...p}><path d="M3 18h18L12 4Z"/><path d="M12 10v4"/><circle cx="12" cy="17" r="0.5"/></svg>;
    case "rss":     return <svg {...p}><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="2"/></svg>;
    case "globe":   return <svg {...p}><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c3 3 3 15 0 18"/><path d="M12 3c-3 3-3 15 0 18"/></svg>;
    case "calendar":return <svg {...p}><rect x="3" y="5" width="18" height="16" rx="1"/><path d="M3 9h18"/><path d="M8 3v4"/><path d="M16 3v4"/></svg>;
    case "filter":  return <svg {...p}><path d="M4 5h16"/><path d="M7 12h10"/><path d="M10 19h4"/></svg>;
    default: return null;
  }
};

window.Icon = Icon;


// ---- src/journal-chrome.jsx ----
// Top utility bar + wordmark header + journal masthead + sub-nav.

const UtilityBar = ({ onSearch, onNav }) => (
  <div className="util">
    <div className="container">
      <div className="left">
        <a href="#" onClick={(e) => { e.preventDefault(); onNav('home'); }}>Nexara Platform</a>
        <span className="pipe"></span>
        <a href="#" className="hide-sm">Institutional access</a>
        <a href="#" className="hide-sm">Librarians</a>
        <a href="#" className="hide-sm">Help</a>
      </div>
      <div className="right">
        <a href="#" onClick={(e) => { e.preventDefault(); onNav('home'); }}>Browse all journals</a>
        <a href="#">For authors</a>
        <a href="#">For reviewers</a>
        <span className="pipe"></span>
        <a href="#">Sign in</a>
      </div>
    </div>
  </div>
);

const WordmarkBar = ({ onNav, view }) => (
  <div className="wordmark-bar">
    <div className="container">
      <a href="#" className="wordmark plain" onClick={(e) => { e.preventDefault(); onNav('home'); }}>
        Nexara <span className="dot"></span>
        <span className="sub">Research Press</span>
      </a>
      <nav className="main-nav">
        <a href="#" className={view === 'home' ? 'active' : ''} onClick={(e) => { e.preventDefault(); onNav('home'); }}>Journal home</a>
        <a href="#">Current issue</a>
        <a href="#">Archive</a>
        <a href="#">Submit</a>
        <a href="#">Editorial board</a>
        <span className="divider"></span>
        <a href="#"><Icon name="bell" size={16} /></a>
        <a href="#"><Icon name="user" size={16} /></a>
      </nav>
    </div>
  </div>
);

const JournalMasthead = ({ onSubmit }) => {
  const j = window.JOURNAL;
  return (
    <section className="masthead">
      <div className="container">
        <div className="grid">
          <div>
            <div className="eyebrow">
              <span>Journal</span>
              <span className="sep">/</span>
              <span>Established {j.founded}</span>
              <span className="sep">/</span>
              <span>{j.frequency.split(' · ')[0]}</span>
              <span className="sep">/</span>
              <span style={{ color: 'var(--oa)' }}>● Diamond open access</span>
            </div>

            <h1 className="journal-title">
              <em>Journal of</em><br/>
              Computational Cognition
            </h1>

            <p className="journal-tagline">{j.tagline}</p>

            <dl className="meta-table">
              <div className="row"><dt>Editor-in-Chief</dt><dd><strong>Dr. {j.editorChief.name}</strong>, {j.editorChief.aff}</dd></div>
              <div className="row"><dt>ISSN (online)</dt><dd className="mono">{j.issn_online}</dd></div>
              <div className="row"><dt>ISSN (print)</dt><dd className="mono">{j.issn_print}</dd></div>
              <div className="row"><dt>DOI prefix</dt><dd className="mono">{j.doiPrefix}</dd></div>
              <div className="row"><dt>Frequency</dt><dd>{j.frequency}</dd></div>
              <div className="row"><dt>Impact factor (2025)</dt><dd><strong>{j.impact}</strong> &nbsp;<span style={{ color: 'var(--muted)', fontSize: '13px' }}>↑ from 5.91 (2024)</span></dd></div>
              <div className="row"><dt>CiteScore</dt><dd><strong>{j.citeScore}</strong></dd></div>
              <div className="row"><dt>Acceptance rate</dt><dd>{j.acceptance}</dd></div>
              <div className="row"><dt>Time to first decision</dt><dd>{j.timeToFirstDecision} (median)</dd></div>
              <div className="row"><dt>Licence</dt><dd>CC BY 4.0 · No author processing charge</dd></div>
            </dl>
          </div>

          <aside className="masthead-side">
            <div className="side-card dark">
              <h3>For authors</h3>
              <p style={{ fontSize: 15, lineHeight: 1.5, margin: '0 0 16px', color: '#cfd5e6' }}>
                Submissions open year-round. Median time to first decision is <strong style={{ color: 'var(--paper)' }}>31 days</strong>; we do not charge article processing fees.
              </p>
              <button className="btn block" onClick={onSubmit} style={{ background: 'var(--paper)', color: 'var(--ink)', borderColor: 'var(--paper)' }}>
                Submit a manuscript <span className="arrow">→</span>
              </button>
              <div className="link-list" style={{ marginTop: 18 }}>
                <a href="#">Author guidelines <span className="num">.PDF</span></a>
                <a href="#">Manuscript template <span className="num">.DOCX</span></a>
                <a href="#">Peer-review policy <span className="num">v3.2</span></a>
              </div>
            </div>

            <div className="side-card">
              <h3>Recognised by</h3>
              <ul style={{ listStyle: 'none', padding: 0, margin: 0, fontSize: 13.5, lineHeight: 1.8, color: 'var(--ink-2)' }}>
                <li>· Indexed in Scopus, Web of Science, PubMed</li>
                <li>· DOAJ Seal · COPE member</li>
                <li>· cOAlition S compliant</li>
              </ul>
            </div>
          </aside>
        </div>
      </div>
    </section>
  );
};

const SubNav = ({ query, setQuery, onSearch, view, onNav }) => (
  <div className="subnav">
    <div className="container">
      <nav className="subnav-links">
        <a href="#" className={view === 'home' ? 'active' : ''} onClick={(e) => { e.preventDefault(); onNav('home'); }}>About</a>
        <a href="#" className={view === 'home' ? '' : ''}>Current issue</a>
        <a href="#">Archive</a>
        <a href="#">For authors</a>
        <a href="#">Editorial board</a>
        <a href="#">Reviewers</a>
        <a href="#">Open data</a>
      </nav>
      <form className="search-inline" onSubmit={(e) => { e.preventDefault(); onSearch(query); }}>
        <input
          type="text"
          placeholder="Search this journal — titles, authors, DOI…"
          value={query}
          onChange={(e) => setQuery(e.target.value)}
        />
        <button type="submit"><Icon name="search" size={14} /></button>
      </form>
    </div>
  </div>
);

const OpenAccessStory = () => (
  <section className="oa-story">
    <div className="container">
      <div className="grid">
        <div>
          <div className="eyebrow">Open access at JCC · est. 2008</div>
          <h2>Every paper, free to read. <em>Every author, free to publish.</em></h2>
          <p>
            <em>Computational Cognition</em> has been diamond open-access since its founding — readers pay nothing, and authors pay nothing. The journal is funded by a consortium of research universities and a single irrevocable endowment, so editorial independence does not depend on publication volume.
          </p>
          <p style={{ marginTop: 24 }}>
            <a href="#" style={{ color: 'var(--oa-bg)', borderBottomColor: 'rgba(243,231,207,0.4)' }}>Read our funding statement →</a>
          </p>
        </div>
        <div>
          <div className="oa-stats">
            <div className="oa-stat">
              <span className="num">1,247</span>
              <span className="label">Articles · since 2008</span>
            </div>
            <div className="oa-stat">
              <span className="num">2.8<span className="unit">M</span></span>
              <span className="label">Downloads · 12 mo</span>
            </div>
            <div className="oa-stat">
              <span className="num">94</span>
              <span className="label">Countries · readers</span>
            </div>
            <div className="oa-stat">
              <span className="num">$0</span>
              <span className="label">Author fee, ever</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
);

Object.assign(window, { UtilityBar, WordmarkBar, JournalMasthead, SubNav, OpenAccessStory });


// ---- src/article-list.jsx ----
// Article list with tabs, filters, saving, citation modal.

const formatAuthors = (authors) => {
  if (authors.length <= 3) return authors.map(a => a.name).join(', ');
  return `${authors[0].name}, ${authors[1].name}, ${authors[2].name}, et al.`;
};

const ArticleRow = ({ a, index, saved, onOpen, onSave, onCite }) => {
  const corresp = a.authors.find(au => au.corresp);
  return (
    <article className="article-row" onClick={() => onOpen(a)}>
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
          {a.authors.map((au, i) => (
            <React.Fragment key={au.name}>
              {i > 0 && ', '}
              <span className={au.corresp ? 'corresp' : ''}>{au.name}</span>
              {au.corresp && <sup>✉</sup>}
            </React.Fragment>
          ))}
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
    </article>
  );
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
    { key: 'discussed', label: 'Most discussed' },
  ];

  return (
    <div>
      <div className="tabs">
        {tabs.map(t => (
          <button key={t.key} className={sort === t.key ? 'active' : ''} onClick={() => setSort(t.key)}>
            {t.label}<span className="count">[{filtered.length}]</span>
          </button>
        ))}
      </div>

      <div>
        {filtered.map((a, i) => (
          <ArticleRow
            key={a.id}
            a={a}
            index={i}
            saved={saved.has(a.id)}
            onOpen={onOpen}
            onSave={onSave}
            onCite={onCite}
          />
        ))}
      </div>
    </div>
  );
};

const CiteModal = ({ article, onClose }) => {
  const [fmt, setFmt] = React.useState('apa');
  if (!article) return null;
  const a = article;
  const authorsApa = a.authors.map(au => au.name.replace(/^(\w)\. /, '$1., ').split(' ').reverse().join(', ')).join(', ').replace(/,([^,]*)$/, ', &$1');
  const formats = {
    apa: `${a.authors.map(au => au.name).join(', ')} (${a.year}). ${a.title}. Journal of Computational Cognition, ${a.volume}(${a.issue}), ${a.pages}. https://doi.org/${a.doi}`,
    chicago: `${a.authors.map(au => au.name).join(', ')}. "${a.title}." Journal of Computational Cognition ${a.volume}, no. ${a.issue} (${a.year}): ${a.pages}. https://doi.org/${a.doi}.`,
    bibtex: `@article{${a.id}${a.year},\n  title = {${a.title}},\n  author = {${a.authors.map(au => au.name).join(' and ')}},\n  journal = {Journal of Computational Cognition},\n  volume = {${a.volume}},\n  number = {${a.issue}},\n  pages = {${a.pages}},\n  year = {${a.year}},\n  doi = {${a.doi}}\n}`,
    ris: `TY  - JOUR\nTI  - ${a.title}\n${a.authors.map(au => `AU  - ${au.name}`).join('\n')}\nJO  - Journal of Computational Cognition\nVL  - ${a.volume}\nIS  - ${a.issue}\nSP  - ${a.pages.split('–')[0]}\nEP  - ${a.pages.split('–')[1] || ''}\nPY  - ${a.year}\nDO  - ${a.doi}\nER  -`,
  };

  return (
    <div className="modal-bg" onClick={onClose}>
      <div className="modal" style={{ position: 'relative' }} onClick={(e) => e.stopPropagation()}>
        <button className="close" onClick={onClose}>×</button>
        <h3>Cite this article</h3>
        <div className="sub">{a.doi}</div>
        <div className="cite-tabs">
          {Object.keys(formats).map(k => (
            <button key={k} className={fmt === k ? 'active' : ''} onClick={() => setFmt(k)}>{k.toUpperCase()}</button>
          ))}
        </div>
        <pre className="cite-body" style={{ whiteSpace: 'pre-wrap', margin: 0, fontFamily: fmt === 'bibtex' || fmt === 'ris' ? '"JetBrains Mono", monospace' : 'var(--serif)' }}>
          {formats[fmt]}
        </pre>
        <div style={{ display: 'flex', gap: 12, marginTop: 18 }}>
          <button className="btn" onClick={() => { navigator.clipboard?.writeText(formats[fmt]); }}>Copy to clipboard</button>
          <button className="btn ghost" onClick={() => alert('Export to reference manager (demo)')}>Send to Zotero / Mendeley</button>
        </div>
      </div>
    </div>
  );
};

Object.assign(window, { ArticleList, ArticleRow, CiteModal, formatAuthors });


// ---- src/archive-sidebar.jsx ----
// Issue archive timeline + journal sidebar (editorial board, stats, signals).

const IssueArchive = ({ onOpenIssue }) => {
  const issues = window.ISSUES;
  return (
    <section className="archive">
      <div className="container">
        <div className="head">
          <div>
            <div className="eyebrow" style={{ marginBottom: 14 }}>Archive · 73 issues since 2008</div>
            <h2>Issue archive</h2>
          </div>
          <div style={{ display: 'flex', gap: 12 }}>
            <button className="btn ghost small">All 73 issues →</button>
          </div>
        </div>

        <div className="timeline-rail">
          <div className="rail"></div>
          <div className="issues">
            {issues.map((iss) => (
              <button key={`${iss.v}-${iss.i}`} className={`issue ${iss.current ? 'current' : ''}`} onClick={() => onOpenIssue(iss)}>
                <div className="cover">
                  <div>
                    <div className="vi">
                      <span className="v">Vol. {iss.v} · Iss. {iss.i}</span>
                      {iss.month}<br/>{iss.year}
                    </div>
                  </div>
                  <div className="topic">{iss.topic}</div>
                  <div className="meta">{iss.articles} articles {iss.current ? '· In progress' : '· Complete'}</div>
                </div>
                <div className="dot"></div>
                <div className="yr">{iss.month} {iss.year}{iss.current ? ' · current' : ''}</div>
              </button>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

const Sidebar = () => {
  const j = window.JOURNAL;
  const board = [j.editorChief, ...j.editors];

  return (
    <aside className="sidebar">
      <section>
        <h3>Journal at a glance</h3>
        <div className="stat-block">
          <div className="stat"><div className="v">{j.impact}</div><div className="l">Impact factor</div></div>
          <div className="stat"><div className="v">{j.citeScore}</div><div className="l">CiteScore</div></div>
          <div className="stat"><div className="v">31<span style={{ fontSize: 14, color: 'var(--muted)' }}>d</span></div><div className="l">First decision</div></div>
          <div className="stat"><div className="v">{j.acceptance}</div><div className="l">Acceptance</div></div>
          <div className="stat"><div className="v">1,247</div><div className="l">Articles published</div></div>
          <div className="stat"><div className="v">94</div><div className="l">Reader countries</div></div>
        </div>
      </section>

      <section>
        <h3>Editorial board</h3>
        {board.map((e) => (
          <div className="editor-card" key={e.name}>
            <div className="editor-photo" data-init={e.init}></div>
            <div className="editor-info">
              <div className="name">{e.name}</div>
              <div className="role">{e.role || 'Editor-in-Chief'}</div>
              <div className="aff">{e.aff}</div>
            </div>
          </div>
        ))}
        <a href="#" style={{ marginTop: 14, display: 'inline-block', fontSize: 14 }}>View full board (38) →</a>
      </section>

      <section>
        <h3>Subjects</h3>
        <div style={{ display: 'flex', flexDirection: 'column', gap: 0 }}>
          {window.SUBJECTS.map((s) => (
            <a key={s} href="#" style={{
              borderBottom: '1px solid var(--rule)',
              padding: '10px 0',
              fontSize: 14.5,
              display: 'flex',
              justifyContent: 'space-between',
            }}>
              <span>{s}</span>
              <span className="mono" style={{ color: 'var(--muted)', fontSize: 11.5 }}>
                {Math.floor(20 + Math.abs(s.charCodeAt(2) - s.charCodeAt(0)) * 7)}
              </span>
            </a>
          ))}
        </div>
      </section>

      <section>
        <h3>Stay informed</h3>
        <p style={{ fontSize: 14, color: 'var(--ink-2)', margin: '0 0 14px', lineHeight: 1.55 }}>
          Get every new article delivered to your inbox on the day it's published.
        </p>
        <div className="search-inline" style={{ marginBottom: 12 }}>
          <input placeholder="name@university.edu" />
          <button>Subscribe</button>
        </div>
        <div style={{ display: 'flex', gap: 14, fontSize: 13, color: 'var(--muted)', fontFamily: '"JetBrains Mono", monospace', letterSpacing: '0.1em', textTransform: 'uppercase' }}>
          <a href="#" style={{ borderBottom: 'none', display: 'inline-flex', gap: 6, alignItems: 'center' }}><Icon name="rss" size={14}/> RSS</a>
          <span>·</span>
          <a href="#" style={{ borderBottom: 'none' }}>Mastodon</a>
          <span>·</span>
          <a href="#" style={{ borderBottom: 'none' }}>Bluesky</a>
        </div>
      </section>
    </aside>
  );
};

const Footer = () => (
  <footer className="footer">
    <div className="container-wide">
      <div className="grid">
        <div>
          <div className="brand">Nexara</div>
          <p className="brand-sub">A non-profit, university-funded publisher of diamond open-access journals in the cognitive, behavioural, and computational sciences.</p>
          <div style={{ marginTop: 18, fontFamily: '"JetBrains Mono", monospace', fontSize: 11, letterSpacing: '0.16em', textTransform: 'uppercase', color: '#8a98b6' }}>
            14 journals · 18,400 articles · since 2003
          </div>
        </div>
        <div>
          <h4>For authors</h4>
          <ul>
            <li><a href="#">Submission portal</a></li>
            <li><a href="#">Author guidelines</a></li>
            <li><a href="#">Manuscript template</a></li>
            <li><a href="#">Publication ethics</a></li>
            <li><a href="#">Open data policy</a></li>
            <li><a href="#">Preprint policy</a></li>
          </ul>
        </div>
        <div>
          <h4>For readers</h4>
          <ul>
            <li><a href="#">Browse journals</a></li>
            <li><a href="#">Subject indexes</a></li>
            <li><a href="#">Alerts &amp; RSS</a></li>
            <li><a href="#">Institutional access</a></li>
            <li><a href="#">Accessibility statement</a></li>
          </ul>
        </div>
        <div>
          <h4>About Nexara</h4>
          <ul>
            <li><a href="#">Our model</a></li>
            <li><a href="#">Consortium members</a></li>
            <li><a href="#">Governance</a></li>
            <li><a href="#">Annual report 2025</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">Press</a></li>
          </ul>
        </div>
      </div>
      <div className="colophon">
        <span>© 2026 Nexara Research Press</span>
        <span>Cambridge · Berlin · Tokyo</span>
        <span>CC BY 4.0 unless otherwise stated</span>
      </div>
    </div>
  </footer>
);

Object.assign(window, { IssueArchive, Sidebar, Footer });


// ---- src/article-detail.jsx ----
// Article detail screen (when user clicks an article).

const ArticleDetail = ({ article, onBack, onCite, onSave, saved }) => {
  if (!article) return null;
  const a = article;

  return (
    <main className="detail">
      <section className="detail-hero">
        <div className="container">
          <a href="#" className="back-link plain" onClick={(e) => { e.preventDefault(); onBack(); }}>
            ← Back to journal home
          </a>

          <div className="detail-eyebrow">
            {a.oa && <span style={{ color: 'var(--oa)' }}>● Open access</span>}
            {a.oa && <span style={{ color: 'var(--rule-strong)' }}>|</span>}
            <span>{a.type}</span>
            <span style={{ color: 'var(--rule-strong)' }}>|</span>
            <span style={{ color: 'var(--accent)' }}>{a.subject}</span>
            <span style={{ color: 'var(--rule-strong)' }}>|</span>
            <span>Vol. {a.volume}, Iss. {a.issue} · {a.year}</span>
          </div>

          <h1>{a.title}</h1>

          <div className="authors-line">
            {a.authors.map((au, i) => (
              <React.Fragment key={au.name}>
                {i > 0 && ', '}
                <strong>{au.name}</strong>
                <sup>{au.aff}{au.corresp ? ',✉' : ''}</sup>
              </React.Fragment>
            ))}
          </div>

          <div className="affiliations">
            {a.affiliations.map((aff, i) => (
              <div key={i}><sup>{i + 1}</sup> {aff}</div>
            ))}
            <div style={{ marginTop: 8 }}><sup>✉</sup> Correspondence: <span className="mono" style={{ fontStyle: 'normal' }}>{a.authors.find(au => au.corresp).name.toLowerCase().replace(/[^a-z]/g, '')}@{a.affiliations[0].split(',').pop().trim().toLowerCase().replace(/[^a-z]/g, '')}.edu</span></div>
          </div>

          <div className="top-actions">
            <button className="btn"><Icon name="download" size={16} /> Download PDF</button>
            <button className="btn ghost" onClick={() => onCite(a)}><Icon name="quote" size={16} /> Cite</button>
            <button className="btn ghost" onClick={() => onSave(a)}>
              <Icon name={saved ? 'saved' : 'save'} size={16} /> {saved ? 'Saved' : 'Save to library'}
            </button>
            <button className="btn ghost"><Icon name="share" size={16} /> Share</button>
            <button className="btn ghost"><Icon name="alert" size={16} /> Track citations</button>
          </div>
        </div>
      </section>

      <section className="detail-body">
        <div className="container">
          <div className="grid">
            <div>
              <div className="metrics-strip">
                <div className="m">
                  <div className="altmetric" style={{ width: 38, height: 38 }}><span className="v" style={{ fontSize: 11 }}>{a.altmetric}</span></div>
                  <div><div className="v">{a.altmetric}</div><div className="l">Altmetric</div></div>
                </div>
                <div className="m"><div><div className="v">{a.citations}</div><div className="l">Citations</div></div></div>
                <div className="m"><div><div className="v">{a.downloads}</div><div className="l">Downloads</div></div></div>
                <div className="m"><div><div className="v">{a.altmetricBreakdown.news + a.altmetricBreakdown.twitter + a.altmetricBreakdown.blogs}</div><div className="l">Online mentions</div></div></div>
              </div>

              <div className="section-block">
                <h2 className="sc-h">Abstract</h2>
                <p className="lead">{a.abstract}</p>
                <p style={{ fontSize: 14, color: 'var(--muted)', fontStyle: 'italic' }}>
                  <strong style={{ fontStyle: 'normal' }}>Keywords:</strong> {a.keywords.join(' · ')}
                </p>
              </div>

              <div className="section-block">
                <h2>1. Introduction</h2>
                <p>The relationship between predictive coding, representation learning, and the formation of spatial maps has emerged as one of the most productive interfaces between systems neuroscience and machine learning. A growing body of work has demonstrated that recurrent networks trained on next-state prediction in naturalistic environments give rise to populations of units whose response profiles strongly resemble those of place cells and grid cells in the rodent hippocampal–entorhinal system.</p>
                <p>Despite this convergence, the conditions under which such representations emerge — and the precise functional role they serve — remain contested. In particular, the role of sparsity constraints, of structured connectivity, and of the choice of predictive objective have each been argued for in isolation, but never compared within a unified architecture under matched training conditions.</p>
                <p>The present study addresses this gap. We train a family of recurrent networks under four predictive objectives, two sparsity regimes, and three connectivity priors, holding all other hyperparameters fixed. Across the resulting 24 conditions, we evaluate the emergence of successor-representation-like structure both behaviourally (via decoding from population activity) and mechanistically (via direct inspection of learned readouts).</p>
              </div>

              <div className="section-block">
                <h2>2. Methods</h2>
                <p>All networks consisted of 512 leaky-integrate-and-fire units with recurrent connectivity initialised from a structured prior. Networks were trained for 4 × 10⁶ steps on trajectories sampled from the DeepMind Lab "Watermaze" and "Foraging" environments. Predictive objectives spanned <em>k</em>-step return prediction (<em>k</em> ∈ {1, 5, 25, 100}), occupancy prediction at horizon 0.95, masked-state reconstruction, and a contrastive prediction term.</p>

                <div className="fig-placeholder">[ Figure 1 — Architecture diagram · drop final image here ]</div>
                <div className="fig-caption">Figure 1. Network architecture and training paradigm. (a) Recurrent core, (b) readout heads, (c) sparsity regularisation schedule.</div>

                <p>Sparsity was implemented via a hard-threshold k-WTA mechanism applied at each timestep, with k held fixed at 32 (high sparsity) or 128 (low sparsity). Connectivity priors were Gaussian-tapered, log-normal heavy-tailed, or uniform random.</p>
              </div>

              <div className="section-block">
                <h2>3. Results</h2>
                <p>Across all 24 conditions, networks reliably developed populations of units exhibiting both place-cell-like (single firing field) and grid-cell-like (hexagonally periodic firing) response profiles, with the latter emerging only under the combination of high sparsity and a tapered connectivity prior. Crucially, the readout weights learned by the predictive heads — when reorganised by their leading eigenvectors — closely matched the analytic successor representation computed from the environment's transition statistics (mean cosine similarity 0.87, 95% CI [0.84, 0.90]).</p>

                <div className="fig-placeholder">[ Figure 2 — Place / grid field emergence · drop image here ]</div>
                <div className="fig-caption">Figure 2. Emergence of place-field and grid-field response profiles across the 24 training conditions.</div>

                <p>This is consistent with the hypothesis that the successor representation is the natural fixed point of any predictive objective applied to a recurrent system under appropriate sparsity constraints, and does not require reward as a training signal.</p>
              </div>

              <div className="section-block">
                <h2>4. Discussion</h2>
                <p>Our findings unify several previously disjoint lines of work. First, they extend earlier results demonstrating place-field emergence in predictive networks by showing that the emergent representation is not merely place-like but specifically successor-like. Second, they make a prediction: experimental ablation of cortical sparsity mechanisms — for example via reduced inhibition — should preferentially disrupt grid-cell but not place-cell coding. We invite empirical tests of this prediction.</p>
              </div>

              <div className="section-block">
                <h2 className="sc-h">Data &amp; code availability</h2>
                <p style={{ fontSize: 14 }}>All training code, trained model weights, and analysis notebooks are archived at <a href="#" className="mono" style={{ fontSize: 13 }}>doi.org/10.5281/zenodo.14829110</a>. Raw trajectories are reproducible from the published configuration files.</p>
              </div>

              <div className="section-block">
                <h2 className="sc-h">References</h2>
                <ol style={{ paddingLeft: 24, fontSize: 14, color: 'var(--ink-2)', lineHeight: 1.7 }}>
                  <li>Dayan, P. (1993). Improving generalization for temporal difference learning: The successor representation. <em>Neural Computation</em>, 5(4), 613–624.</li>
                  <li>Stachenfeld, K. L., Botvinick, M. M., &amp; Gershman, S. J. (2017). The hippocampus as a predictive map. <em>Nature Neuroscience</em>, 20(11), 1643–1653.</li>
                  <li>Whittington, J. C. R., et al. (2020). The Tolman-Eichenbaum machine: Unifying space and relational memory. <em>Cell</em>, 183(5), 1249–1263.</li>
                  <li>Banino, A., et al. (2018). Vector-based navigation using grid-like representations. <em>Nature</em>, 557, 429–433.</li>
                  <li>… 47 further references omitted in this preview.</li>
                </ol>
              </div>
            </div>

            <nav className="detail-toc">
              <div style={{ fontFamily: '"JetBrains Mono", monospace', fontSize: 11, letterSpacing: '0.18em', textTransform: 'uppercase', color: 'var(--muted)', marginBottom: 14 }}>On this page</div>
              <ol>
                <li><a href="#"><span className="num">§</span>Abstract</a></li>
                <li><a href="#"><span className="num">1</span>Introduction</a></li>
                <li><a href="#"><span className="num">2</span>Methods</a></li>
                <li><a href="#"><span className="num">3</span>Results</a></li>
                <li><a href="#"><span className="num">4</span>Discussion</a></li>
                <li><a href="#"><span className="num">5</span>Data &amp; code</a></li>
                <li><a href="#"><span className="num">6</span>References</a></li>
              </ol>

              <div style={{ marginTop: 32, paddingTop: 22, borderTop: '1px solid var(--ink)' }}>
                <div style={{ fontFamily: '"JetBrains Mono", monospace', fontSize: 11, letterSpacing: '0.18em', textTransform: 'uppercase', color: 'var(--muted)', marginBottom: 14 }}>Article info</div>
                <dl className="meta-table" style={{ fontSize: 13 }}>
                  <div className="row"><dt>DOI</dt><dd className="mono" style={{ fontSize: 12 }}>{a.doi}</dd></div>
                  <div className="row"><dt>Pages</dt><dd>{a.pages}</dd></div>
                  <div className="row"><dt>Received</dt><dd>{a.receivedOn}</dd></div>
                  <div className="row"><dt>Published</dt><dd>{a.publishedOn}</dd></div>
                  <div className="row"><dt>Licence</dt><dd>CC BY 4.0</dd></div>
                </dl>
              </div>
            </nav>
          </div>
        </div>
      </section>
    </main>
  );
};

Object.assign(window, { ArticleDetail });


// ---- src/app.jsx ----
// Main app — view routing, state, tweaks panel wiring.

const { useState, useEffect, useMemo } = React;

const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{
  "accent": "#0b1a36",
  "accentWarm": "#8a1c1c",
  "density": "regular",
  "typeface": "source-serif",
  "showOpenAccess": true
}/*EDITMODE-END*/;

const ACCENT_PALETTES = [
  ["#0b1a36", "#8a1c1c"],  // navy + cinnabar (default)
  ["#0a2540", "#1f8a5b"],  // navy + forest
  ["#1a1a2e", "#b8742a"],  // ink + ochre
  ["#262626", "#7a1a4f"],  // charcoal + plum
  ["#0e3b3b", "#a04510"],  // deep teal + rust
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
      return a.title.toLowerCase().includes(q)
        || a.abstract.toLowerCase().includes(q)
        || a.authors.some(au => au.name.toLowerCase().includes(q))
        || a.subject.toLowerCase().includes(q)
        || a.doi.toLowerCase().includes(q)
        || a.keywords.some(k => k.toLowerCase().includes(q));
    };
  }, [searchTerm]);

  return (
    <div className="app">
      <UtilityBar onNav={handleNav} />
      <WordmarkBar onNav={handleNav} view={view} />

      {view === 'home' && (
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
                    onSave={handleSave}
                  />

                  <div style={{ marginTop: 32, display: 'flex', gap: 16, alignItems: 'center', padding: '24px', background: 'var(--paper-2)', border: '1px solid var(--rule-strong)' }}>
                    <div style={{ flex: 1 }}>
                      <div style={{ fontFamily: '"JetBrains Mono", monospace', fontSize: 11, letterSpacing: '0.18em', textTransform: 'uppercase', color: 'var(--muted)', marginBottom: 6 }}>
                        Continue browsing
                      </div>
                      <div style={{ fontFamily: 'var(--display)', fontSize: 20, fontWeight: 500 }}>
                        See all 1,247 articles in the archive
                      </div>
                    </div>
                    <button className="btn" onClick={() => showToast('Archive (demo)')}>View full archive <span className="arrow">→</span></button>
                  </div>
                </div>

                <Sidebar />
              </div>
            </div>
          </section>

          <IssueArchive onOpenIssue={(iss) => showToast(`Vol. ${iss.v}, Iss. ${iss.i} (demo)`)} />
        </>
      )}

      {view === 'article' && currentArticle && (
        <ArticleDetail
          article={currentArticle}
          onBack={() => handleNav('home')}
          onCite={handleCite}
          onSave={handleSave}
          saved={saved.has(currentArticle.id)}
        />
      )}

      {view === 'search' && (
        <>
          <SubNav query={query} setQuery={setQuery} onSearch={handleSearch} view={view} onNav={handleNav} />
          <section className="section search-results">
            <div className="container">
              <div className="head">
                <h2>Search results for <em>"{searchTerm}"</em></h2>
                <span className="count">{searchFilter ? window.ARTICLES.filter(searchFilter).length : 0} matches</span>
              </div>
              {searchFilter && window.ARTICLES.filter(searchFilter).length === 0 && (
                <div style={{ padding: '48px 0', textAlign: 'center', color: 'var(--muted)', fontStyle: 'italic', fontSize: 17 }}>
                  No results. Try <a href="#" onClick={(e) => { e.preventDefault(); handleSearch('memory'); setQuery('memory'); }}>memory</a>,
                  {' '}<a href="#" onClick={(e) => { e.preventDefault(); handleSearch('predictive'); setQuery('predictive'); }}>predictive</a>,
                  or <a href="#" onClick={(e) => { e.preventDefault(); handleSearch('Bayesian'); setQuery('Bayesian'); }}>Bayesian</a>.
                </div>
              )}
              <div className="two-col">
                <ArticleList
                  onOpen={handleOpenArticle}
                  onCite={handleCite}
                  saved={saved}
                  onSave={handleSave}
                  filter={searchFilter}
                />
                <Sidebar />
              </div>
            </div>
          </section>
        </>
      )}

      <Footer />

      {citeArticle && <CiteModal article={citeArticle} onClose={() => setCiteArticle(null)} />}
      {toast && <div className="toast">✓ {toast}</div>}

      <TweaksPanel>
        <TweakSection label="Theme" />
        <TweakColor
          label="Palette"
          value={[t.accent, t.accentWarm]}
          options={ACCENT_PALETTES}
          onChange={(v) => setTweak({ accent: v[0], accentWarm: v[1] })}
        />
        <TweakSection label="Typography" />
        <TweakRadio
          label="Typeface"
          value={t.typeface}
          options={[
            { value: 'source-serif', label: 'Source Serif' },
            { value: 'spectral', label: 'Spectral' },
            { value: 'newsreader', label: 'Newsreader' },
          ]}
          onChange={(v) => setTweak('typeface', v)}
        />
        <TweakSection label="Density" />
        <TweakRadio
          label="Spacing"
          value={t.density}
          options={['compact', 'regular', 'comfy']}
          onChange={(v) => setTweak('density', v)}
        />
        <TweakSection label="Sections" />
        <TweakToggle
          label="Open-access story"
          value={t.showOpenAccess}
          onChange={(v) => setTweak('showOpenAccess', v)}
        />
      </TweaksPanel>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);

