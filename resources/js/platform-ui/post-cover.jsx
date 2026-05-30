import React from 'react';

export function PostCover({ className = '', src = null, alt = '' }) {
    if (src) {
        return (
            <img
                src={src}
                alt={alt}
                className={`post-cover-image ${className}`.trim()}
                loading="lazy"
            />
        );
    }

    return (
        <div
            className={`post-cover-placeholder ${className}`.trim()}
            aria-hidden
        />
    );
}
