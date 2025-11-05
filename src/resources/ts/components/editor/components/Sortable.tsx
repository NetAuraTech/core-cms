import {ComponentChildren, FunctionComponent, h} from "preact";
import { useEffect, useRef, useState } from "preact/hooks";
// @ts-ignore
import Sortable from 'sortablejs';

interface SortableWrapperProps {
    items: any[];
    onMove: (oldIndex: number, newIndex: number) => void;
    children: ComponentChildren;
    className?: string;
    group?: string;
}

export const SortableWrapper: FunctionComponent<SortableWrapperProps> = ({
                                                                    items,
                                                                    onMove,
                                                                    children,
                                                                    className,
                                                                    group = 'default-sortable-group'
                                                                }) => {
    const containerRef = useRef<HTMLDivElement>(null);
    const sortableInstance = useRef<Sortable | null>(null);

    useEffect(() => {
        if (containerRef.current) {
            sortableInstance.current = new Sortable(containerRef.current, {
                animation: 150,
                group: group,
                // @ts-ignore
                onEnd: (evt) => {
                    if (evt.oldIndex !== undefined && evt.newIndex !== undefined) {
                        onMove(evt.oldIndex, evt.newIndex);
                    }
                },
                handle: '.drag',
            });
        }

        return () => {
            if (sortableInstance.current) {
                sortableInstance.current.destroy();
                sortableInstance.current = null;
            }
        };
    }, [onMove, group]);

    return (
        <div ref={containerRef} className={className}>
            {children}
        </div>
    );
};
