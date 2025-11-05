import {SidebarFieldsProps} from "../../types";
import {useUpdateData} from "../../store";
import {FieldsRenderer} from "./FieldsRenderer";

export function SidebarFields({fields, data, path}: SidebarFieldsProps) {
    const updateData = useUpdateData()
    return (
        <FieldsRenderer
            fields={fields}
            data={data}
            onUpdate={updateData}
            path={path}
        />
    )
}
