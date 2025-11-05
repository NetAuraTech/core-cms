import {SidebarComponentProps} from "../../types";
import {CSSProperties, memo, useMemo, useRef} from "preact/compat";
import {useFieldFocused, useRemoveBloc, useSetFocusIndex} from "../../store";
import {strToDom} from "../../../../shared/functions/dom";
import {prevent} from "../../../../shared/functions/functions";
import {CopyAction} from "./actions/copyAction";
import {translate} from "../../../../shared/functions/i18n";
import {IconDown, IconTrash} from "../ui/Icons";
import {SidebarFields} from "./sidebarFields";
import {useToggle, useUpdateEffect} from "../../../../shared/functions/hooks";
import {SidebarComponentMissing} from "./SidebarComponentMissing";

export const SidebarComponent = memo(function SidebarItem({
                                                              data,
                                                              definition,
                                                              path,
                                                          }: SidebarComponentProps) {
    const ref = useRef<HTMLDivElement | null>(null)
    const isFocused = useFieldFocused(data._id)
    const [isCollapsed, toggleCollapsed, setCollapsed] = useToggle(!isFocused)
    const removeBloc = useRemoveBloc()
    const setFocusIndex = useSetFocusIndex()
    const label =
        definition?.label && data[definition.label] ? data[definition.label] : null

    useUpdateEffect(() => {
        if (isFocused) {
            setCollapsed(false)
            window.setTimeout(
                () =>
                    ref.current!.scrollIntoView({behavior: 'smooth', block: 'start'}),
                100,
            )
        } else {
            setCollapsed(true)
        }
    }, [isFocused])

    const labelHTMLSafe = useMemo(
        () => (label?.includes('<') ? strToDom(label).innerText : label),
        [label],
    )

    const handleRemove = () => {
        removeBloc(data)
    }

    const focusBloc = () => {
        if (isCollapsed) {
            setFocusIndex(path)
        }
        toggleCollapsed()
    }

    if (!definition) {
        return <SidebarComponentMissing data={data}/>
    }

    return (
        <div className={'sidebar__sortable'} style={{position: 'relative'} as CSSProperties}>
            <div className="drag"></div>
            <div className="flex-group align-items-center justify-content-space-between padding-inline-4"
                 style={{width: "initial", flexWrap: 'initial'} as CSSProperties} ref={ref}>
                <h3 className="heading-3" style={{width: '100%', cursor: 'pointer'} as CSSProperties}
                    onClick={prevent(focusBloc)}>
                    <strong>{definition.title}</strong>
                    {isCollapsed ? labelHTMLSafe : null}
                </h3>
                <div className="flex-group align-items-center" style={{flexWrap: 'initial'} as CSSProperties}>
                    <CopyAction data={data}/>
                    <button
                        className="button padding-0 clr-red-400"
                        data-type="transparent"
                        onClick={handleRemove}
                        title={translate('core-cms.admin.editor.component.delete')}
                    >
                        <IconTrash/>
                    </button>
                    <button
                        className="button padding-1"
                        data-type="primary"
                        onClick={prevent(toggleCollapsed)}
                        style={{rotate: `${isCollapsed ? -90 : 0}deg`, borderRadius: '50%'} as CSSProperties}
                    >
                        <IconDown/>
                    </button>
                </div>
            </div>
            {!isCollapsed && (
                <div className="grid margin-block-4">
                    <SidebarFields fields={definition.fields} data={data} path={path}/>
                </div>
            )}
        </div>
    )
})