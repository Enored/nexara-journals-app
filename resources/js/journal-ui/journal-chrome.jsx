import React from 'react';
import { Icon } from './icons';

// Top utility bar + wordmark header + journal masthead + sub-nav.

export const UtilityBar = ({ onSearch, onNav }) =>
<div className="util">
    <div className="container">
      <div className="left">
        <a href="#" onClick={(e) => {e.preventDefault();onNav('home');}}>Nexara Platform</a>
        <span className="pipe"></span>
        <a href="#" className="hide-sm">Institutional access</a>
        <a href="#" className="hide-sm">Librarians</a>
        <a href="#" className="hide-sm">Help</a>
      </div>
      <div className="right">
        <a href="#" onClick={(e) => {e.preventDefault();onNav('home');}}>Browse all journals</a>
        <a href="#">For authors</a>
        <a href="#">For reviewers</a>
        <span className="pipe"></span>
        <a href="#">Sign in</a>
      </div>
    </div>
  </div>;


export const WordmarkBar = ({ onNav, view }) =>
<div className="wordmark-bar" style={{ backgroundColor: "rgb(255, 255, 255)" }}>
    <div className="container">
      <a href="#" className="wordmark plain" onClick={(e) => {e.preventDefault();onNav('home');}}>
        Nexara <span className="dot"></span>
        <span className="sub">Research Press</span>
      </a>
      <nav className="main-nav">
        <a href="#" className={view === 'home' ? 'active' : ''} onClick={(e) => {e.preventDefault();onNav('home');}}>Journal home</a>
        <a href="#">Current issue</a>
        <a href="#">Archive</a>
        <a href="#">Submit</a>
        <a href="#">Editorial board</a>
        <span className="divider"></span>
        <a href="#"><Icon name="bell" size={16} /></a>
        <a href="#"><Icon name="user" size={16} /></a>
      </nav>
    </div>
  </div>;


export const JournalMasthead = ({ onSubmit }) => {
  const j = window.JOURNAL;
  return (
    <section className="masthead" style={{ backgroundColor: "rgb(255, 255, 255)" }}>
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
              {j.name}
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
    </section>);

};

export const SubNav = ({ query, setQuery, onSearch, view, onNav }) =>
<div className="subnav" style={{ backgroundColor: "rgb(255, 255, 255)" }}>
    <div className="container">
      <nav className="subnav-links">
        {/* <a href="#" onClick={(e) => {e.preventDefault();onNav('home');}}>About</a> */}
        <a href="#" className="active">Current issue</a>
        <a href="#">Archive</a>
        <a href="#">For authors</a>
        <a href="#">Editorial board</a>
        <a href="#">Reviewers</a>
        <a href="#">Open data</a>
      </nav>
      <form className="search-inline" onSubmit={(e) => {e.preventDefault();onSearch(query);}} style={{ backgroundColor: "rgb(255, 255, 255)" }}>
        <input
        type="text"
        placeholder="Search this journal — titles, authors, DOI…"
        value={query}
        onChange={(e) => setQuery(e.target.value)} />
      
        <button type="submit"><Icon name="search" size={14} /></button>
      </form>
    </div>
  </div>;


export const OpenAccessStory = () =>
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
  </section>;
