import {useEffect, useState} from "preact/compat";

interface UseImageResolverReturn {
    resolvedUrl: string | null;
    svgContent: string | null;
    isSvg: boolean;
    isLoading: boolean;
    error: Error | null;
}

export function useImageResolver(imageName: string): UseImageResolverReturn {
    const [resolvedUrl, setResolvedUrl] = useState<string | null>(null);
    const [svgContent, setSvgContent] = useState<string | null>(null);
    const [isSvg, setIsSvg] = useState(false);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<Error | null>(null);

    useEffect(() => {
        let cancelled = false;

        const resolveImage = async () => {
            if (!imageName) {
                setIsLoading(false);
                return;
            }

            setIsLoading(true);
            setError(null);
            setSvgContent(null);

            try {
                const isSvgFile = imageName.toLowerCase().endsWith('.svg');
                setIsSvg(isSvgFile);

                // Priorité 1 : Thème
                const themeImageUrl = `/assets/assets/editor/${imageName}`;
                const themeResponse = await fetch(themeImageUrl, { method: 'HEAD' });

                if (themeResponse.ok && !cancelled) {
                    setResolvedUrl(themeImageUrl);

                    // Si c'est un SVG, récupérer le contenu
                    if (isSvgFile) {
                        const svgResponse = await fetch(themeImageUrl);
                        const svgText = await svgResponse.text();
                        setSvgContent(svgText);
                    }

                    setIsLoading(false);
                    return;
                }

                // Fallback : Package
                const packageImageUrl = `/vendor/core-cms/editor/${imageName}`;
                const packageResponse = await fetch(packageImageUrl, { method: 'HEAD' });

                if (packageResponse.ok && !cancelled) {
                    setResolvedUrl(packageImageUrl);

                    // Si c'est un SVG, récupérer le contenu
                    if (isSvgFile) {
                        const svgResponse = await fetch(packageImageUrl);
                        const svgText = await svgResponse.text();
                        setSvgContent(svgText);
                    }

                    setIsLoading(false);
                    return;
                }

                // Image introuvable
                if (!cancelled) {
                    const err = new Error(`Image not found: ${imageName}`);
                    setError(err);
                    setResolvedUrl(null);
                    setIsLoading(false);
                }
            } catch (err) {
                if (!cancelled) {
                    setError(err as Error);
                    setResolvedUrl(null);
                    setIsLoading(false);
                }
            }
        };

        resolveImage();

        return () => {
            cancelled = true;
        };
    }, [imageName]);

    return { resolvedUrl, svgContent, isSvg, isLoading, error };
}