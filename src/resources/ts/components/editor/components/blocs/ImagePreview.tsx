import {useImageResolver} from "../../hooks/useImageResolver";
import {CSSProperties} from "preact/compat";

export default function ImagePreview({
                          imageName,
                      }: {
    imageName: string;
}) {
    const { resolvedUrl, svgContent, isSvg, isLoading, error } = useImageResolver(imageName);

    if (isLoading) {
        return <div style={{width: "280px", height: "180px", borderRadius: "5px", border: "solid 1px var(--neutral-800)"} as CSSProperties}></div>;
    }

    if (error || !resolvedUrl) {
        return <div style={{width: "280px", height: "180px", borderRadius: "5px", border: "solid 1px var(--neutral-800)"} as CSSProperties}>
        </div>;
    }

    // Si c'est un SVG, l'injecter en inline pour que currentColor fonctionne
    if (isSvg && svgContent) {
        return (
            <div
        dangerouslySetInnerHTML={{ __html: svgContent }}
        />
    );
    }

    // Sinon, utiliser une balise img classique
    return <img src={resolvedUrl} alt={imageName} width={280} height={180} />;
}