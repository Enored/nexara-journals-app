import React from 'react';

function AboutHero({ hero }) {
    return (
        <section className="about-hero">
            <div className="container-wide">
                <div className="grid">
                    <div>
                        <div className="eyebrow">{hero.eyebrow}</div>
                        <h1>{hero.title}</h1>
                    </div>
                    <p className="lead">{hero.lead}</p>
                </div>
            </div>
        </section>
    );
}

function Mission({ mission }) {
    return (
        <section className="about-sec mission-sec">
            <div className="container-wide">
                <div className="head">
                    <div className="sub">01 — Mission</div>
                    <h2>{mission.heading}</h2>
                    <p className="standfirst">{mission.standfirst}</p>
                </div>
                <div className="mission-prose">
                    {mission.paragraphs.map((p, i) => (
                        <p key={i}>{p}</p>
                    ))}
                </div>
            </div>
        </section>
    );
}

function Stats({ stats }) {
    return (
        <section className="stats-band">
            <div className="container-wide">
                <div className="head">
                    <div className="sub">02 — By the numbers</div>
                    <h2>The Press, at a glance.</h2>
                </div>
                <div className="grid">
                    {stats.map((s, i) => (
                        <div className="cell" key={i}>
                            <span className="v">{s.v}</span>
                            <span className="l">{s.l}</span>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}

function Leadership({ leadership }) {
    return (
        <section className="about-sec">
            <div className="container-wide">
                <div className="head">
                    <div className="sub">03 — Leadership</div>
                    <h2>The people who run the Press.</h2>
                    <p className="standfirst">
                        Six editors and one operations lead — small enough to know each other, large enough to keep the work moving.
                    </p>
                </div>
                <div className="lead-grid">
                    {leadership.map((p) => (
                        <article className="lead-card" key={p.id}>
                            <div className="lead-portrait">
                                <span className="initials">{p.initials}</span>
                            </div>
                            <div className="lc-name">{p.name}</div>
                            <div className="lc-role">{p.role}</div>
                            <p className="lc-bio">{p.bio}</p>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}

function Timeline({ timeline }) {
    return (
        <section className="timeline-band">
            <div className="container-wide">
                <div className="head">
                    <div className="sub">04 — History</div>
                    <h2>Twenty-three years of open scholarly publishing.</h2>
                    <p className="standfirst">A short chronology of the moments that shaped the Press.</p>
                </div>
                <div className="timeline">
                    {timeline.map((t, i) => (
                        <div
                            className={`tl-item ${i === timeline.length - 1 ? 'recent' : ''}`}
                            key={t.year}
                        >
                            <div className="tl-year">{t.year}</div>
                            <h3 className="tl-title">{t.title}</h3>
                            <p className="tl-body">{t.body}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}

function Offices({ offices }) {
    return (
        <section className="about-sec">
            <div className="container-wide">
                <div className="head">
                    <div className="sub">05 — Locations</div>
                    <h2>Where to find us.</h2>
                </div>
                <div className="offices-grid">
                    {offices.map((o) => (
                        <div className="office" key={o.city}>
                            <div className="city">{o.city}</div>
                            <div className="country">{o.country}</div>
                            <div className="line">{o.line}</div>
                            <div className="row">
                                <span className="k">Address</span>
                                <span className="v">{o.address}</span>
                            </div>
                            <div className="row">
                                <span className="k">Phone</span>
                                <span className="v">{o.phone}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}

function Contact({ contact }) {
    return (
        <section className="contact-band">
            <div className="container-wide">
                <div className="head">
                    <div className="sub">06 — Contact</div>
                    <h2>{contact.heading}</h2>
                    <p className="standfirst">{contact.standfirst}</p>
                </div>
                <div className="contact-grid">
                    {contact.items.map((it) => (
                        <a className="contact-row plain" key={it.label} href={it.href}>
                            <span className="k">{it.label}</span>
                            <span className="v">{it.value}</span>
                        </a>
                    ))}
                </div>
            </div>
        </section>
    );
}

export default function AboutPage({ about }) {
    return (
        <>
            <AboutHero hero={about.hero} />
            <Mission mission={about.mission} />
            <Stats stats={about.stats} />
            <Leadership leadership={about.leadership} />
            <Timeline timeline={about.timeline} />
            <Offices offices={about.offices} />
            <Contact contact={about.contact} />
        </>
    );
}
