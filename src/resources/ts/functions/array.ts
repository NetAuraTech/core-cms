export function moveItem<T extends Array<unknown>>(
    items: T,
    from: number,
    to: number
): T {
    const newItems = [...items] as T;

    const [movedItem] = newItems.splice(from, 1);

    newItems.splice(to, 0, movedItem);

    return newItems;
}

export function insertItem<T extends Array<any>>(
    items: T,
    index: number,
    value: any
) {
    const clone = [...items];
    clone.splice(index, 0, value)
    return clone
}