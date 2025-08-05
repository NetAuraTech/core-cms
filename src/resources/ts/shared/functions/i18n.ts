import getLodash from "lodash/get";
import eachRightLodash from "lodash/eachRight";
import replaceLodash from "lodash/replace";

type NestedObject = { [key: string]: any };

function nestKeys(obj: Record<string, any>): NestedObject {
    const result: NestedObject = {};

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
    return Object.fromEntries(
        Object.entries(input).map(([key, value]) => [key, nestKeys(value)])
    );
}

export function defineI18n() {
    if (window.i18n) {
        window.i18n = transformInput(window.i18n);
    }

    window.translate = function (key: string, args?: Record<string, string>): string {
        let value: string = getLodash(window.i18n, key, key);

        if (args) {
            eachRightLodash(args, (paramVal, paramKey) => {
                value = replaceLodash(value, `:${paramKey}`, paramVal);
            });
        }

        return value;
    };
}

export function translate(key: string, args?: Record<string, string>): string {
    return window.translate(key, args);
}
