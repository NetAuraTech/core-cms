import {prevent} from "../../../../shared/functions/functions";
import {EditorComponentTemplate} from "../../types";
import {useSetData, useTemplates} from "../../store";
import {useCallback, useState} from "preact/compat";

export function SidebarTemplates({ onTemplate }: { onTemplate: () => void }) {
    const templates = useTemplates()

    const setData = useSetData()
    const [loadingTemplate, setLoadingTemplate] = useState<EditorComponentTemplate>()

    const callback = useCallback(
        async (t: EditorComponentTemplate) => {
            setLoadingTemplate(t)
            let data: EditorComponentTemplate['data']
            if (typeof t.data === 'function') {
                setLoadingTemplate(t)
                data = await t.data().catch(() => [])
                setLoadingTemplate(t)
            } else {
                data = t.data
            }
            setData(data)
            onTemplate()
        },
        [setData, onTemplate],
    )
    return (
        <div className='sidebar__templates padding-4'>
            {templates.map(t => (
                <TemplateCard
                    template={t}
                    onClick={callback}
                    loading={loadingTemplate === t}
                />
            ))}
        </div>
    )
}

function TemplateCard({
                          template,
                          onClick,
                          loading,
                      }: {
    template: EditorComponentTemplate
    onClick: (t: EditorComponentTemplate) => void
    loading: boolean
}) {
    return (
        <div
            className={`sidebar__templates-card ${loading && 'loading'}`}
            onClick={prevent(() => (loading ? null : onClick(template)))}
        >
            <img src={template.image} alt='' />
            <div className='sidebar__templates-card-body'>
                <div className='heading-3'>
                    {template.name}
                </div>
                <div>{template.description}</div>
            </div>
        </div>
    )
}