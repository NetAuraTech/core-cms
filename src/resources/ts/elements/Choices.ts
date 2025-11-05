import TomSelect from 'tom-select'
import {defineI18n, translate} from "../shared/functions/i18n";
import {jsonFetch} from "../shared/functions/api";

export class InputChoicesElement extends HTMLInputElement {
  widget: any = undefined

  connectedCallback() {}

  disconnectedCallback() {}
}

export class SelectChoicesElement extends HTMLSelectElement {
  widget: any = undefined

  connectedCallback() {}

  disconnectedCallback() {}
}

function bindBehaviour(
  cls: typeof InputChoicesElement | typeof SelectChoicesElement,
) {
  cls.prototype.connectedCallback = function () {
    defineI18n();

    if (this.getAttribute('choicesBinded')) {
      return
    }
    this.setAttribute('choicesBinded', 'true')

    type OptionsType = {
      hideSelected?: boolean
      ideSelected?: boolean
      persist?: boolean
      plugins: {
        no_backspace_delete?: {}
        dropdown_input?: {}
        remove_button?: {}
      }
      closeAfterSelect: boolean
      allowEmptyOption?: boolean
      valueField?: string
      labelField?: string
      searchField?: string
      load?: (query: string, callback: (data: any) => void) => void
      create?: boolean
    }
    const options: OptionsType = {
      hideSelected: true,
      persist: false,
      plugins: {
        no_backspace_delete: {},
      },
      closeAfterSelect: true,
    }
    if (this.tagName === 'SELECT') {
      options.allowEmptyOption = true
      options.plugins.no_backspace_delete = {}
      options.plugins.dropdown_input = {}
      if (this.getAttribute('multiple')) {
        options.plugins.remove_button = {
          title: translate('core-cms.core.choice.delete'),
        }
      }
    } else {
      options.plugins.remove_button = {
        title: translate('core-cms.core.choice.delete'),
      }
    }

    // Configure the options according to the situation
    if (this.dataset.remote) {
      options.valueField = this.dataset.value
      options.labelField = this.dataset.label
      options.searchField = this.dataset.label
      options.load = async (query, callback) => {
        const url = `${this.dataset.remote}?q=${encodeURIComponent(query)}`
        const data = await jsonFetch(url)
        callback(data)
      }
    }
    if (this.dataset.create) {
      options.create = true
    }
    // @ts-ignore
    this.widget = new TomSelect(this, options)

    // If the ‘redirect’ option is present, we redirect at the change of value
    if (this.dataset.redirect !== undefined) {
      this.widget?.on('change', () => redirectOnChange(this))
    }
  }

  cls.prototype.disconnectedCallback = function () {
    if (this.widget) {
      this.widget.destroy()
    }
  }
}

function redirectOnChange(select: any) {
  const params = new URLSearchParams(window.location.search)
  if (select.value === '') {
    params.delete(select.name)
  } else {
    params.set(select.name, select.value)
  }
  if (params.has('page')) {
    params.delete('page')
  }
}

Array.from([InputChoicesElement, SelectChoicesElement]).forEach(bindBehaviour)
