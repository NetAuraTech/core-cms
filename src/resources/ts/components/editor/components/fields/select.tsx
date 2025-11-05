import {JSX} from "preact";
import {useUniqId} from "../../../../shared/functions/hooks";
import {Field} from "../ui/field";
import {defineField} from "./utils";


export type SelectOption = {
    value: string
    label: string
}

export type SelectFieldArgs = {
    label?: string
    options: SelectOption[]
    help?: string
    default?: string
    canAnimate?: boolean
}

const Component: ({value, onChange, options}: { value: any; onChange: any; options: any }) => JSX.Element = ({
                                                          value,
                                                          onChange,
                                                          options,
                                                      }) => {
    const id = useUniqId('selectinput')
    return (
        <Field
            id={id}
            label={options.label}
            type="select"
            help={options.help}
            options={options.options}
            value={value}
            onInput={e => onChange((e.target as HTMLSelectElement).value)}
        />
    )
}

export const Select = defineField<SelectFieldArgs, string>({
    defaultOptions: {
        default: '',
        options: [],
        canAnimate: false
    },
    render: Component,
})
