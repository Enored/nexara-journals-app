import React from 'react';
import {
  BellRing,
  Bookmark,
  BookmarkCheck,
  Download,
  Quote,
  Share2,
} from 'lucide-react';

// Article detail screen (when user clicks an article).

export const ArticleDetail = ({ article, onBack, onCite, onSave, saved }) => {
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
            <button type="button" className="btn">
              <Download size={16} strokeWidth={1.5} aria-hidden /> Download PDF
            </button>
            <button type="button" className="btn ghost" onClick={() => onCite(a)}>
              <Quote size={16} strokeWidth={1.5} aria-hidden /> Cite
            </button>
            <button type="button" className="btn ghost" onClick={() => onSave(a)}>
              {saved ? (
                <BookmarkCheck size={16} strokeWidth={1.5} aria-hidden />
              ) : (
                <Bookmark size={16} strokeWidth={1.5} aria-hidden />
              )}
              {saved ? 'Saved' : 'Save to library'}
            </button>
            <button type="button" className="btn ghost">
              <Share2 size={16} strokeWidth={1.5} aria-hidden /> Share
            </button>
            <button type="button" className="btn ghost">
              <BellRing size={16} strokeWidth={1.5} aria-hidden /> Track citations
            </button>
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
