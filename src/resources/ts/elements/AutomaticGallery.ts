interface GalleryImage {
    width: number;
    element: HTMLElement;
}

export default class AutomaticGallery {
    /**
     * Defines the custom element
     * @param name
     */
    static defineElement(name: string = 'automatic-gallery') {
        class AutomaticGalleryElement extends HTMLElement {
            #gap = 0;
            #rowHeight = 0;
            #images: GalleryImage[] = [];
            #resizeListener!: () => void;

            get observedAttributes() {
                return ["rowHeight"];
            }

            connectedCallback() {
                this.style.setProperty('display', 'flex');
                this.style.setProperty('flex-direction', 'column');
                this.style.setProperty('--gap', this.getAttribute('gap'));
                this.style.setProperty('gap', 'var(--gap, .5rem)');

                this.#gap = parseFloat(getComputedStyle(this).rowGap);
                this.#rowHeight = parseFloat(this.getAttribute('rowHeight') ?? '0');

                this.#images = Array.from(this.children)
                    .map((el, idx) => this.getImageDimension(el as HTMLElement, idx))
                    .filter((v): v is GalleryImage => Boolean(v)); // Type guard

                this.buildLines();

                let timer: number | null = null;
                this.#resizeListener = () => {
                    if (timer !== null) clearTimeout(timer);
                    timer = window.setTimeout(() => {
                        withTransition(() => this.buildLines());
                    }, 500);
                };
                window.addEventListener('resize', this.#resizeListener);
            }

            disconnectedCallback() {
                window.removeEventListener('resize', this.#resizeListener);
            }

            buildLines() {
                this.innerHTML = '';
                const containerWidth = this.getBoundingClientRect().width;

                let index = 0;
                let rowWidth = -this.#gap;
                let rowStartAt = 0;

                while (index < this.#images.length) {
                    const image = this.#images[index];
                    if (!image) continue; // Vérification pour éviter undefined

                    const newRowWidth = rowWidth + image.width + this.#gap;

                    if (rowWidth < containerWidth) {
                        index++;
                        rowWidth = newRowWidth;
                        continue;
                    }

                    if (rowWidth > 0 && newRowWidth - containerWidth > containerWidth - rowWidth) {
                        index--;
                    }

                    const rowImages = this.#images.slice(rowStartAt, index + 1);
                    this.buildLine(rowImages);

                    rowStartAt = index + 1;
                    rowWidth = -this.#gap;
                    index++;
                }

                if (rowStartAt < this.#images.length) {
                    const rowImages = this.#images.slice(rowStartAt);
                    this.buildLine(rowImages, 0);
                }
            }

            buildLine(images: GalleryImage[], space: number = 0) {
                const div = document.createElement('div');
                div.style.setProperty('display', 'grid');
                div.style.setProperty('gap', 'var(--gap, .5rem)');
                div.style.setProperty(
                    'grid-template-columns',
                    images.map((image) => `${image.width}fr`).join(' ') + (space === 0 ? '' : ` ${space}fr`)
                );
                for (const image of images) {
                    div.appendChild(image.element);
                }
                this.appendChild(div);
            }

            getImageDimension(element: HTMLElement, k: number): GalleryImage | null {
                element.style.setProperty('view-transition-name', `image-${k}`);
                const img = element.tagName === 'IMG' ? element : element.querySelector('img');

                if (!img) return null;

                const width = parseFloat(img.getAttribute('width') ?? '0');
                const height = parseFloat(img.getAttribute('height') ?? '1'); // Éviter division par 0
                const ratio = width / height;

                return {
                    width: ratio * this.#rowHeight,
                    element: element
                };
            }
        }

        customElements.define(name, AutomaticGalleryElement);
    }
}

const withTransition = (cb: () => void) => {
    if (!document.startViewTransition) {
        cb();
        return;
    }
    document.startViewTransition(cb);
};