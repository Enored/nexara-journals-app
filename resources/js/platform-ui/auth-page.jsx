import React, { useState } from 'react';
import { Link, useForm, usePage } from '@inertiajs/react';
import {
    Bell,
    Bookmark,
    Building2,
    Check,
    Eye,
    EyeOff,
    PenLine,
} from 'lucide-react';

const ORCID_LOGO = (
    <svg className="orcid-logo" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" aria-hidden>
        <path
            fill="#A6CE39"
            d="M256 128c0 70.7-57.3 128-128 128S0 198.7 0 128 57.3 0 128 0s128 57.3 128 128z"
        />
        <g fill="#FFF">
            <path d="M86.3 186.2H70.9V79.1h15.4v107.1z" />
            <path d="M108.9 79.1h41.6c39.6 0 57 28.3 57 53.6 0 27.5-21.5 53.6-56.8 53.6h-41.8V79.1zm15.4 93.3h24.5c34.9 0 42.9-26.5 42.9-39.7 0-21.5-13.7-39.7-43.7-39.7h-23.7v79.4z" />
            <path d="M88.7 56.8c0 5.5-4.5 10.1-10.1 10.1s-10.1-4.6-10.1-10.1c0-5.6 4.5-10.1 10.1-10.1s10.1 4.6 10.1 10.1z" />
        </g>
    </svg>
);

const BENEFIT_ICONS = {
    bookmark: Bookmark,
    bell: Bell,
    edit: PenLine,
};

function scorePw(pw) {
    let s = 0;
    if (pw.length >= 8) s++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) s++;
    if (/\d/.test(pw)) s++;
    if (/[^A-Za-z0-9]/.test(pw)) s++;
    return Math.max(1, Math.min(s, 4));
}

const PW_LABELS = ['', 'Weak', 'Fair', 'Good', 'Strong'];

function AuthAside({ press }) {
    const { platform } = usePage().props;
    const benefits = [
        {
            icon: 'bookmark',
            text: (
                <>
                    <b>Save searches and articles</b> across all {press.journals} journals, synced to
                    every device.
                </>
            ),
        },
        {
            icon: 'bell',
            text: (
                <>
                    <b>Alerts for new issues</b>, citing articles, and topics you follow.
                </>
            ),
        },
        {
            icon: 'edit',
            text: (
                <>
                    <b>Submit and track manuscripts</b> — diamond open access, no fees, ever.
                </>
            ),
        },
    ];

    return (
        <aside className="auth-aside">
            <Link href={platform.urls.home} className="wordmark">
                <span className="mark">
                    {press.name} <span className="dot" />
                </span>
                <span className="sub">Research Press</span>
            </Link>

            <div className="pitch">
                <div className="eyebrow">Free to read · free to publish · since 2003</div>
                <h2>
                    One account for <em>every</em> journal in the Press.
                </h2>
                <div className="benefits">
                    {benefits.map((b) => {
                        const Icon = BENEFIT_ICONS[b.icon];
                        return (
                            <div className="benefit" key={b.icon}>
                                <span className="bi">
                                    <Icon size={14} strokeWidth={1.5} aria-hidden />
                                </span>
                                <span className="bt">{b.text}</span>
                            </div>
                        );
                    })}
                </div>
            </div>

            <div className="quote">
                <p>
                    &ldquo;Nexara is the rare publisher where the reader, the author, and the editor
                    all want the same thing — the work, in the open.&rdquo;
                </p>
                <div className="who">— Prof. Helena Vásquez · Editor-in-Chief, JCC</div>
            </div>
        </aside>
    );
}

function AuthErrors({ errors }) {
    const messages = Object.values(errors || {}).flat();
    if (messages.length === 0) {
        return null;
    }
    return (
        <div className="auth-errors">
            {messages.length === 1 ? (
                <p>{messages[0]}</p>
            ) : (
                <ul>
                    {messages.map((msg) => (
                        <li key={msg}>{msg}</li>
                    ))}
                </ul>
            )}
        </div>
    );
}

function SignInForm() {
    const { platform } = usePage().props;
    const [show, setShow] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: true,
    });

    const submit = (e) => {
        e.preventDefault();
        post(platform.urls.login, { preserveScroll: true });
    };

    return (
        <form onSubmit={submit}>
            <div className="auth-head">
                <h1>Welcome back</h1>
                <p>Sign in to your Nexara account to pick up where you left off.</p>
            </div>

            <AuthErrors errors={errors} />

            <div className="sso-stack">
                <button type="button" className="sso-btn" disabled title="Coming soon">
                    {ORCID_LOGO} Sign in with ORCID
                </button>
                <button type="button" className="sso-btn inst" disabled title="Coming soon">
                    <Building2 size={18} strokeWidth={1.5} aria-hidden />
                    Access through your institution
                </button>
            </div>

            <div className="auth-divider">
                <span>or with email</span>
            </div>

            <div className="field">
                <label htmlFor="si-email">Email address</label>
                <div className="inp-wrap">
                    <input
                        id="si-email"
                        type="email"
                        placeholder="name@university.edu"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        className={errors.email ? 'field-error' : ''}
                        required
                        autoComplete="email"
                    />
                </div>
            </div>
            <div className="field">
                <label htmlFor="si-pw">Password</label>
                <div className="inp-wrap">
                    <input
                        id="si-pw"
                        type={show ? 'text' : 'password'}
                        className={`has-btn ${errors.password ? 'field-error' : ''}`}
                        placeholder="••••••••••"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        required
                        autoComplete="current-password"
                    />
                    <button
                        type="button"
                        className="reveal"
                        onClick={() => setShow(!show)}
                        aria-label={show ? 'Hide password' : 'Show password'}
                    >
                        {show ? (
                            <EyeOff size={17} strokeWidth={1.5} />
                        ) : (
                            <Eye size={17} strokeWidth={1.5} />
                        )}
                    </button>
                </div>
            </div>

            <div className="auth-row">
                <label
                    className={`checkbox ${data.remember ? 'on' : ''}`}
                    onClick={() => setData('remember', !data.remember)}
                >
                    <span className="cb">
                        {data.remember && <Check size={11} strokeWidth={2.5} aria-hidden />}
                    </span>
                    <span className="ct">Keep me signed in</span>
                </label>
                <button type="button" className="forgot" disabled title="Coming soon">
                    Forgot password?
                </button>
            </div>

            <button type="submit" className="btn block auth-submit" disabled={processing}>
                Sign in <span className="arrow">→</span>
            </button>

            <div className="auth-alt">
                New to Nexara? <Link href={platform.urls.register}>Create a free account</Link>
            </div>
        </form>
    );
}

function RegisterForm({ countries, roles }) {
    const { platform } = usePage().props;
    const [show, setShow] = useState(false);
    const [agree, setAgree] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        country: '',
        role: '',
    });

    const score = scorePw(data.password);

    const submit = (e) => {
        e.preventDefault();
        if (!agree) {
            return;
        }
        post(platform.urls.register, { preserveScroll: true });
    };

    return (
        <form onSubmit={submit}>
            <div className="auth-head">
                <h1>Create your account</h1>
                <p>Free, and always will be. One login for reading, alerts, and submissions.</p>
            </div>

            <AuthErrors errors={errors} />

            <div className="sso-stack">
                <button type="button" className="sso-btn" disabled title="Coming soon">
                    {ORCID_LOGO} Register with ORCID
                </button>
            </div>

            <div className="auth-divider">
                <span>or with email</span>
            </div>

            <div className="field row2">
                <div>
                    <label htmlFor="r-first">First name</label>
                    <div className="inp-wrap">
                        <input
                            id="r-first"
                            type="text"
                            placeholder="Ada"
                            value={data.first_name}
                            onChange={(e) => setData('first_name', e.target.value)}
                            className={errors.first_name ? 'field-error' : ''}
                            required
                            autoComplete="given-name"
                        />
                    </div>
                </div>
                <div>
                    <label htmlFor="r-last">Last name</label>
                    <div className="inp-wrap">
                        <input
                            id="r-last"
                            type="text"
                            placeholder="Lovelace"
                            value={data.last_name}
                            onChange={(e) => setData('last_name', e.target.value)}
                            className={errors.last_name ? 'field-error' : ''}
                            required
                            autoComplete="family-name"
                        />
                    </div>
                </div>
            </div>

            <div className="field">
                <label htmlFor="r-email">Email address</label>
                <div className="inp-wrap">
                    <input
                        id="r-email"
                        type="email"
                        placeholder="name@university.edu"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        className={errors.email ? 'field-error' : ''}
                        required
                        autoComplete="email"
                    />
                </div>
                <div className="field-hint">
                    Use your institutional email to unlock affiliated access automatically.
                </div>
            </div>

            <div className="field row2">
                <div>
                    <label htmlFor="r-role">Role</label>
                    <div className="inp-wrap">
                        <select
                            id="r-role"
                            value={data.role}
                            onChange={(e) => setData('role', e.target.value)}
                        >
                            <option value="">I am a…</option>
                            {roles.map((r) => (
                                <option key={r} value={r}>
                                    {r}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>
                <div>
                    <label htmlFor="r-country">Country</label>
                    <div className="inp-wrap">
                        <select
                            id="r-country"
                            value={data.country}
                            onChange={(e) => setData('country', e.target.value)}
                        >
                            <option value="">Select country…</option>
                            {countries.map((c) => (
                                <option key={c} value={c}>
                                    {c}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>
            </div>

            <div className="field">
                <label htmlFor="r-pw">Password</label>
                <div className="inp-wrap">
                    <input
                        id="r-pw"
                        type={show ? 'text' : 'password'}
                        className={`has-btn ${errors.password ? 'field-error' : ''}`}
                        placeholder="At least 8 characters"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        required
                        autoComplete="new-password"
                    />
                    <button
                        type="button"
                        className="reveal"
                        onClick={() => setShow(!show)}
                        aria-label={show ? 'Hide password' : 'Show password'}
                    >
                        {show ? (
                            <EyeOff size={17} strokeWidth={1.5} />
                        ) : (
                            <Eye size={17} strokeWidth={1.5} />
                        )}
                    </button>
                </div>
                {data.password && (
                    <>
                        <div className={`pw-strength s${score}`}>
                            <span className="seg" />
                            <span className="seg" />
                            <span className="seg" />
                            <span className="seg" />
                        </div>
                        <div className="pw-label">{PW_LABELS[score]} password</div>
                    </>
                )}
            </div>

            <div className="field">
                <label htmlFor="r-pw2">Confirm password</label>
                <div className="inp-wrap">
                    <input
                        id="r-pw2"
                        type={show ? 'text' : 'password'}
                        className={errors.password_confirmation ? 'field-error' : ''}
                        placeholder="Repeat password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        required
                        autoComplete="new-password"
                    />
                </div>
            </div>

            <div className="auth-row" style={{ marginTop: 4 }}>
                <label
                    className={`checkbox start ${agree ? 'on' : ''}`}
                    onClick={() => setAgree(!agree)}
                >
                    <span className="cb">
                        {agree && <Check size={11} strokeWidth={2.5} aria-hidden />}
                    </span>
                    <span className="ct">
                        I agree to the <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.
                    </span>
                </label>
            </div>

            <button type="submit" className="btn block auth-submit" disabled={!agree || processing}>
                Create account <span className="arrow">→</span>
            </button>

            <div className="auth-alt">
                Already have an account? <Link href={platform.urls.login}>Sign in</Link>
            </div>
        </form>
    );
}

export default function AuthPage({ mode, press, countries, roles }) {
    const { platform } = usePage().props;
    const isRegister = mode === 'register';

    return (
        <div className="auth-page">
            <AuthAside press={press} />
            <main className="auth-main">
                <div className="auth-form-wrap">
                    <div className="auth-tabs">
                        <Link
                            href={platform.urls.login}
                            className={!isRegister ? 'active' : ''}
                        >
                            Sign in
                        </Link>
                        <Link
                            href={platform.urls.register}
                            className={isRegister ? 'active' : ''}
                        >
                            Register
                        </Link>
                    </div>

                    {isRegister ? (
                        <RegisterForm countries={countries} roles={roles} />
                    ) : (
                        <SignInForm />
                    )}

                    <div className="auth-legal">
                        Protected by reCAPTCHA. Nexara is a non-profit scholarly publisher.
                        <br />
                        <a href="#">Privacy</a> · <a href="#">Terms</a> · <a href="#">Accessibility</a>
                    </div>
                </div>
            </main>
        </div>
    );
}
