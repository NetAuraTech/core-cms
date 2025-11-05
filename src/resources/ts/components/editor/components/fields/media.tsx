import {useUniqId} from "../../../../shared/functions/hooks";
import {defineField} from "./utils";
import {JSX} from "preact";
import {useEffect, useRef, useState} from "preact/compat";
import {jsonFetch} from "../../../../shared/functions/api";
import {translate} from "../../../../shared/functions/i18n";
import {Field} from "../ui/field";

export type MediaFieldArgs = {
    label?: string
    default?: MediaValue,
    canAnimate?: boolean
}

export type MediaValue = {
    id: string | null
    alt: string
    height: string
    opacity: string
} | null

type Alt = {
    id: number
    text: string
    isDefault: boolean
}

const Component: ({value, onChange, options}: { value: MediaValue; onChange: any; options: MediaFieldArgs }) => JSX.Element = ({
                                                                                                                                   value,
                                                                                                                                   onChange,
                                                                                                                                   options,
                                                                                                                               }) => {
    const id = useUniqId('media-input')
    const inputRef = useRef<HTMLInputElement>(null)
    const [alts, setAlts] = useState<Alt[]>([])
    const [loading, setLoading] = useState(false)

    const parsedValue: MediaValue = value || {
        id: null,
        alt: '',
        height: '',
        opacity: '1'
    }

    useEffect(() => {
        if (!parsedValue.id) {
            setAlts([])
            return
        }

        setLoading(true)
        jsonFetch(`/api/media/${parsedValue.id}/alts`)
            .then((data: Alt[]) => {
                setAlts(data)

                if (!parsedValue.alt && data.length > 0) {
                    onChange({
                        ...parsedValue,
                        alt: ''
                    })
                }
            })
            .catch(() => setAlts([]))
            .finally(() => setLoading(false))
    }, [parsedValue.id])

    useEffect(() => {
        if (inputRef.current) {
            const handleMediaChange = (e: CustomEvent) => {
                onChange({
                    id: e.detail.id || null,
                    alt: '',
                    height: parsedValue.height,
                    opacity: parsedValue.opacity
                })
            }

            inputRef.current.addEventListener('media', handleMediaChange as EventListener)

            return () => {
                inputRef.current?.removeEventListener('media', handleMediaChange as EventListener)
            }
        }
    }, [parsedValue.height, parsedValue.opacity])

    const handleAltChange = (e: Event) => {
        onChange({
            ...parsedValue,
            alt: (e.target as HTMLSelectElement).value
        })
    }

    const handleHeightChange = (e: Event) => {
        onChange({
            ...parsedValue,
            height: (e.target as HTMLInputElement).value
        })
    }

    const handleOpacityChange = (e: Event) => {
        onChange({
            ...parsedValue,
            opacity: (e.target as HTMLInputElement).value
        })
    }

    const showAltSelect = alts.length > 1
    const hasMedia = !!parsedValue.id

    return (
        <>
            <div className="form-group">
                {options.label && <label>{options.label}</label>}
                <input
                    ref={inputRef}
                    type="text"
                    id={id}
                    name={id}
                    is="input-media"
                    data-endpoint="/api/media"
                    defaultValue={parsedValue.id || ''}
                    style={{display: 'none'}}
                />
            </div>
            {hasMedia && showAltSelect && !loading && (
                <div className="form-group margin-block-start-4">
                    <label htmlFor={`${id}-alt`}>
                        {translate('core-cms.admin.editor.sidebar.tabs.media.alt')}
                    </label>
                    <select
                        id={`${id}-alt`}
                        className="form-control"
                        value={parsedValue.alt}
                        onChange={handleAltChange}
                    >
                        <option value="">
                            {alts.find(a => a.isDefault)?.text || translate('core-cms.admin.editor.sidebar.tabs.media.alt.default')}
                        </option>
                        {alts
                            .filter(a => !a.isDefault)
                            .map(alt => (
                                <option key={alt.id} value={alt.id.toString()}>
                                    {alt.text}
                                </option>
                            ))}
                    </select>
                </div>
            )}
            {hasMedia && (
                <>
                    <Field
                        id={`${id}-height`}
                        value={parsedValue.height}
                        step={1}
                        type="number"
                        label={translate('core-cms.admin.editor.sidebar.tabs.media.width.value')}
                        help={translate('core-cms.admin.editor.sidebar.tabs.media.width.help')}
                        onInput={handleHeightChange}
                    />
                    <Field
                        id={`${id}-opacity`}
                        value={parsedValue.opacity}
                        step={0.01}
                        min={0}
                        max={1}
                        type="number"
                        label={translate('core-cms.admin.editor.sidebar.tabs.background.image.opacity')}
                        onInput={handleOpacityChange}
                    />
                </>
            )}
        </>
    )
}

export const Media = defineField<MediaFieldArgs, MediaValue>({
    defaultOptions: {
        default: null,
        canAnimate: false
    },
    render: Component,
})