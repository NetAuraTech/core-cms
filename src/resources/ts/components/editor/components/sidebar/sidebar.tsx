import {EditorComponentData} from "../../types";
import {CSSProperties, useCallback, useState} from "preact/compat";
import {
    useFieldDefinitions,
    usePreviewMode,
    useSetBlockIndex,
    useTemplates,
    useTogglePreviewMode,
    useUpdateData
} from "../../store";
import {PreviewModes} from "../../enum";
import {moveItem} from "../../../../functions/array";
import {prevent} from "../../../../shared/functions/functions";
import {translate} from "../../../../shared/functions/i18n";
import Icon from "../../../../shared/components/Icon";
import {CopyAction} from "./actions/copyAction";
import {SidebarEmpty} from "./sidebarEmpty";
import {SortableWrapper} from "../Sortable";
import {SidebarComponent} from "./SidebarComponent";
import {SidebarTemplates} from "./SidebarTemplates";


enum States {
    BLOCS,
    TEMPLATES,
}

export function Sidebar({
                            data,
                            onClose,
                            ...props
                        }: {
    data: EditorComponentData[]
    onClose: () => void
}) {
    const [state, setState] = useState(States.BLOCS);
    const templates = useTemplates();
    const toggleMode = useCallback(() => {
        setState(v => (v === States.BLOCS ? States.TEMPLATES : States.BLOCS))
    }, []);
    const hasTemplates = templates.length > 0;
    const showEmpty = data.length === 0 && hasTemplates;
    const isTemplateMode = state === States.TEMPLATES;

    const togglePreviewMode = useTogglePreviewMode();
    const previewMode = usePreviewMode();
    const isPhone = previewMode === PreviewModes.PHONE;
    const setAddBlock = useSetBlockIndex();

    const updateData = useUpdateData()
    const definitions = useFieldDefinitions()
    const handleMove = (from: number, to: number) => {
        updateData(moveItem(data, from, to))
    }

    return (
        <div className='sidebar' {...props}>
            <div className='sidebar__header'>
                <button
                    className='button padding-0'
                    data-type="transparent"
                    type={'button'}
                    onClick={prevent(onClose)}
                    title={translate('core-cms.admin.editor.sidebar.close')}
                >
                    <Icon name={'cross'} additionalClass="small"/>
                </button>
                <div className='flex-group align-items-center'>
                    {hasTemplates && (
                        <button
                            className='button padding-0'
                            data-type="transparent"
                            type={'button'}
                            onClick={prevent(toggleMode)}
                            aria-label={isTemplateMode ? translate('core-cms.admin.editor.sidebar.component.add') : translate('core-cms.admin.editor.sidebar.template.use')}
                        >
                            <Icon name={isTemplateMode ? 'block' : 'template'} additionalClass="small"/>
                        </button>
                    )}
                    <CopyAction data={data}/>
                    <button
                        className='button padding-0'
                        data-type="transparent"
                        type={'button'}
                        onClick={prevent(togglePreviewMode)}
                        title={translate('core-cms.admin.editor.sidebar.mode.responsive')}
                    >
                        <Icon name={'responsive'} additionalClass="small"/>
                    </button>
                    <button
                        className='button flex-group align-items-center'
                        data-type="primary"
                        type={'button'}
                        onClick={prevent(() => setAddBlock())}
                    >
                        <Icon name={'plus'} additionalClass="small" />
                        {translate('core-cms.admin.editor.sidebar.component.add')}
                    </button>
                </div>
            </div>
            {state === States.BLOCS &&
                (showEmpty ? (
                    <SidebarEmpty onAction={() => setState(States.TEMPLATES)}/>
                ) : (
                    <div className='grid padding-4' style={{overflowY: "auto", overflowX: "hidden"} as CSSProperties}>
                        <SortableWrapper items={data} onMove={handleMove}>
                            {data.map((v, k) => (
                                <SidebarComponent
                                    key={v._id}
                                    data={v}
                                    definition={definitions[v._name]}
                                    path={`${k}`}
                                />
                            ))}
                        </SortableWrapper>
                    </div>
                ))}
            {state === States.TEMPLATES && (
                <SidebarTemplates onTemplate={() => setState(States.BLOCS)} />
            )}
            <div className='sidebar__footer'>
                <button
                    className="button flex-group align-items-center"
                    data-type="primary"
                    type={'submit'}
                >
                    <Icon additionalClass="small" name={'save'}/>
                    {translate('core-cms.admin.save')}
                </button>
            </div>
        </div>
    )
}
