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
