import {PreviewModes} from "./enum";
import {UseBoundStore} from "zustand";
import {createStore} from "zustand/vanilla";
import { InsertPosition } from './enum'
import {ComponentChildren, FunctionComponent} from "preact";

export type EditorComponentData = {
    _id: string
    _name: string
    [key: string]: any
};

export type EditorComponentDefinition = {
    // Bloc id
    _id: string
    // Bloc title
    title: string
    // Field used for the block label
    label?: string
    // Fields inside the block
    fields: Array<FieldDefinition<any, any>>
    // Block category
    category?: string
};

export type EditorComponentDefinitions = Record<
    string,
    EditorComponentDefinition
>;

export type EditorComponentTemplate = {
    name: string
    description: string
    image: string
    data:
        | Omit<EditorComponentData, '_id'>[]
        | (() => Promise<Omit<EditorComponentData, '_id'>[]>)
};

export type FieldDefinition<O = Record<string, unknown>, V = unknown> =
    | SingleFieldDefinition<O, V>
    | FieldGroupDefinition<O>;

export type SingleFieldDefinition<O, V> = {
    name: string
    options: O
    render: FieldComponent<O, V>
    shouldRender: (data: Record<string, unknown>) => boolean
    group?: false
    extraProps?: (data: Record<string, unknown>) => Record<string, any>
    when: (fieldName: string, expectedValue?: unknown) => SingleFieldDefinition<O, V>;
};

export type FieldGroupDefinition<O> = {
    options: O
    render: FieldGroupComponent<O>
    shouldRender: (data: Record<string, unknown>) => boolean
    group: true
    fields: FieldDefinition[]
    when: (fieldName: string, expectedValue?: unknown) => FieldGroupDefinition<O>;
};

export type FieldGroupComponent<O> = FunctionComponent<{
    options: O
    children: ComponentChildren
}>;

export type FieldComponent<
    FieldOptions,
    FieldValue,
    FieldExtraParams = {},
> = FunctionComponent<
    {
        value: FieldValue
        onChange: (v: FieldValue) => void
        options: FieldOptions
    } & FieldExtraParams
>;

export type FieldCondition = (data: Record<string, any>) => boolean

export type StoreState = {
    data: EditorComponentData[];
    definitions: EditorComponentDefinitions;
    hiddenCategories: string[];
    rootElement: HTMLElement;
    templates: EditorComponentTemplate[];
    insertPosition: InsertPosition;
    previousData: EditorComponentData[];
    rollbackMessage: string | null;
    addBlockIndex: number | null;
    focusIndex: string | null;
    previewMode: PreviewModes;
    sidebarWidth: number;
};

export type StoreActions = {
    setSidebarWidth: (width: number) => void;
    updateData: (newData: any, path?: string) => void;
    removeBloc: (removedData: EditorComponentData) => void;
    rollback: () => void;
    voidRollback: () => void;
    insertData: (
        name: string,
        index: number,
        extraData?: object
    ) => EditorComponentData;
    setData: (newData: Omit<EditorComponentData, '_id'>[]) => void;
    setFocusIndex: (id: string | null) => void;
    setAddBlockIndex: (index?: number | null) => void;
    togglePreviewMode: () => void;
};

export type StoreProviderState = StoreState & StoreActions;

export type Store = ReturnType<typeof createStore>;

export type StoreData<T extends UseBoundStore<any>> = T extends UseBoundStore<infer V>
    ? V
    : never;

export type EditorManagerProps = {
    inner?: any,
    value: EditorComponentData[]
    previewUrl: string
    name: string
    iconsUrl: string
    visible: boolean
    element: Element
    onChange: (v: string) => void
}

export type BlocSelectorProps = {
    iconsUrl: string
}

export interface IndexableObject {
    _id: string
}

export type SidebarComponentMissingProps = {
    data: EditorComponentData
}


export type SidebarComponentProps = {
    data: EditorComponentData
    definition?: EditorComponentDefinition
    path: string
}

export type SidebarFieldsProps = {
    fields: EditorComponentDefinition['fields']
    data: EditorComponentData
    path: string
}

export type FieldProps = {
    field: FieldDefinition & { group?: false }
    value: unknown
    onUpdate: (value: unknown, path: string) => void
    path: string
    extraProps?: Record<string, any>
}
