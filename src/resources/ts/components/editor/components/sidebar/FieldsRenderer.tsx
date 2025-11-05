import {EditorComponentDefinition, FieldProps} from "../../types";
import {useCallback} from "preact/compat";

type FieldsRendererProps = {
    fields: EditorComponentDefinition['fields']
    data: Record<string, unknown>
    onUpdate: (value: unknown, path: string) => void
    path: string
}

export function FieldsRenderer({
                                   data,
                                   fields,
                                   path,
                                   onUpdate,
                               }: FieldsRendererProps) {
    return (
        <>
            {fields
                .filter(field => field.shouldRender(data))
                .map((field, k) =>
                    field.group ? (
                        <field.render key={k} options={field.options}>
                            <FieldsRenderer
                                fields={field.fields}
                                data={data}
                                path={path}
                                onUpdate={onUpdate}
                            />
                        </field.render>
                    ) : (
                        <Field
                            key={field.name}
                            field={field}
                            value={field.name ? data[field.name] : undefined}
                            path={`${path}.${field.name}`}
                            extraProps={field.extraProps ? field.extraProps(data) : undefined}
                            onUpdate={onUpdate}
                        />
                    ),
                )}
        </>
    )
}

function Field({
                   field,
                   value,
                   path,
                   extraProps,
                   onUpdate,
               }: FieldProps) {
    const Component = field.render
    const handleChange = useCallback(
        (v: unknown) => {
            onUpdate(v, path)
        },
        [path, onUpdate],
    )

    return (
        <Component
            value={value}
            onChange={handleChange}
            options={field.options}
            {...extraProps}
        />
    )
}
