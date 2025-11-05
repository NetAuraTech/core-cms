import {h, ComponentChildren, JSX} from "preact";
import {CSSProperties, FormEvent, ReactNode, useEffect, useRef} from "preact/compat";
import Icon from "../../../../shared/components/Icon";


type Option = {
    value: string
    label: string
}

type FieldProps = {
    id?: string,
    value?: string,
    step?: number,
    min?: number,
    max?: number,
    type?: 'input' | 'text' | 'textarea' | 'select' | 'media' | 'datepicker' | 'number' | 'range' | 'color',
    label?: ComponentChildren
    help?: ReactNode
    options?: Option[],
    onInput: (e: FormEvent | Event) => void
} & JSX.IntrinsicElements['input'] &
    JSX.IntrinsicElements['textarea'] &
    JSX.IntrinsicElements['select']

export function Field({
                          id,
                          value,
                          label,
                          step,
                          min,
                          max,
                          help,
                          type = 'text',
                          options,
                          onInput
                      }: FieldProps) {
    let render: JSX.Element | null = null
    const ref = useRef<HTMLInputElement | null>(null);
    let canClear = false;

    if (type === 'text') {
        render = <input
            className="form-control"
            name={id}
            id={id}
            type="text"
            onInput={onInput}
            value={value}
        />
    }

    if (type === 'number') {
        render = <input
            className="form-control"
            name={id}
            id={id}
            type="number"
            onInput={onInput}
            defaultValue={value}
            step={step}
            min={min}
            max={max}
        />
    }

    if (type === 'range') {
        render = <input
            className="form-control"
            name={id}
            id={id}
            type="range"
            onInput={onInput}
            defaultValue={value}
            step={step}
            min={min}
            max={max}
        />
    }

    if (type === 'color') {
        canClear = true;
        render = <input
            ref={ref}
            className="form-control"
            name={id}
            id={id}
            type="color"
            onInput={onInput}
            defaultValue={value}
        />
    }

    if (type === 'textarea') {
        render = <textarea
            className="form-control"
            style={{resize: "vertical"} as CSSProperties}
            name={id}
            id={id}
            onInput={onInput}
            defaultValue={value}
        />
    }

    if (type === 'select') {
        render = <select
            className="form-control"
            name={id}
            id={id}
            onInput={onInput}
            defaultValue={value}
        >
            {options && options.map((option, k) => <option key={k}
                                                           value={option.value}>{option.label}</option>)}
        </select>
    }

    if (type === 'media') {
        canClear = true;
        render = <input
            ref={ref}
            className="form-control"
            name={id}
            id={id}
            type="text"
            defaultValue={value}
            is="input-media"
            data-endpoint="/api/media"
            // @ts-ignore
            overwrite="overwrite"
            style={{display: "none"} as CSSProperties}
        />;
    }

    if (type === 'datepicker') {
        render = <input
            className="form-control flatpickr-input"
            name={id}
            id={id}
            type="hidden"
            onInput={onInput}
            is="date-picker"
            defaultValue={value}
        />
    }

    useEffect(() => {
        if (type === 'media') {
            if (ref.current instanceof HTMLInputElement) {
                //@ts-ignore
                ref.current.addEventListener('media', e => {
                    onInput(e)
                })
            }
        }
    }, [render])

    const handleClear = () => {
        if (type === 'color') {
            if (ref.current instanceof HTMLInputElement) {
                ref.current.value = '#000000';
                const changeEvent = new Event("input", {bubbles: true});
                Object.defineProperty(changeEvent, "target", {
                    writable: false,
                    value: {value: "transparent"}
                });

                ref.current.dispatchEvent(changeEvent);
                onInput(changeEvent);
            }
        }

        if (type === 'media') {
            if (ref.current) {
                ref.current.value = "";
                const changeEvent = new Event("input", {bubbles: true});
                Object.defineProperty(changeEvent, "target", {
                    writable: false,
                    value: {value: null}
                });

                //@ts-ignore
                ref.current.setAttachment({id: '', url: null})

                ref.current.dispatchEvent(changeEvent);
                onInput(changeEvent);
            }
        }
    }


    return <div className={'form-group'}>
        <label htmlFor={id}>
            {type === 'range' ? `${label} (${value})` : label}
            {canClear && <button
                className="button padding-0 margin-inline-start-2"
                data-type="transparent"
                type="button"
                onClick={handleClear}
            >
                <span className="clr-red-400">
                    <Icon name="cross" additionalClass="small"/>
                </span>
            </button>}
        </label>
        {render}
        {help && <div
            className={'clr-neutral-600'}
            style={{fontSize: '.85rem'} as CSSProperties}
        >{help}</div>}
    </div>
}
