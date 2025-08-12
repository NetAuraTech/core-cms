//@ts-ignore
import getLodash from "lodash/get";
//@ts-ignore
import eachRightLodash from "lodash/eachRight";
//@ts-ignore
import replaceLodash from "lodash/replace";

type NestedObject = { [key: string]: any };

function nestKeys(obj: Record<string, any>): NestedObject {
    const result: NestedObject = {};

    //@ts-ignore
    Object.entries(obj).forEach(([key, value]) => {
        const keys = key.split('.');
        let current: NestedObject = result;

        while (keys.length > 1) {
            let subKey = keys.shift() as string;

            if (typeof current[subKey] === 'string') {
                current[subKey] = { "value": current[subKey] };
            }

            if (!current[subKey]) {
                current[subKey] = {};
            }

            current = current[subKey];
        }

        const lastKey = keys[0] as string;
        if (typeof current[lastKey] === 'string') {
            current[lastKey] = { "value": current[lastKey] };
        } else {
            current[lastKey] = value;
        }
    });

    return result;
}

function transformInput(input: Record<string, any>): Record<string, NestedObject> {
    //@ts-ignore
    return Object.fromEntries(
        //@ts-ignore
        Object.entries(input).map(([key, value]) => [key, nestKeys(value)])
    );
}

export function defineI18n() {
    //@ts-ignore
    if (window.i18n) {
        //@ts-ignore
        window.i18n = transformInput(window.i18n);
    }

    //@ts-ignore
    window.translate = function (key: string, args?: Record<string, string | number>, count?: number): string {
        //@ts-ignore
        let value: string = getLodash(window.i18n, key, key);

        // @ts-ignore
        if (typeof count === 'number' && value.includes('|')) {
            const rules = value.split('|');
            let matchedRule = null;

            for (const rule of rules) {

                const zeroMatch = rule.match(/^\{0\}(.*)$/);
                if (zeroMatch && count === 0) {
                    matchedRule = zeroMatch[1];
                    break;
                }

                const rangeMatch = rule.match(/^\[(\d+),(\d+|\*)\](.*)$/);
                if (rangeMatch) {
                    const min = parseInt(rangeMatch[1], 10);
                    const max = rangeMatch[2] === '*' ? Infinity : parseInt(rangeMatch[2], 10);
                    if (count >= min && count <= max) {
                        matchedRule = rangeMatch[3];
                        break;
                    }
                }
            }

            if (matchedRule !== null) {
                value = matchedRule;
            }
        }

        if (args) {
            eachRightLodash(args, (paramVal, paramKey) => {
                value = replaceLodash(value, `:${paramKey}`, String(paramVal));
            });
        }

        // @ts-ignore
        if (typeof count === 'number' && value.includes(':count')) {
            value = replaceLodash(value, ':count', String(count));
        }

        return value;
    };
}

export function translate(key: string, args?: Record<string, string>, count?: number): string {
    //@ts-ignore
    return window.translate(key, args, count);
}
