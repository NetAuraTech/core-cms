import {JSX} from "preact";
import {CSSProperties} from "preact/compat";
import {defineFieldGroup} from "../utils";


export type RowArgs = {
    label?: string
}

const RowComponent: ({options, children}: { options: any; children: any }) => JSX.Element = ({ options, children }) => {
    return (
        <div className="form-group">
            {options.label && <label style={{fontSize: '1.5rem'} as CSSProperties}>{options.label}</label>}
            <div className="flex-group align-items-center form-group margin-block-end-6" style={{alignItems: 'flex-start'} as CSSProperties}>
                {children}
            </div>
            <hr/>
        </div>
    )
}

export const Row = defineFieldGroup<RowArgs>({
    defaultOptions: {},
    render: RowComponent,
})
