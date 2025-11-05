import {useUniqId} from "../../../../shared/functions/hooks";
import {defineField} from "./utils";
import {Field} from "../ui/field";
import {JSX} from "preact";

export type RangeFieldArgs = {
    label?: string
    multiline?: boolean
    help?: string
    default?: string
    step?: number
    min?: number
    max?: number
    canAnimate?: boolean
}

const Component: ({value, onChange, options}: { value: any; onChange: any; options: any }) => JSX.Element = ({
                                                          value,
                                                          onChange,
                                                          options,
                                                      }) => {
    const id = useUniqId('rangeinput')
    return (
        <Field
            id={id}
            label={options.label}
            type='range'
            value={value}
            help={options.help}
            onInput={e => onChange((e.target as HTMLInputElement).value)}
            step={options.step}
            min={options.min}
            max={options.max}
        />
    )
}

export const Range = defineField<RangeFieldArgs, string>({
    defaultOptions: {
        default: '',
        canAnimate: false
    },
    render: Component,
})
