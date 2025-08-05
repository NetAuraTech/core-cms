import htm from "htm/mini";

/**
 * Finds the position of the element relative to the top of the page recursively
 */
export function offsetTop(element: any, parent: HTMLElement | null = null) {
    let top = element.offsetTop
    while ((element = element.offsetParent)) {
        if (parent === element) {
            return top
        }
        top += element.offsetTop
    }
    return top
}

type Attributes = Record<string, any>;

/**
 * Creates an HTML element
 * This function only covers the needs of the application, jsx-dom could replace this function
 * @return HTMLElement
 */
export function createElement(
    tagName: string | ((attributes: Attributes) => {}),
    attributes: Attributes = {},
    ...children: any[]
) {
    if (typeof tagName === 'function') {
        return tagName(attributes)
    }

    const svgTags = ['svg', 'use', 'path', 'circle', 'g']
    // We build the element
    const e = !svgTags.includes(tagName)
        ? document.createElement(tagName)
        : document.createElementNS('http://www.w3.org/2000/svg', tagName)

    // We associate it with the right attributes
    for (let k of Object.keys(attributes)) {
        if (typeof attributes[k] === "function" && k.startsWith("on")) {
            e.addEventListener(k.substring(2).toLowerCase(), attributes[k]);
        } else if (k === "xlink:href") {
            e.setAttributeNS("http://www.w3.org/1999/xlink", "href", attributes[k]);
        } else {
            e.setAttribute(k, attributes[k]);
        }
    }

    // Flattening the children
    children = children.reduce((acc, child) => {
        return Array.isArray(child) ? [...acc, ...child] : [...acc, child]
    }, [])

    for (const child of children) {
        if (typeof child === 'string') {
            e.appendChild(document.createTextNode(child))
        } else if (child instanceof HTMLElement || child instanceof SVGElement) {
            e.appendChild(child)
        } else {
            console.error("Unable to add element", child, typeof child)
        }
    }
    return e
}

/**
 * CreateElement version Tagged templates
 * @type {(strings: TemplateStringsArray, ...values: any[]) => (HTMLElement[] | HTMLElement)}
 */
export const html = htm.bind(createElement)

/**
 * Transform a string into a DOM element
 */
export function strToDom(str: string) {
    return document.createRange().createContextualFragment(str.trim())
        .firstChild as HTMLElement
}

/**
 * @return {null|HTMLElement}
 */
export function closest(element: any, selector: string) {
    for (; element && element !== document; element = element.parentNode) {
        if (element.matches(selector)) return element
    }
    return null
}

/**
 * @return {HTMLElement}
 */
export function $(selector: string) {
    return document.querySelector(selector)
}

/**
 * @return {HTMLElement[]}
 */
export function $$(selector: string) {
    return Array.from(document.querySelectorAll(selector))
}

/**
 * Generates a class from various variables
 */
export function classNames(...classnames: string[]) {
    return classnames.filter(classname => classname !== null).join(' ')
}

/**
 * Converts form data into a JavaScript object
 * @return {{[p: string]: string}}
 */
export function formDataToObj(form: HTMLFormElement) {
    return Object.fromEntries(new FormData(form))
}
