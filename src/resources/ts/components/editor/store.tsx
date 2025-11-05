import {createStore, StoreApi} from 'zustand/vanilla'
import {devtools, combine} from 'zustand/middleware'
import {
    EditorComponentData,
    EditorComponentDefinitions,
    EditorComponentTemplate, StoreActions, StoreProviderState, StoreState,
} from './types'
import {deepSet, indexify} from '../../functions/object'
import {InsertPosition} from './enum'
import {PreviewModes} from "./enum";
import {createContext, useCallback, useContext, useSyncExternalStore} from "preact/compat";
import {fillDefaults} from "../../functions/fields";
import {ComponentChildren} from "preact";
import {insertItem} from "../../functions/array";
import {clamp} from "lodash";
import {translate} from "../../shared/functions/i18n";
import {uniqId} from "../../shared/functions/string";

const sidebarWidth =
    typeof localStorage !== 'undefined'
        ? localStorage.getItem('veSidebarWidth')
        : 0

export function useStore<T, U>(
    store: StoreApi<T>,
    selector: (state: T) => U,
): U {
    return useSyncExternalStore(
        store.subscribe,
        () => selector(store.getState()),
    )
}

export const createEditorStore = (
    data: EditorComponentData[] = [],
    definitions: EditorComponentDefinitions,
    hiddenCategories: string[] = [],
    rootElement: HTMLElement,
    templates: EditorComponentTemplate[],
    insertPosition: InsertPosition,
) =>
    createStore(
        devtools(
            combine<StoreState, StoreActions>(
                {
                    data,
                    definitions,
                    hiddenCategories,
                    rootElement,
                    templates,
                    insertPosition,
                    previousData: [],
                    rollbackMessage: null,
                    addBlockIndex: null,
                    focusIndex: null,
                    previewMode: PreviewModes.DESKTOP,
                    sidebarWidth: clamp(
                        sidebarWidth ? parseInt(sidebarWidth, 10) : 33,
                        0,
                        window.innerWidth - 375,
                    ),
                },
                set => ({
                    setSidebarWidth: function (width: number) {
                        localStorage.setItem('veSidebarWidth', width.toString())
                        set(() => ({
                            sidebarWidth: width,
                        }))
                    },
                    updateData: function (newData: any, path?: string) {
                        return set(state => ({
                            data: deepSet(state.data, path, newData),
                        }))
                    },
                    removeBloc: function (removedData: EditorComponentData) {
                        set(({ data }) => ({
                            previousData: [...data],
                            data: data.filter(d => d !== removedData),
                            rollbackMessage: translate('core-cms.admin.editor.item.delete.confirmed'),
                        }))
                    },
                    rollback: function () {
                        return set(({previousData}) => ({
                            previousData: [],
                            rollbackMessage: null,
                            data: previousData,
                        }))
                    },
                    voidRollback: function () {
                        return set(() => ({
                            rollbackMessage: null,
                            previousData: [],
                        }))
                    },
                    insertData: function (
                        name: string,
                        index: number,
                        extraData?: object,
                    ): EditorComponentData {
                        const newData = {
                            ...extraData,
                            _name: name,
                            _id: name + uniqId(),
                        }
                        set(state => {
                            return {
                                data: insertItem(state.data, index, newData),
                                focusIndex: newData._id,
                            }
                        })
                        return newData
                    },
                    setData: function (
                        newData: Omit<EditorComponentData, '_id'>[],
                    ): void {
                        set(() => {
                            return {
                                data: indexify(newData) as EditorComponentData[],
                                focusIndex: null,
                            }
                        })
                    },
                    setFocusIndex: function (id: string | null) {
                        set(() => ({focusIndex: id}))
                    },
                    setAddBlockIndex: function (index?: number | null) {
                        if (index === undefined) {
                            set(state => ({
                                addBlockIndex:
                                    state.insertPosition === InsertPosition.End
                                        ? 0
                                        : state.data.length,
                            }))
                            return
                        }
                        set(() => ({addBlockIndex: index ?? null}))
                    },
                    togglePreviewMode: function () {
                        set(({previewMode}) => ({
                            previewMode:
                                previewMode === PreviewModes.DESKTOP
                                    ? PreviewModes.PHONE
                                    : PreviewModes.DESKTOP,
                        }))
                    },
                }),
            ),
        ),
    )

const StoreContext = createContext<ReturnType<typeof createEditorStore> | null>(null)

export function StoreProvider({
                                  children,
                                  data,
                                  definitions,
                                  hiddenCategories,
                                  rootElement,
                                  templates,
                                  insertPosition,
                              }: {
    children: ComponentChildren
    data: EditorComponentData[]
    templates: EditorComponentTemplate[]
    definitions: EditorComponentDefinitions
    hiddenCategories: string[]
    rootElement: HTMLElement
    insertPosition: InsertPosition
}) {
    const store = createEditorStore(data, definitions, hiddenCategories, rootElement, templates, insertPosition)
    return (
        <StoreContext.Provider
            value={store}>
            {children}
        </StoreContext.Provider>
    )
}

export function useEditorStore<T>(selector: (state: StoreProviderState) => T): T {
    const store = useContext(StoreContext) as StoreApi<StoreProviderState> | null;
    if (!store) throw new Error('useEditorStore must be used within a StoreProvider')
    return useStore(store, selector)
}

export function useData() {
    return useEditorStore(state => state.data)
}

export function useRootElement() {
    return useEditorStore(state => state.rootElement)
}

export function useUpdateData() {
    return useEditorStore(state => state.updateData)
}

export function useRemoveBloc() {
    return useEditorStore(state => state.removeBloc)
}

export function useInsertData() {
    return useEditorStore(state => state.insertData)
}

export function useSetData() {
    return useEditorStore(state => state.setData)
}

export function useFocusIndex() {
    return useEditorStore(state => state.focusIndex)
}

export function useDefinitions() {
    return useEditorStore(state => state.definitions)
}

export function useSetFocusIndex() {
    return useEditorStore(state => state.setFocusIndex)
}

export function useFieldFocused(id: string) {
    return useEditorStore(state => state.focusIndex === id)
}

export function usePreviewMode() {
    return useEditorStore(state => state.previewMode)
}

export function useTogglePreviewMode() {
    return useEditorStore(state => state.togglePreviewMode)
}

export function useSidebarWidth() {
    return useEditorStore(state => state.sidebarWidth)
}

export function useSetSidebarWidth() {
    return useEditorStore(state => state.setSidebarWidth)
}

export function useFieldDefinitions() {
    return useEditorStore(state => state.definitions)
}

export function useHiddenCategories() {
    return useEditorStore(state => state.hiddenCategories)
}

export function useBlocSelectionVisible(): boolean {
    return useEditorStore(state => state.addBlockIndex) !== null
}

export function useSetBlockIndex(): Function {
    return useEditorStore(state => state.setAddBlockIndex)
}

export function useTemplates(): EditorComponentTemplate[] {
    return useEditorStore(state => state.templates)
}

export function useAddBlock() {
    const insertData = useInsertData()
    const blockIndex = useEditorStore(state => state.addBlockIndex) || 0

    const definitions = useDefinitions()
    const setBlockIndex = useSetBlockIndex()
    return useCallback(
        (blocName: string) => {
            insertData(
                blocName,
                blockIndex,
                fillDefaults({}, definitions[blocName]!.fields),
            )
            setBlockIndex(null)
        },
        [insertData, blockIndex, definitions, setBlockIndex],
    )
}

export function useRollbackMessage() {
    const message = useEditorStore(state => state.rollbackMessage)
    const rollback = useEditorStore(state => state.rollback)
    const voidRollback = useEditorStore(state => state.voidRollback)
    return {message, rollback, voidRollback}
}

export const store = useEditorStore
