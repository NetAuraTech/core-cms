import {EditorComponentData} from "../../types";
import {useFieldFocused, useSetFocusIndex} from "../../store";
import {usePreview} from "../../hooks/usePreview";
import DOMPurify from "dompurify";
import parse from "html-react-parser";
import {useEffect, useRef} from "preact/compat";
import {offsetTop} from "../../../../shared/functions/dom";

type PreviewItemProps = {
    data: EditorComponentData
    initialHTML: string
    previewUrl: string
    title: string
}

export function PreviewItem({
                                data,
                                initialHTML,
                                previewUrl,
                                title,
                            }: PreviewItemProps) {
    const ref = useRef<HTMLDivElement>(null)
    const {loading, html} = usePreview(data, previewUrl, initialHTML)
    const setFocusIndex = useSetFocusIndex()
    const isFocused = useFieldFocused(data._id)

    const sanitizedData = DOMPurify.sanitize(html);

    useEffect(() => {
        if (isFocused) {
            const top = offsetTop(ref.current!) - 40
            const root = ref.current!.closest('html')!
            root.scrollTop = top
        }
    }, [isFocused])

    return (
        <div
            className={`editor__preview-item ${isFocused ? 'focused' : ''}`}
            id={`previewItem${data._id}`}
            ref={ref}
            onClick={() => setFocusIndex(data._id)}
        >
            {loading && <div className="loader__wrapper"><span className="loader"></span></div>}
            <div
                className={`title ${isFocused ? 'focused' : ''}`}
            >
                {title}
            </div>
            <div>{parse(sanitizedData)}</div>
        </div>
    )
}
