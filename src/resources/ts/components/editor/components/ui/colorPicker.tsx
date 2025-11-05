import { h } from "preact";
import { useState } from "preact/hooks";
import { CSSProperties } from "preact/compat";
import { Popover } from './Popover';
import {prevent} from "../../../../shared/functions/functions";
import {colorToProperty} from "../../../../functions/css";
import {translate} from "../../../../shared/functions/i18n";

interface ColorPickerProps {
    value: string | null | undefined;
    options: { colors: string[] };
    onChange: (color: string | null) => void;
}

export const ColorPicker = ({value, options, onChange}: ColorPickerProps) => {
    const [isOpen, setOpen] = useState(false);
    const changeHandler = (color: string) =>
        prevent(() => {
            onChange(color);
            setOpen(false);
        });

    return (
        <Popover.Root open={isOpen} onOpenChange={setOpen}>
            <Popover.Trigger asChild>
                <button
                    className={`button ${(value === undefined || value === null) ? 'transparent' : ''}`}
                    title={translate('core-cms.admin.editor.sidebar.field.htmltext.color')}
                    style={
                        value
                            ? ({
                                '--selected-color': colorToProperty(value),
                            } as CSSProperties)
                            : undefined
                    }
                />
            </Popover.Trigger>
            <Popover.Content className={'tooltip'} side='bottom'>
                <div
                    className={'palette'}
                    style={
                        {'--children': options.colors.length + 1} as CSSProperties
                    }
                >
                    {options.colors.map(color => (
                        <button
                            className={`item ${value === color ? 'selected' : ''}`}
                            title={color}
                            key={color}
                            style={
                                {
                                    '--color-button': colorToProperty(color),
                                } as CSSProperties
                            }
                            onClick={changeHandler(color)}
                        />
                    ))}
                    <button
                        className={`item ${value === 'var(--neutral-000)' ? 'selected' : ''}`}
                        title={'var(--neutral-000)'}
                        key={'var(--neutral-000)'}
                        style={
                            {
                                '--color-button': colorToProperty('var(--neutral-000)'),
                            } as CSSProperties
                        }
                        onClick={changeHandler('var(--neutral-000)')}
                    />
                    <button
                        className={'item-transparent'}
                        onClick={prevent(() => onChange(null))}
                    />
                </div>
            </Popover.Content>
        </Popover.Root>
    );
};
