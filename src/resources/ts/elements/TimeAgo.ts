import {defineI18n, translate} from "../shared/functions/i18n";


export default class TimeAgo {
    /**
     * Defines the custom element
     * @param name
     */
    static defineElement(name: string = 'time-ago') {
        defineI18n();
        class TimeAgoElement extends HTMLElement {
            timer: number

            terms = [
                {
                    time: 45,
                    divide: 60,
                    text: translate('core-cms.core.time-ago.minute.less'),
                },
                {
                    time: 90,
                    divide: 60,
                    text: translate('core-cms.core.time-ago.minute.about'),
                },
                {
                    time: 45 * 60,
                    divide: 60,
                    text: '%d ' + translate('core-cms.core.time-ago.minute.minutes'),
                },
                {
                    time: 90 * 60,
                    divide: 60 * 60,
                    text: translate('core-cms.core.time-ago.hour.about'),
                },
                {
                    time: 24 * 60 * 60,
                    divide: 60 * 60,
                    text: '%d ' + translate('core-cms.core.time-ago.hour.hours'),
                },
                {
                    time: 42 * 60 * 60,
                    divide: 24 * 60 * 60,
                    text: translate('core-cms.core.time-ago.day.about'),
                },
                {
                    time: 30 * 24 * 60 * 60,
                    divide: 24 * 60 * 60,
                    text: '%d ' +  translate('core-cms.core.time-ago.day.days'),
                },
                {
                    time: 45 * 24 * 60 * 60,
                    divide: 24 * 60 * 60 * 30,
                    text: translate('core-cms.core.time-ago.month.about'),
                },
                {
                    time: 365 * 24 * 60 * 60,
                    divide: 24 * 60 * 60 * 30,
                    text: '%d ' + translate('core-cms.core.time-ago.month.months'),
                },
                {
                    time: 365 * 1.5 * 24 * 60 * 60,
                    divide: 24 * 60 * 60 * 365,
                    text: translate('core-cms.core.time-ago.year.about'),
                },
                {
                    time: Infinity,
                    divide: 24 * 60 * 60 * 365,
                    text: '%d ' + translate('core-cms.core.time-ago.year.years'),
                },
            ]
            constructor() {
                super()
                this.timer = 0
            }

            connectedCallback() {
                const timestamp = parseInt(this.getAttribute('time') || '0', 10) * 1000
                const date = new Date(timestamp)
                this.updateText(date)
                this.classList.add('capitalize-first-letter');
            }

            disconnectedCallback() {
                if (this.timer) window.clearTimeout(this.timer)
            }

            updateText(date: Date) {
                const seconds = (new Date().getTime() - date.getTime()) / 1000
                let term = null
                for (term of this.terms) {
                    if (Math.abs(seconds) < term.time) {
                        break
                    }
                }
                if (term) {
                    if (seconds >= 0) {
                        this.innerHTML = translate('core-cms.core.time-ago.ago', {
                            time: `${term.text.replace(
                                '%d',
                                Math.round(seconds / term.divide).toString(),
                            )}`
                        })
                    } else {
                        this.innerHTML = translate('core-cms.core.time-ago.in', {
                            time: `${term.text.replace(
                                '%d',
                                Math.round(Math.abs(seconds) / term.divide).toString(),
                            )}`
                        })
                    }
                    let nextTick = Math.abs(seconds) % term.divide
                    if (nextTick === 0) {
                        nextTick = term.divide
                    }
                    if (nextTick > 2147482) {
                        return
                    }
                    this.timer = window.setTimeout(() => {
                        window.requestAnimationFrame(() => {
                            this.updateText(date)
                        })
                    }, 1000 * nextTick)
                }
            }
        }

        customElements.define(name, TimeAgoElement)
    }
}
