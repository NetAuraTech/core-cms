import {FieldComponent} from "../../types";
import {CSSProperties} from "preact/compat";
import {defineField} from "./utils";
import {useUniqId} from "../../../../shared/functions/hooks";


type FieldArgs = {
    label?: string
    help?: string
    default?: boolean
    canAnimate?: boolean
}

const Component: FieldComponent<FieldArgs, boolean> = ({
                                                          value,
                                                          onChange,
                                                          options,
                                                      }) => {
    const id = useUniqId('checkboxinput')
    return (
        <div className="form-group">
            <div className="form-switch">
                <input type="checkbox"
                       id={id}
                       name={id}
                       role="switch"
                       checked={value}
                       className="form-control"
                       onChange={() => onChange(!value)}
                />
                <label className="form-check-label" htmlFor={id}>
                    <span className="switch"></span>{options.label}
                </label>
            </div>
            {options.help && <div
                className={'clr-neutral-600'}
                style={{fontSize: '.85rem'} as CSSProperties}
            >{options.help}</div>}
        </div>
    )
}

export const Checkbox = defineField<FieldArgs, boolean>({
    defaultOptions: {
        default: false,
        canAnimate: false
    },
    render: Component,
})
