import {defineI18n} from "./shared/functions/i18n";
import './elements/index'
import {$} from "./shared/functions/dom";
import {InputChoicesElement, SelectChoicesElement} from "./elements/Choices";
import {clearAllBodyScrollLocks} from "body-scroll-lock";
import Lightbox from "./elements/Lightbox";
import AutomaticGallery from "./elements/AutomaticGallery";

defineI18n();
document.addEventListener("DOMContentLoaded", () => {
    let csrfToken = (document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content) ?? "";

    const refreshCSRF = async (): Promise<void> => {
        try {
            const response = await fetch("/api/csrf");
            const data: { token: string } = await response.json();
            csrfToken = data.token;

            const meta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
            if (meta) {
                meta.content = csrfToken;

                window.dispatchEvent(new CustomEvent('csrf-ready', { detail: { token: csrfToken } }));
            }
        } catch (e) {
            console.warn("CSRF refresh failed", e);
        }
    };

    void refreshCSRF();

    const header = $('#header')
    if (header) {
        const burgerButton = $('#js-burger')
        burgerButton?.addEventListener('click', () => {
            const lists = header.querySelectorAll('ul')
            const currentState = burgerButton.getAttribute('data-state')

            if (!currentState || currentState === 'closed') {
                header.setAttribute('data-state', 'opened')
                header.setAttribute('aria-expanded', 'true')
                burgerButton.setAttribute('data-state', 'opened')
                burgerButton.setAttribute('aria-expanded', 'true')
                lists.forEach(ul => {
                    ul.setAttribute('data-state', 'opened')
                    ul.setAttribute('aria-expanded', 'true')
                })
            } else {
                header.setAttribute('data-state', 'closed')
                header.setAttribute('aria-expanded', 'false')
                burgerButton.setAttribute('data-state', 'closed')
                burgerButton.setAttribute('aria-expanded', 'false')
                lists.forEach(ul => {
                    ul.setAttribute('data-state', 'closed')
                    ul.setAttribute('aria-expanded', 'false')
                })
            }
        })
    }
});

window.addEventListener('csrf-ready', function(event) {
    fetch("/api/flash-messages", {
        method: "GET",
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': event.detail.token,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async response => {
            if (response.status === 204) {
                return [] as FlashMessage[];
            }
            return (await response.json()) as FlashMessage[];
        })
        .then(messages => {
            const main = document.querySelector<HTMLElement>("main");

            if (!main) return;

            messages.forEach(msg => {
                const alertElement = document.createElement("alert-message");
                alertElement.setAttribute("type", msg.type);
                alertElement.setAttribute("is-floating", "true");

                if (msg.duration) {
                    alertElement.setAttribute("duration", String(msg.duration));
                }

                alertElement.textContent = msg.message;
                main.insertBefore(alertElement, main.firstChild);
            });
        })
        .catch((error: unknown) => {
            console.warn("Failed to load flash messages", error);
        });
});
interface FlashMessage {
    type: string;
    message: string;
    duration?: number;
}

Lightbox.defineElement();
AutomaticGallery.defineElement();

customElements.define('input-choices', InputChoicesElement, {
    extends: 'input',
});

customElements.define('select-choices', SelectChoicesElement, {
    extends: 'select',
});

clearAllBodyScrollLocks();