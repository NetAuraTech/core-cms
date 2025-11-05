import { CSSProperties } from "preact/compat";

export function IconCheck() {
    return (
        <svg
            className="icon small clr-green-500"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 16 16"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path
                fill="currentColor"
                d="M13.315 2.716A7.461 7.461 0 102.763 13.268 7.461 7.461 0 0013.315 2.716zM8.039 14.454a6.468 6.468 0 01-6.46-6.46 6.468 6.468 0 016.46-6.462A6.468 6.468 0 0114.5 7.992a6.468 6.468 0 01-6.46 6.461z"
            />
            <path
                fill="currentColor"
                d="M6.915 9.556L4.62 7.262l-.708.707 3.002 3.002 5.234-5.235-.707-.707-4.527 4.527z"
            />
        </svg>
    )
}

export function IconCode() {
    return (
        <svg
            className="icon small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path
                fill="currentColor"
                d="M24 12l-5.657 5.657-1.414-1.414L21.172 12l-4.243-4.243 1.414-1.414L24 12zM2.828 12l4.243 4.243-1.414 1.414L0 12l5.657-5.657L7.07 7.757 2.828 12zm6.96 9H7.66l6.552-18h2.128L9.788 21z"
            />
        </svg>
    )
}

export function IconDown() {
    return (
        <svg
            className="icon small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path
                d="M12 13.172l4.95-4.95 1.414 1.414L12 16 5.636 9.636 7.05 8.222z"
                fill="currentColor"
            />
        </svg>
    )
}

export function IconTrash() {
    return (
        <svg
            className="icon small clr-red-400"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path
                d="M17 6h5v2h-2v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V8H2V6h5V3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3zm1 2H6v12h12V8zm-4.586 6l1.768 1.768-1.414 1.414L12 15.414l-1.768 1.768-1.414-1.414L10.586 14l-1.768-1.768 1.414-1.414L12 12.586l1.768-1.768 1.414 1.414L13.414 14zM9 4v2h6V4H9z"
                fill="currentColor"
            />
        </svg>
    )
}

export function IconBold() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M6 12h9a4 4 0 0 1 0 8H7a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h7a4 4 0 0 1 0 8"/>
        </svg>
    )
}

export function IconItalic() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <line x1="19" x2="10" y1="4" y2="4"/>
            <line x1="14" x2="5" y1="20" y2="20"/>
            <line x1="15" x2="9" y1="4" y2="20"/>
        </svg>
    )
}

export function IconUnderline() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M6 4v6a6 6 0 0 0 12 0V4"/>
            <line x1="4" x2="20" y1="20" y2="20"/>
        </svg>
    )
}

export function IconStrike() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M16 4H9a3 3 0 0 0-2.83 4"/>
            <path d="M14 12a4 4 0 0 1 0 8H6"/>
            <line x1="4" x2="20" y1="12" y2="12"/>
        </svg>
    )
}

export function IconHighlight() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="m9 11-6 6v3h9l3-3"/>
            <path d="m22 12-4.6 4.6a2 2 0 0 1-2.8 0l-5.2-5.2a2 2 0 0 1 0-2.8L14 4"/>
        </svg>
    )
}

export function IconAlignLeft() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M15 12H3"/>
            <path d="M17 18H3"/>
            <path d="M21 6H3"/>
        </svg>
    )
}

export function IconAlignRight() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M21 12H9"/>
            <path d="M21 18H7"/>
            <path d="M21 6H3"/>
        </svg>
    )
}

export function IconAlignCenter() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M17 12H7"/>
            <path d="M19 18H5"/>
            <path d="M21 6H3"/>
        </svg>
    )
}

export function IconAlignJustify() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M3 12h18"/>
            <path d="M3 18h18"/>
            <path d="M3 6h18"/>
        </svg>
    )
}

export function IconAlignUnset() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M11 12H3"/>
            <path d="M16 6H3"/>
            <path d="M16 18H3"/>
            <path d="m19 10-4 4"/>
            <path d="m15 10 4 4"/>
        </svg>
    )
}

export function IconLink() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
    )
}

export function IconUnlink() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="m18.84 12.25 1.72-1.71h-.02a5.004 5.004 0 0 0-.12-7.07 5.006 5.006 0 0 0-6.95 0l-1.72 1.71"/>
            <path d="m5.17 11.75-1.71 1.71a5.004 5.004 0 0 0 .12 7.07 5.006 5.006 0 0 0 6.95 0l1.71-1.71"/>
            <line x1="8" x2="8" y1="2" y2="5"/>
            <line x1="2" x2="5" y1="8" y2="8"/>
            <line x1="16" x2="16" y1="19" y2="22"/>
            <line x1="19" x2="22" y1="16" y2="16"/>
        </svg>
    )
}

export function IconHeading1() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M4 12h8"/>
            <path d="M4 18V6"/>
            <path d="M12 18V6"/>
            <path d="m17 12 3-2v8"/>
        </svg>
    )
}

export function IconHeading2() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M4 12h8"/>
            <path d="M4 18V6"/>
            <path d="M12 18V6"/>
            <path d="M21 18h-4c0-4 4-3 4-6 0-1.5-2-2.5-4-1"/>
        </svg>
    )
}

export function IconHeading3() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M4 12h8"/>
            <path d="M4 18V6"/>
            <path d="M12 18V6"/>
            <path d="M17.5 10.5c1.7-1 3.5 0 3.5 1.5a2 2 0 0 1-2 2"/>
            <path d="M17 17.5c2 1.5 4 .3 4-1.5a2 2 0 0 0-2-2"/>
        </svg>
    )
}

export function IconHeading4() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M12 18V6"/>
            <path d="M17 10v3a1 1 0 0 0 1 1h3"/>
            <path d="M21 10v8"/>
            <path d="M4 12h8"/>
            <path d="M4 18V6"/>
        </svg>
    )
}

export function IconUndo() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M9 14 4 9l5-5"/>
            <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"/>
        </svg>
    )
}

export function IconRedo() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="m15 14 5-5-5-5"/>
            <path d="M20 9H9.5A5.5 5.5 0 0 0 4 14.5A5.5 5.5 0 0 0 9.5 20H13"/>
        </svg>
    )
}

export function IconRemoveFormatting() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M4 7V4h16v3"/>
            <path d="M5 20h6"/>
            <path d="M13 4 8 20"/>
            <path d="m15 15 5 5"/>
            <path d="m20 15-5 5"/>
        </svg>
    )
}

export function IconList() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M10 12h11"/>
            <path d="M10 18h11"/>
            <path d="M10 6h11"/>
            <path d="M4 10h2"/>
            <path d="M4 6h1v4"/>
            <path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/>
        </svg>
    )
}

export function IconListSink() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M16 12H3"/>
            <path d="M16 6H3"/>
            <path d="M10 18H3"/>
            <path d="M21 6v10a2 2 0 0 1-2 2h-5"/>
            <path d="m16 16-2 2 2 2"/>
        </svg>
    )
}

export function IconListLift() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path d="M16 12H3"/>
            <path d="M16 18H3"/>
            <path d="M10 6H3"/>
            <path d="M21 18V8a2 2 0 0 0-2-2h-5"/>
            <path d="m16 8-2-2 2-2"/>
        </svg>
    )
}

export function IconVideo() {
    return (
        <svg
            className="icon very-small"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2"
            stroke="currentColor"
            style={{maxWidth: 'initial'} as CSSProperties}
        >
            <path
                d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/>
            <path d="m10 15 5-3-5-3z"/>
        </svg>
    )
}
