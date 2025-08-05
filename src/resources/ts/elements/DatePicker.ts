import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import {translate} from "../shared/functions/i18n";

export default class DatePicker {
    /**
     * Defines the custom element
     * @param name
     */
    static defineElement(name: string = 'date-picker') {
        class DatePickerElement extends HTMLInputElement {
            flatpickr: any

            connectedCallback() {
                const hour = this.getAttribute('hour')
                const defaultHour = hour ? parseInt(hour, 10) : undefined
                this.flatpickr = flatpickr(this, {
                    locale: {
                        firstDayOfWeek: 1,
                        weekdays: {
                            shorthand: [
                                translate('core-cms.core.datepicker.locale.weekdays.shorthand.sun'),
                                translate('core-cms.core.datepicker.locale.weekdays.shorthand.mon'),
                                translate('core-cms.core.datepicker.locale.weekdays.shorthand.tue'),
                                translate('core-cms.core.datepicker.locale.weekdays.shorthand.wed'),
                                translate('core-cms.core.datepicker.locale.weekdays.shorthand.thu'),
                                translate('core-cms.core.datepicker.locale.weekdays.shorthand.fri'),
                                translate('core-cms.core.datepicker.locale.weekdays.shorthand.sat')
                            ],
                            longhand: [
                                translate('core-cms.core.datepicker.locale.weekdays.longhand.sunday'),
                                translate('core-cms.core.datepicker.locale.weekdays.longhand.monday'),
                                translate('core-cms.core.datepicker.locale.weekdays.longhand.tuesday'),
                                translate('core-cms.core.datepicker.locale.weekdays.longhand.wednesday'),
                                translate('core-cms.core.datepicker.locale.weekdays.longhand.thursday'),
                                translate('core-cms.core.datepicker.locale.weekdays.longhand.friday'),
                                translate('core-cms.core.datepicker.locale.weekdays.longhand.saturday')
                            ],
                        },
                        months: {
                            shorthand: [
                                translate('core-cms.core.datepicker.locale.months.shorthand.jan'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.feb'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.mar'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.apr'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.may'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.jun'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.jul'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.aug'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.sep'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.oct'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.nov'),
                                translate('core-cms.core.datepicker.locale.months.shorthand.dec'),
                            ],
                            longhand: [
                                translate('core-cms.core.datepicker.locale.months.longhand.january'),
                                translate('core-cms.core.datepicker.locale.months.longhand.february'),
                                translate('core-cms.core.datepicker.locale.months.longhand.march'),
                                translate('core-cms.core.datepicker.locale.months.longhand.april'),
                                translate('core-cms.core.datepicker.locale.months.longhand.may'),
                                translate('core-cms.core.datepicker.locale.months.longhand.june'),
                                translate('core-cms.core.datepicker.locale.months.longhand.july'),
                                translate('core-cms.core.datepicker.locale.months.longhand.august'),
                                translate('core-cms.core.datepicker.locale.months.longhand.september'),
                                translate('core-cms.core.datepicker.locale.months.longhand.october'),
                                translate('core-cms.core.datepicker.locale.months.longhand.november'),
                                translate('core-cms.core.datepicker.locale.months.longhand.december'),
                            ],
                        },
                        rangeSeparator: translate('core-cms.core.datepicker.locale.rangeSeparator'),
                        weekAbbreviation: translate('core-cms.core.datepicker.locale.weekAbbreviation'),
                        scrollTitle: translate('core-cms.core.datepicker.locale.scrollTitle'),
                        toggleTitle: translate('core-cms.core.datepicker.locale.toggleTitle'),
                        time_24hr: true,
                        ordinal(nth) {
                            if (nth > 1) {
                                return ''
                            }
                            return 'er'
                        },
                    },
                    altFormat: 'd F Y H:i',
                    dateFormat: 'Y-m-d H:i:s',
                    altInput: true,
                    enableTime: true,
                    defaultHour: defaultHour,
                    onClose: () => {
                        this.dispatchEvent(new Event('blur'))
                    },
                })
            }

            disconnectedCallback() {
                this.flatpickr.destroy()
            }
        }

        customElements.define(name, DatePickerElement, {extends: 'input'})
    }
}
