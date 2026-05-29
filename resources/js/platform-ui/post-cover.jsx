import React from 'react';

export function PostCover({ className = '' }) {
    return (
        <div
            className={`post-cover-placeholder ${className}`.trim()}
            aria-hidden
        />
    );
}
