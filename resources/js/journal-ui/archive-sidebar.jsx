import React from 'react';
import { ArrowRight, Rss } from 'lucide-react';
import { useJournalData } from './data-context';

// Issue archive timeline + journal sidebar (editorial board, stats, signals).

export const IssueArchive = ({ onOpenIssue }) => {
  const { issues, journal } = useJournalData();
  const totalIssues = journal?.totalIssues ?? issues.length;
  const foundedYear = journal?.founded;
  const eyebrowText = foundedYear
    ? `Archive · ${totalIssues} ${totalIssues === 1 ? 'issue' : 'issues'} since ${foundedYear}`
    : `Archive · ${totalIssues} ${totalIssues === 1 ? 'issue' : 'issues'}`;

  if (issues.length === 0) {
    return (
      <section className="archive">
        <div className="container">
          <div className="head">
            <div>
              <div className="eyebrow archive-eyebrow">Archive</div>
              <h2>Issue archive</h2>
            </div>
          </div>
          <p style={{ color: 'var(--muted)', fontStyle: 'italic', margin: 0 }}>
            No published issues yet. Check back once the first edition goes live.
          </p>
        </div>
      </section>
    );
  }

  return (
    <section className="archive">
      <div className="container">
        <div className="head">
          <div>
            <div className="eyebrow archive-eyebrow">{eyebrowText}</div>
            <h2>Issue archive</h2>
          </div>
          <div style={{ display: 'flex', gap: 12 }}>
            <button type="button" className="btn ghost small">
              All {totalIssues} {totalIssues === 1 ? 'issue' : 'issues'}
              <ArrowRight size={16} strokeWidth={1.5} aria-hidden />
            </button>
          </div>
        </div>

        <div className="timeline-rail">
          <div className="rail"></div>
          <div className="issues">
            {issues.map((iss) =>
            <button key={iss.id ?? `${iss.v}-${iss.i}`} className={`issue ${iss.current ? 'current' : ''}`} onClick={() => onOpenIssue(iss)}>
                <div className="cover">
                  <div>
                    <div className="vi">
                      <span className="v">Vol. {iss.v} · Iss. {iss.i}</span>
                      {iss.month}<br />{iss.year}
                    </div>
                  </div>
                  <div className="topic">{iss.topic}</div>
                  <div className="meta">{iss.articles} {iss.articles === 1 ? 'article' : 'articles'} {iss.current ? '· In progress' : '· Complete'}</div>
                </div>
                <div className="dot"></div>
                <div className="yr">{iss.month} {iss.year}{iss.current ? ' · current' : ''}</div>
              </button>
            )}
          </div>
        </div>
      </div>
    </section>);

};

export const Sidebar = () => {
  const { journal: j, subjects } = useJournalData();
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
        {board.map((e) =>
        <div className="editor-card" key={e.name}>
            <div className="editor-photo" data-init={e.init}></div>
            <div className="editor-info">
              <div className="name">{e.name}</div>
              <div className="role">{e.role || 'Editor-in-Chief'}</div>
              <div className="aff">{e.aff}</div>
            </div>
          </div>
        )}
        <a href="#" className="plain" style={{ marginTop: 14, display: 'inline-flex', alignItems: 'center', gap: 6, fontSize: 14 }}>
          View full board (38)
          <ArrowRight size={16} strokeWidth={1.5} aria-hidden />
        </a>
      </section>

      <section>
        <h3>Subjects</h3>
        <div style={{ display: 'flex', flexDirection: 'column', gap: 0 }}>
          {subjects.map((s) =>
          <a key={s} href="#" style={{
            borderBottom: '1px solid var(--rule)',
            padding: '10px 0',
            fontSize: 14.5,
            display: 'flex',
            justifyContent: 'space-between'
          }}>
              <span>{s}</span>
              <span className="mono" style={{ color: 'var(--muted)', fontSize: 11.5 }}>
                {Math.floor(20 + Math.abs(s.charCodeAt(2) - s.charCodeAt(0)) * 7)}
              </span>
            </a>
          )}
        </div>
      </section>

      <section>
        <h3>Stay informed</h3>
        <p style={{ fontSize: 14, color: 'var(--ink-2)', margin: '0 0 14px', lineHeight: 1.55 }}>
          Get every new article delivered to your inbox on the day it's published.
        </p>
        <div className="search-inline" style={{ marginBottom: 12, backgroundColor: "rgb(255, 255, 255)" }}>
          <input placeholder="name@university.edu" />
          <button>Subscribe</button>
        </div>
        <div style={{ display: 'flex', gap: 14, fontSize: 13, color: 'var(--muted)', fontFamily: '"JetBrains Mono", monospace', letterSpacing: '0.1em', textTransform: 'uppercase' }}>
          <a href="#" style={{ borderBottom: 'none', display: 'inline-flex', gap: 6, alignItems: 'center' }}>
            <Rss size={14} strokeWidth={1.5} aria-hidden /> RSS
          </a>
          <span>·</span>
          <a href="#" style={{ borderBottom: 'none' }}>Mastodon</a>
          <span>·</span>
          <a href="#" style={{ borderBottom: 'none' }}>Bluesky</a>
        </div>
      </section>
    </aside>);

};

export const Footer = () =>
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
  </footer>;
