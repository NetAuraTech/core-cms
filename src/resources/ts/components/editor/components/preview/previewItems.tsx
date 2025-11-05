import {EditorComponentData} from "../../types";
import {useFieldDefinitions, useSetBlockIndex} from "../../store";
import {PreviewItem} from "./previewItem";
import {prevent} from "../../../../shared/functions/functions";
import {translate} from "../../../../shared/functions/i18n";
import Icon from "../../../../shared/components/Icon";

export function PreviewItems({
                                 data,
                                 initialHTML = {},
                                 previewUrl,
                             }: {
    data: EditorComponentData[]
    initialHTML: Record<string, string>
    previewUrl: string
}) {
    const definitions = useFieldDefinitions();
    const setAddBlock = useSetBlockIndex();

    return (
        <>
            {data.map((v, k) => (
                <div key={v._id}>
                    <button
                        className={'editor__add-button-floating'}
                        // @ts-ignore
                        onClick={prevent(() => setAddBlock(k))}
                    >
                            <span>
                                {translate('core-cms.admin.editor.sidebar.component.add')}
                            </span>
                    </button>
                    <PreviewItem
                        title={definitions[v._name]?.title || ''}
                        data={v}
                        initialHTML={initialHTML[v._id] || ''}
                        previewUrl={previewUrl}
                    />
                </div>
            ))}
            <div className={'preview__add-button margin-4'}>
                <button
                    className="button padding-4 flex-group align-items-center"
                    data-type="transparent"
                    type={'button'}
                    onClick={prevent(() => setAddBlock())}
                >
                    <Icon name="plus" additionalClass="small"/>
                    {translate('core-cms.admin.editor.sidebar.component.add')}
                </button>
            </div>
        </>
    )
}
