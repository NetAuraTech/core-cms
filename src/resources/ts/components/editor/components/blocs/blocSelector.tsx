import {EditorComponentDefinitions} from "../../types";
import {
    useAddBlock,
    useBlocSelectionVisible,
    useFieldDefinitions,
    useHiddenCategories,
    useSetBlockIndex
} from "../../store";
import {Tabs} from "../ui/Tabs";
import {BlocSelectorItem} from "./blocSelectorItem";
import {translate} from "../../../../shared/functions/i18n";
import Icon from "../../../../shared/components/Icon";
import {CSSProperties, useEffect, useMemo, useRef, useState} from "preact/compat";

const ALL_TAB = translate('core-cms.admin.editor.sidebar.component.all')

export function BlocSelector() {
    const isVisible = useBlocSelectionVisible();
    const modalRef = useRef<HTMLElement | null>(null);
    const setBlockIndex = useSetBlockIndex()
    const [search, setSearch] = useState('')
    const definitions = useFieldDefinitions();
    const hiddenCategories = useHiddenCategories();
    const addBlock = useAddBlock();

    const categories = useMemo(() => {
        return [
            ALL_TAB,
            ...Object.values(definitions)
                .filter(d => d.category)
                .filter(d => !hiddenCategories.includes(d.category ?? ''))
                .reduce(
                    (acc, d) => (acc.includes(d.category!) ? acc : [...acc, d.category!]),
                    [] as string[],
                ),
        ]
    }, [definitions])

    const onClose = () => {
        setBlockIndex(null)
    }

    const handleClose = () => {
        if (modalRef.current) {
            modalRef.current?.dispatchEvent(new Event('close'));
        }
    }

    useEffect(() => {
        if (modalRef.current instanceof HTMLElement) {
            modalRef.current.addEventListener('close', onClose);
        }

        setSearch('');

        return () => {
            if (modalRef.current instanceof HTMLElement) {
                modalRef.current.removeEventListener("close", onClose);
            }
        };
    }, [isVisible])

    if (!isVisible) {
        return null
    }

    // @ts-ignore
    return <modal-dialog ref={modalRef} overlay-close={true}>
        <div className="card grid" style={{
            maxWidth: 1290,
            width: '100%',
            height: 750,
            gridTemplateRows: '64px 1fr'
        } as CSSProperties}>
            <div className="flex-group align-items-center justify-content-space-between"
                 style={{width: 'initial'} as CSSProperties}>
                <h2 className="heading-2">
                    {translate('core-cms.admin.editor.sidebar.component.add')}
                </h2>
                <div className="form-group">
                    <input
                        type='search'
                        placeholder={translate('core-cms.admin.editor.sidebar.component.search')}
                        value={search}
                        // @ts-ignore
                        onChange={e => setSearch(e.target.value)}
                    />
                </div>
                <button
                    className="button padding-0"
                    data-type="transparent"
                    type="button"
                    onClick={handleClose}
                    title={translate('core-cms.admin.editor.sidebar.close')}
                >
                    <Icon name="cross" additionalClass="small"/>
                </button>
            </div>
            <Tabs>
                {categories.map(category => (
                    <Tabs.Tab className="grid margin-block-start-4" style={{ gridTemplateColumns: 'repeat(4, 1fr)', overflow: 'auto', maxHeight: 510 } as CSSProperties} key={category} title={category}>
                        {Object.keys(definitions)
                            .filter(
                                key =>
                                    !hiddenCategories.includes(
                                        definitions[key]!.category ?? '',
                                    ),
                            )
                            .filter(searchDefinition(search ?? '', category, definitions))
                            .map(key => (
                                <BlocSelectorItem
                                    key={key}
                                    definition={definitions[key]!}
                                    name={key}
                                    onClick={() => addBlock(key)}
                                />
                            ))}
                    </Tabs.Tab>
                ))}
            </Tabs>
        </div>
        {/*@ts-ignore */}
    </modal-dialog>
}

function searchDefinition(
    search: string,
    category: string,
    definitions: EditorComponentDefinitions,
) {
    return (key: string) => {
        const categoryFilter =
            category === ALL_TAB ? true : definitions[key]!.category === category
        const searchFilter =
            search === ''
                ? true
                : definitions[key]!.title.toLowerCase().includes(search.toLowerCase())
        return categoryFilter && searchFilter
    }
}
