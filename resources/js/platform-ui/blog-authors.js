export const BLOG_AUTHORS = {
    'Helena Vásquez': {
        initials: 'HV',
        bio: 'Editor-in-Chief of the Journal of Cognitive Computation. Writes on open access, editorial policy, and the economics of diamond publishing.',
    },
    'Marek Tóth': {
        initials: 'MT',
        bio: 'Deputy Editor overseeing triage and reviewer assignment across Nexara journals.',
    },
    'Rohan Iyer': {
        initials: 'RI',
        bio: 'Statistics Editor; leads reproducibility audits and submission-time model checks.',
    },
    'Ayanna Okafor': {
        initials: 'AO',
        bio: 'Methods Editor and programme lead for the Early-Career Reviewer cohort.',
    },
    'Sofía Castellanos': {
        initials: 'SC',
        bio: 'Reviews Editor; writes explainers on metrics, attention, and responsible use of altmetrics.',
    },
};

export function authorFor(post) {
    const known = BLOG_AUTHORS[post.author];
    if (known) {
        return known;
    }
    const initials = post.author
        .split(' ')
        .map((w) => w[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
    return { initials, bio: '' };
}
