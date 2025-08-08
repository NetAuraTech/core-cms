import { clamp, random } from "../shared/functions/number";
import { jsonFetch } from "../shared/functions/api";

export default class PuzzleCaptcha {
    /**
     * Defines the custom element
     * @param name
     */
    static defineElement(name: string = 'puzzle-captcha') {
        class PuzzleCaptchaElement extends HTMLElement {

            connectedCallback() {
                const width = parseInt(this.getAttribute('width') ?? '300', 10);
                const height = parseInt(this.getAttribute('height') ?? '150', 10);
                const pieceWidth = parseInt(this.getAttribute('piece-width') ?? '50', 10);
                const pieceHeight = parseInt(this.getAttribute('piece-height') ?? '50', 10);

                const form = this.closest('form');
                const button = form?.querySelector('button');
                if (button) button.setAttribute('disabled', 'disabled');

                const container = document.createElement('div');
                container.classList.add('captcha');
                container.style.setProperty('--width', `${width}px`);
                container.style.setProperty('--height', `${height}px`);

                const background = document.createElement('div');
                background.classList.add('captcha__background');
                background.style.setProperty('--image', `url(${this.getAttribute('src') ?? ''})`);

                const piece = document.createElement('div');
                piece.classList.add('captcha__piece');
                piece.style.setProperty('--piece-width', `${pieceWidth}px`);
                piece.style.setProperty('--piece-height', `${pieceHeight}px`);

                background.appendChild(piece);
                container.appendChild(background);
                this.prepend(container);

                let isDragging = false;
                let position = {
                    x: random(0, width - pieceWidth),
                    y: random(0, height - pieceHeight)
                };

                const challengeInput = this.querySelector<HTMLInputElement>('#captcha-challenge');
                const answerInput = this.querySelector<HTMLInputElement>('#captcha-answer');
                if (!answerInput) return;

                answerInput.value = `${position.x}-${position.y}`;
                piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`);

                piece.addEventListener('pointerdown', () => {
                    isDragging = true;
                    document.body.style.setProperty('user-select', 'none');

                    window.addEventListener('pointerup', async () => {
                        isDragging = false;
                        document.body.style.removeProperty('user-select');

                        const loaderWrapper = document.createElement('div');
                        loaderWrapper.classList.add('loader__wrapper');
                        const loader = document.createElement('span');
                        loader.classList.add('loader');
                        loaderWrapper.appendChild(loader);
                        container.appendChild(loaderWrapper);

                        try {
                            await jsonFetch('/captcha/check', {
                                method: 'POST',
                                body: {
                                    challenge: challengeInput?.value ?? '',
                                    answer: `${position.x}-${position.y}`,
                                }
                            });
                            container.classList.add('captcha__success');
                            button?.removeAttribute('disabled');
                        } catch {
                            container.classList.add('captcha__error');
                            setTimeout(() => container.classList.remove('captcha__error'), 500);
                        }
                        container.removeChild(loaderWrapper);

                    }, { once: true });
                });

                this.addEventListener('pointermove', e => {
                    if (!isDragging) return;

                    position.x = clamp(position.x + e.movementX, 0, width - pieceWidth);
                    position.y = clamp(position.y + e.movementY, 0, height - pieceHeight);
                    answerInput.value = `${position.x}-${position.y}`;
                    piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`);
                });
            }

            disconnectedCallback() {}
        }

        customElements.define(name, PuzzleCaptchaElement);
    }
}
