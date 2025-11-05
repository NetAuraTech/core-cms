import {useUniqId} from "../../../../shared/functions/hooks";
import {defineField} from "./utils";
import {Field} from "../ui/field";
import {JSX} from "preact";

export type DatePickerFieldArgs = {
    label?: string
    multiline?: boolean
    help?: string
    default?: string
    canAnimate?: boolean
}

const Component: ({value, onChange, options}: { value: any; onChange: any; options: any }) => JSX.Element = ({
                                                          value,
                                                          onChange,
                                                          options,
                                                      }) => {
    const id = useUniqId('datepickerinput')
    return (
        <Field
            id={id}
            label={options.label}
            type={'datepicker'}
            value={value}
            help={options.help}
            onInput={e => onChange((e.target as HTMLInputElement).value)}
        />
    )
}

export const DatePicker = defineField<DatePickerFieldArgs, string>({
    defaultOptions: {
        default: '',
        canAnimate: false
    },
    render: Component,
})
