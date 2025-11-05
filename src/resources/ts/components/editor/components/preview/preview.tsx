import {EditorComponentData} from "../../types";
import {createPortal, useEffect, useRef, useState} from "preact/compat";
import {usePreviewMode} from "../../store";
import {PreviewModes} from "../../enum";
import {PHONE_HEIGHT} from "../../constants";
import {PreviewItems} from "./previewItems";
import {useWindowSize} from '../../../../shared/functions/window';
import {useAsyncEffect} from "../../../../shared/functions/hooks";


type PreviewProps = {
    data: EditorComponentData[]
    previewUrl: string
}

export function Preview({data, previewUrl}: PreviewProps) {
    const iframe = useRef<HTMLIFrameElement | null>(null)
    const [iframeRoot, setIframeRoot] = useState<HTMLElement | null>(null)
    const initialHTML = useRef<Record<string, string>>({})
    const [loaded, setLoaded] = useState(false)
    const showSpinner = !loaded
    const previewMode = usePreviewMode()
    const {height: windowHeight} = useWindowSize()
    let transform = undefined

    // @ts-ignore
    if (previewMode === PreviewModes.PHONE && windowHeight < 844) {
        // @ts-ignore
        transform = {transform: `scale(${windowHeight / PHONE_HEIGHT})`}
    }

    useAsyncEffect(async () => {
        setLoaded(false)
        const r = await fetch(previewUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(data),
        })
        if (!r.ok) {
            return
        }

        const iframeDocument = iframe.current!.contentDocument!;

        iframeDocument?.open();
        iframeDocument?.write(await r.text());
        iframeDocument?.close();
        setLoaded(true)
    }, [])

    useEffect(() => {
        if (loaded && iframe.current) {
            const handleLoad = () => {
                const iframeDocument = iframe.current!.contentDocument;
                if (!iframeDocument) return;

                // Wait until the document is ready (useful if Firefox is still running slowly)
                if (iframeDocument.readyState !== "complete") {
                    const onReady = () => {
                        initIframe(iframeDocument);
                        iframeDocument.removeEventListener("DOMContentLoaded", onReady);
                    };
                    iframeDocument.addEventListener("DOMContentLoaded", onReady);
                } else {
                    initIframe(iframeDocument);
                }
            };

            const initIframe = (iframeDocument: Document) => {
                const root = iframeDocument.querySelector("#ve-components") as HTMLElement | null;
                if (root) {
                    initialHTML.current = Array.from(root.children).reduce(
                        (acc, v, k) => ({ ...acc, [data[k]!._id]: v.outerHTML }),
                        {},
                    );
                    root.innerHTML = "";
                    setIframeRoot(root);
                }
            };

            iframe.current.addEventListener("load", handleLoad);

            return () => {
                iframe.current?.removeEventListener("load", handleLoad);
            };
        }
    }, [loaded]);

    return (
        <div className={'preview'}>
            {showSpinner && <div className="loader__wrapper"><span className="loader"></span></div>}
            <iframe
                className={`${loaded ? 'loaded' : ''} ${
                    previewMode === PreviewModes.PHONE ? 'mobile' : ''
                }`}
                ref={iframe}
                style={transform}
                onLoad={() => setLoaded(true)}
            />
            {iframeRoot &&
                createPortal(
                    <PreviewItems
                        data={data}
                        initialHTML={initialHTML.current}
                        previewUrl={previewUrl}
                    />,
                    iframeRoot,
                )}
        </div>
    )
}
