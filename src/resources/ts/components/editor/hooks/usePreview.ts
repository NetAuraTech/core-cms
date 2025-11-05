import {EditorComponentData} from "../types";
import {useRef, useState} from "preact/compat";
import {useEffectDebounced} from "../../../shared/functions/hooks";

export function usePreview(
    data: EditorComponentData,
    previewUrl: string,
    initialHTML: string,
): { loading: boolean; html: string } {
    const [loading, setLoading] = useState(false)
    const [html, setHTML] = useState(initialHTML)
    const isFirstRender = useRef(!!initialHTML)

    useEffectDebounced(
        () => {
            if (isFirstRender.current) {
                isFirstRender.current = false
                return
            }

            const hiddenInputs = document.querySelectorAll<HTMLInputElement>('input[type="text"][hidden][id^="preview-"]')
            const hiddenData: Record<string, string> = {}
            hiddenInputs.forEach(input => {
                hiddenData[input.id] = input.value
            })

            const payload = {
                ...data,
                ...hiddenData,
                preview: true,
            }

            const timer = window.setTimeout(() => setLoading(true), 200)
            fetch(previewUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify(payload),
            })
                .then(r => r.text())
                .then(setHTML)
                .finally(() => {
                    clearTimeout(timer)
                    setLoading(false)
                })
            return () => clearTimeout(timer)
        },
        [data],
        500,
    )
    return {
        loading,
        html,
    }
}
