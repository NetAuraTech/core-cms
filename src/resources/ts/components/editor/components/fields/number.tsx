import {useUniqId} from "../../../../shared/functions/hooks";
import {defineField} from "./utils";
import {Field} from "../ui/field";
import {JSX} from "preact";

export type NumberFieldArgs = {
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
    const id = useUniqId('numberinput')
    return (
        <Field
            id={id}
            label={options.label}
            type='number'
            value={value}
            help={options.help}
            onInput={e => onChange((e.target as HTMLInputElement).value)}
            step={options.step}
            min={options.min}
            max={options.max}
        />
    )
}

export const Number = defineField<NumberFieldArgs, string>({
    defaultOptions: {
        default: '',
        canAnimate: false
    },
    render: Component,
})
