import {useUniqId} from "../../../../shared/functions/hooks";
import {Field} from "../ui/field";
import {defineField} from "./utils";
import {JSX} from "preact";


export type TextFieldArgs = {
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
    const id = useUniqId('textinput')
    return (
        <Field
            id={id}
            label={options.label}
            type={options.multiline ? 'textarea' : 'text'}
            value={value}
            help={options.help}
            onInput={e => onChange((e.target as HTMLInputElement).value)}
        />
    )
}

export const Text = defineField<TextFieldArgs, string>({
    defaultOptions: {
        default: '',
        canAnimate: false
    },
    render: Component,
})
