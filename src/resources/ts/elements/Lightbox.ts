export default class Lightbox {
    /**
     * Defines the custom element
     * @param name
     */
    static defineElement(name: string = 'light-box') {
        class LightboxElement extends HTMLElement {
            #element!: HTMLDivElement;
            #next!: HTMLButtonElement;
            #prev!: HTMLButtonElement;
            #close!: HTMLButtonElement;
            #container!: HTMLDivElement;
            #images: string[] = [];
            #url: string = "";
            #canChange: boolean = false;

            constructor() {
                super();
            }

            connectedCallback() {
                this.init();
            }

            init() {
                const links = Array.from(this.querySelectorAll('a'));
                this.#images = links.map(link => link.getAttribute('href') ?? '');

                links.forEach(link => link.addEventListener('click', e => {
                    e.preventDefault();
                    const href = (e.currentTarget as HTMLLinkElement).getAttribute('href');
                    if (href) {
                        this.lightbox(href);
                    }
                }));
            }

            lightbox(url: string) {
                this.enableScrollLock();
                this.#element = this.buildDOM(url);
                this.loadImage(url);
                this.appendChild(this.#element);
                this.onKeyUp = this.onKeyUp.bind(this);
                document.addEventListener('keyup', this.onKeyUp);
            }

            buildDOM(url: string): HTMLDivElement {
                const dom = document.createElement('div');
                dom.classList.add('lightbox');

                this.#next = document.createElement('button');
                this.#next.classList.add('next');
                this.#next.innerHTML = '<svg width="16px" height="28px"><use xlink:href="/vendor/core-cms/sprite.svg#arrow"/></svg>';
                this.#next.addEventListener('click', this.next.bind(this));
                dom.appendChild(this.#next);

                this.#prev = document.createElement('button');
                this.#prev.classList.add('prev');
                this.#prev.innerHTML = '<svg width="16px" height="28px"><use xlink:href="/vendor/core-cms/sprite.svg#arrow"/></svg>';
                this.#prev.addEventListener('click', this.prev.bind(this));
                dom.appendChild(this.#prev);

                this.#close = document.createElement('button');
                this.#close.classList.add('close');
                this.#close.innerHTML = '<svg width="100%" height="100%"><use xlink:href="/vendor/core-cms/sprite.svg#cross"/></svg>';
                this.#close.addEventListener('click', this.close.bind(this));
                dom.appendChild(this.#close);

                this.#container = document.createElement('div');
                this.#container.classList.add('lightbox__container');
                dom.appendChild(this.#container);

                return dom;
            }

            loadImage(url: string) {
                this.#canChange = false;
                this.#url = "";
                const image = new Image();

                this.#container.innerHTML = '';

                const loader = document.createElement('div');
                loader.classList.add('lightbox__loader');
                this.#container.appendChild(loader);

                image.onload = () => {
                    this.#container.removeChild(loader);
                    this.#container.appendChild(image);
                    this.#url = url;
                    this.#canChange = true;
                };

                image.setAttribute('src', url);
            }

            onKeyUp(e: KeyboardEvent) {
                if (e.key === 'Escape') {
                    this.close(e);
                } else if (e.key === 'ArrowLeft') {
                    this.prev(e);
                } else if (e.key === 'ArrowRight') {
                    this.next(e);
                }
            }

            reset() {
                this.#element = undefined as any;
                this.#next = undefined as any;
                this.#prev = undefined as any;
                this.#close = undefined as any;
                this.#container = undefined as any;
                this.#url = "";
            }

            close(e: MouseEvent | KeyboardEvent) {
                e.preventDefault();
                this.disableScrollLock();
                document.removeEventListener('keyup', this.onKeyUp);
                this.#element.classList.add('fade-out');

                setTimeout(() => {
                    this.removeChild(this.#element);
                    this.reset();
                }, 500);
            }

            next(e: MouseEvent | KeyboardEvent) {
                e.preventDefault();
                if (this.#canChange) {
                    let index = this.#images.findIndex(image => image === this.#url);
                    const nextImage = this.#images[index + 1];
                    if (nextImage) {
                        this.loadImage(nextImage);
                    }
                }
            }

            prev(e: MouseEvent | KeyboardEvent) {
                e.preventDefault();
                if (this.#canChange) {
                    let index = this.#images.findIndex(image => image === this.#url);
                    const prevImage = this.#images[index - 1];
                    if (prevImage) {
                        this.loadImage(prevImage);
                    }
                }
            }

            preventScroll(e: Event) {
                if (!document.querySelector(".lightbox")?.contains(e.target as Node)) {
                    e.preventDefault();
                }
            }

            enableScrollLock() {
                document.body.style.setProperty('overflow-y', 'hidden');
            }

            disableScrollLock() {
                document.body.style.setProperty('overflow-y', '');
            }
        }

        customElements.define(name, LightboxElement);
    }
}