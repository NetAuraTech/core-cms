import {FieldDefinition} from "../../types";
import {FunctionComponent, JSX} from "preact";
import {CSSProperties, useMemo, useState} from "preact/compat";
import {fillDefaults} from "../../../../functions/fields";
import {textContent, uniqId} from "../../../../shared/functions/string";
import {deepSet} from "../../../../functions/object";
import {prevent} from "../../../../shared/functions/functions";
import Icon from "../../../../shared/components/Icon";
import {SortableWrapper} from "../Sortable";
import {moveItem} from "../../../../functions/array";
import {translate} from "../../../../shared/functions/i18n";
import {IconDown, IconTrash} from "../ui/Icons";
import {defineField} from "./utils";
import {FieldsRenderer} from "../sidebar/FieldsRenderer";
import {useToggle} from "../../../../shared/functions/hooks";


type FieldValue = RepeaterLine[]

export type RepeaterFieldArgs = {
    label?: string
    min?: number
    max?: number
    addLabel?: string
    fields: FieldDefinition<any, any>[]
    collapsed?: string
    default?: FieldValue
    canAnimate?: boolean
}

type RepeaterLine = { _id: string; [key: string]: unknown }

const Component: ({value, onChange, options}: {
    value: any;
    onChange: any;
    options: any
}) => JSX.Element = ({
                         value,
                         onChange,
                         options,
                     }) => {
    const valueLine: RepeaterLine[] = value ?? []
    const canAdd = !options.max || valueLine.length < options.max
    const canRemove = !options.min || valueLine.length > options.min
    const [lastAdditionIndex, setLastAdditionIndex] = useState(-1)
    const group = uniqId()

    const add = () => {
        onChange([
            ...valueLine,
            fillDefaults({_id: uniqId()}, options.fields) as RepeaterLine,
        ])
        setLastAdditionIndex(valueLine.length)
    }

    const remove = (line: Object) => {
        onChange(valueLine.filter(v => v !== line))
    }

    const updateProperty = (v: unknown, path: string) => {
        onChange(deepSet(valueLine, path, v))
    }

    function handleMove(from: number, to: number) {
        onChange(moveItem(valueLine, from, to))
    }

    return (
        <div className="form-group">
            {options.label && <label>{options.label}</label>}
            <div
                className={`padding-1`}
            >
                <SortableWrapper items={valueLine} onMove={handleMove} group={group}>
                    {valueLine.map((line, k) => (
                        <FieldLine
                            key={line._id}
                            line={line}
                            index={k}
                            onUpdate={updateProperty}
                            onRemove={canRemove ? remove : null}
                            options={options}
                            defaultCollapsed={lastAdditionIndex !== k}
                        />
                    ))}
                </SortableWrapper>
                {canAdd && (
                    <div
                        className="padding-4"
                        style={{backgroundColor: 'var(--neutral-300)'} as CSSProperties}
                    >
                        <button
                            className="button flex-group align-items-center"
                            data-type="accent"
                            type="button"
                            onClick={prevent(add)}
                        >
                            <Icon name="plus" additionalClass="small"/>
                            {options.addLabel}
                        </button>
                    </div>
                )}
            </div>
        </div>
    )
}

const FieldLine: FunctionComponent<{
    line: RepeaterLine
    index: number
    onRemove: null | ((line: RepeaterLine) => void)
    onUpdate: (v: unknown, path: string) => void
    options: RepeaterFieldArgs
    defaultCollapsed: boolean
}> = ({line, index, onRemove, onUpdate, options, defaultCollapsed}) => {
    const [collapsed, toggleCollapsed] = useToggle(defaultCollapsed)

    const title = options.collapsed
        ? (line[options.collapsed] as string)
        : `#${index + 1}`
    const escapedTitle = useMemo(() => textContent(title), [title])

    return (
        <div className={'sidebar__sortable'} data-id={line._id} style={{position: 'relative'} as CSSProperties}>
            <div className="drag"></div>
            <div className="flex-group align-items-center justify-content-space-between padding-inline-4"
                 style={{width: "initial", flexWrap: 'initial'} as CSSProperties}>
                <h3 className="heading-3" style={{width: '100%', cursor: 'pointer'} as CSSProperties}
                    onClick={prevent(toggleCollapsed)}>
                    <strong>{escapedTitle}</strong>
                </h3>
                <div className="flex-group align-items-center" style={{flexWrap: 'initial'} as CSSProperties}>
                    <button
                        className="button padding-0 clr-red-400"
                        data-type="transparent"
                        onClick={() => onRemove ? onRemove(line) : null}
                        title={translate('core-cms.admin.editor.component.delete')}
                    >
                        <IconTrash/>
                    </button>
                    <button
                        className="button padding-1"
                        data-type="primary"
                        onClick={prevent(toggleCollapsed)}
                        style={{rotate: `${collapsed ? -90 : 0}deg`, borderRadius: '50%'} as CSSProperties}
                    >
                        <IconDown/>
                    </button>
                </div>
            </div>
            {!collapsed && (
                <div className={'grid margin-block-4'}>
                    <FieldsRenderer
                        fields={options.fields}
                        data={line}
                        onUpdate={onUpdate}
                        path={index.toString()}
                    />
                </div>
            )}
        </div>
    )
}

export const Repeater = defineField<RepeaterFieldArgs, FieldValue>(() => ({
    defaultOptions: {addLabel: translate('core-cms.admin.add'), fields: [], default: []},
    render: Component,
}))

