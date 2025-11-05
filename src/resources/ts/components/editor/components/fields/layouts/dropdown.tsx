import {JSX} from "preact";
import {CSSProperties, useMemo} from "preact/compat";
import {defineFieldGroup} from "../utils";
import {useToggle} from "../../../../../shared/functions/hooks";
import {textContent} from "../../../../../shared/functions/string";
import {prevent} from "../../../../../shared/functions/functions";
import {IconDown} from "../../ui/Icons";


export type DropdownArgs = {
    label?: string
    collapsed?: string
}

const DropdownComponent: ({options, children}: { options: any; children: any }) => JSX.Element = ({ options, children }) => {
    const [collapsed, toggleCollapsed] = useToggle(true)

    const title = collapsed
        ? (options.collapsed as string) ?? options.label
        : (options.label as string) ?? ''
    const escapedTitle = useMemo(() => textContent(title), [title])


    return (
        <div class="sidebar__sortable" style={{position: 'relative'} as CSSProperties}>
            <div className="flex-group align-items-center justify-content-space-between padding-inline-4"
                 style={{width: "initial", flexWrap: 'initial'} as CSSProperties}>
                <h3 className="heading-3" style={{width: '100%', cursor: 'pointer'} as CSSProperties}
                    onClick={prevent(toggleCollapsed)}>
                    <strong>{escapedTitle}</strong>
                </h3>
                <div className="flex-group align-items-center" style={{flexWrap: 'initial'} as CSSProperties}>
                    <button
                        className="button padding-1"
                        data-type="primary"
                        onClick={prevent(toggleCollapsed)}
                        style={{rotate: `${collapsed ? -90 : 0}deg`, borderRadius: '50%'} as CSSProperties}
                    >
                        <IconDown/>
                    </button>
                </div>
            </div>
            {!collapsed && (
                <div className={'grid margin-block-4'}>
                    {children}
                </div>
            )}
        </div>
    )
}

export const Dropdown = defineFieldGroup<DropdownArgs>({
    defaultOptions: {},
    render: DropdownComponent,
})