import {EditorManagerProps} from "./types";
import {
    useData,
    useSetSidebarWidth,
    useSidebarWidth,
    useUpdateData,
} from "./store";
import {stringifyFields} from "../../functions/object";
import {useClipboardPaste} from "./hooks/useClipboardPaste";
import parse from "html-react-parser";
import {CSSProperties, RefObject, useEffect, useMemo, useRef, useState} from "preact/compat";
import {Sidebar} from "./components/sidebar/sidebar";
import {Preview} from "./components/preview/preview";
import {BlocSelector} from "./components/blocs/blocSelector";
import {useStopPropagation, useToggle, useUpdateEffect} from "../../shared/functions/hooks";
import {translate} from "../../shared/functions/i18n";

export function EditorManager({
                                  inner,
                                  value,
                                  previewUrl,
                                  name,
                                  element,
                                  iconsUrl,
                                  visible: visibleProps,
                                  onChange,
                              }: EditorManagerProps) {
    const [skipNextChange, setSkipNextChange] = useState(true);
    const updateData = useUpdateData();
    const data = useData();
    const [visible, toggleVisible] = useToggle(false);
    const sidebarWidth = useSidebarWidth()
    const [sidebarCollapsed, toggleSidebar] = useToggle(false)
    const showResizeControl = !sidebarCollapsed
    const [drag, setDrag] = useState(false)
    const setSidebarWidth = useSetSidebarWidth()

    const handleClose = () => {
        toggleVisible()
    }

    const doNothing = () => null;

    const cleanedData = useMemo(() => stringifyFields(data), [data]);

    useUpdateEffect(() => {
        setSkipNextChange(true)
        updateData(value)
    }, [value]);

    useClipboardPaste(visible)

    useEffect(() => {
        if (skipNextChange) {
            setSkipNextChange(false)
        } else {
            onChange(cleanedData)
        }
    }, [cleanedData]);

    const div = useRef<HTMLElement | null>(null);

    useStopPropagation(div as RefObject<HTMLElement>, ['change', 'close']);

    const handleMouseDown = (e: Event) => {
        e.stopPropagation();
        e.preventDefault();
        setDrag(true);
        const listener = (e: MouseEvent) => {
            setSidebarWidth(Math.round((100 * e.clientX) / window.innerWidth))
        }
        document.documentElement.addEventListener('mousemove', listener);
        document.documentElement.addEventListener(
            'mouseup',
            () => {
                setDrag(false);
                document.documentElement.removeEventListener('mousemove', listener);
            },
            {once: true},
        )
    }

    return <div>
        <button
            type="button"
            className="button"
            data-type="accent"
            onClick={toggleVisible}
        >
            {translate('core-cms.admin.content.edit')}
        </button>
        {visible && (
            <div
                className={`editor`}
                style={{'--sidebar': `${sidebarWidth}vw`} as CSSProperties}
            >
                <Sidebar data={data} onClose={handleClose} />
                {previewUrl && <Preview data={data} previewUrl={previewUrl} />}
                <BlocSelector iconsUrl={iconsUrl} />
            </div>
        )}
        <textarea hidden name={name} value={cleanedData} onChange={doNothing}/>
        {parse(inner)}
    </div>
}
