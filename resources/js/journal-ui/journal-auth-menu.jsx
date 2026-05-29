import React, { useEffect, useRef, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';

export function JournalAuthMenu({ className = '' }) {
    const { auth, platform } = usePage().props;
    const user = auth?.user;
    const [open, setOpen] = useState(false);
    const rootRef = useRef(null);

    useEffect(() => {
        const onDocClick = (e) => {
            if (rootRef.current && !rootRef.current.contains(e.target)) {
                setOpen(false);
            }
        };
        const onEscape = (e) => {
            if (e.key === 'Escape') {
                setOpen(false);
            }
        };
        document.addEventListener('click', onDocClick);
        document.addEventListener('keydown', onEscape);
        return () => {
            document.removeEventListener('click', onDocClick);
            document.removeEventListener('keydown', onEscape);
        };
    }, []);

    if (!user) {
        return (
            <a href={platform.urls.login} className={`journal-auth-signin plain ${className}`.trim()}>
                Sign in
            </a>
        );
    }

    const displayName = user.firstName?.trim() || user.name?.split(/\s+/)[0] || user.name;

    const logout = () => {
        setOpen(false);
        router.post(platform.urls.logout);
    };

    return (
        <div ref={rootRef} className={`journal-auth ${className}`.trim()}>
            <button
                type="button"
                className="journal-auth-trigger plain"
                onClick={() => setOpen((v) => !v)}
                aria-expanded={open}
                aria-haspopup="menu"
            >
                <span>{displayName}</span>
                <ChevronDown size={14} strokeWidth={1.5} aria-hidden className={open ? 'journal-auth-chevron-open' : ''} />
            </button>
            {open && (
                <div className="journal-auth-menu" role="menu">
                    <a href={platform.urls.dashboard} className="journal-auth-menu-item plain" role="menuitem">
                        Dashboard
                    </a>
                    <a href={platform.urls.settings} className="journal-auth-menu-item plain" role="menuitem">
                        Settings
                    </a>
                    <button type="button" className="journal-auth-menu-item" role="menuitem" onClick={logout}>
                        Logout
                    </button>
                </div>
            )}
        </div>
    );
}
