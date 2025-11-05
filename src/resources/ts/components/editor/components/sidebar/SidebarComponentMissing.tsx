import {SidebarComponentMissingProps} from "../../types";
import {useRemoveBloc} from "../../store";
import {IconTrash} from "../ui/Icons";
import {translate} from "../../../../shared/functions/i18n";
import {prevent} from "../../../../shared/functions/functions";
import {CSSProperties} from "preact/compat";

export function SidebarComponentMissing({data}: SidebarComponentMissingProps) {
    const removeBloc = useRemoveBloc()
    return (
        <div className={'sidebar__sortable missing'} style={{position: 'relative'} as CSSProperties}>
            <div className="drag"></div>
            <div className="flex-group align-items-center justify-content-space-between padding-inline-4"
                 style={{width: "initial", flexWrap: 'initial'} as CSSProperties}>
                <h3 className="heading-3">
                    <strong>{`${translate('core-cms.admin.editor.sidebar.component.unknown')} : ${data._name}`}</strong>
                </h3>
                <button
                    className="button padding-0"
                    data-type="transparent"
                    onClick={prevent(() => removeBloc(data))}
                    title={translate('core-cms.admin.editor.component.delete')}
                >
                    <IconTrash/>
                </button>
            </div>
        </div>
    )
}
