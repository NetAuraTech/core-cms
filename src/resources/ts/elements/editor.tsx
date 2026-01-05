import {
    EditorComponentData,
    EditorComponentDefinition,
    EditorComponentDefinitions,
    EditorComponentTemplate, FieldDefinition
} from "../components/editor/types";
import {indexify} from "../functions/object";
import {fillDefaults} from "../functions/fields";
import {createRoot} from "preact/compat/client";
import {InsertPosition} from "../components/editor/enum";
import {EditorManager} from "../components/editor/editorManager";
import {StoreProvider} from "../components/editor/store";
import {Text, TextFieldArgs} from "../components/editor/components/fields/text";
import {Select, SelectFieldArgs, SelectOption} from "../components/editor/components/fields/select";
import {Color, ColorFieldArgs} from "../components/editor/components/fields/color";
import {Number, NumberFieldArgs} from "../components/editor/components/fields/number";
import {Tabs, TabDefinition} from "../components/editor/components/fields/layouts/tabs";
import {Row} from "../components/editor/components/fields/layouts/rows";
import {Range, RangeFieldArgs} from "../components/editor/components/fields/range";
import {Repeater, RepeaterFieldArgs} from "../components/editor/components/fields/repeater";
import {HtmlText, HtmlTextFieldArgs} from "../components/editor/components/fields/htmlText";
import {Media, MediaFieldArgs} from "../components/editor/components/fields/media";
import {DatePicker} from "../components/editor/components/fields/datePicker";
import {Checkbox} from "../components/editor/components/fields/checkbox";
import {Dropdown} from "../components/editor/components/fields/layouts/dropdown";
import { translate } from "../shared/functions/i18n";
import {jsonFetchOrFlash} from "../shared/functions/api";

const components: EditorComponentDefinitions = {}
const templates: EditorComponentTemplate[] = []

export class Editor {
    jsonFetchOrFlash = jsonFetchOrFlash
    components: EditorComponentDefinition[] = []
    options: SelectOption[] = []

    /**
     * Defines the custom element
     * @param elementName
     */
    defineElement(elementName: string = 'editor-builder') {
        class EditorElement extends HTMLElement {
            static changeEventName = 'change'
            private _value = '';
            private _mounted: boolean = false
            private _data: EditorComponentData[] | null = null

            static get observedAttributes() {
                return ['hidden', 'value']
            }

            get value(): string {
                return this._value
            }

            set value(v: string) {
                if (v === this._value) {
                    return
                }
                this._value = v
                this._data = null
                this.renderComponent()
            }

            connectedCallback() {
                this._value = this.getAttribute('value') || '[]'
                this.renderComponent()
                this._mounted = true
            }

            disconnectedCallback() {
                this._mounted = false
            }

            attributeChangedCallback(
                name: string,
                oldValue?: string,
                newValue?: string,
            ) {
                if (!this._mounted) {
                    return false
                }

                if (name === 'value') {
                    if (newValue === this._value) {
                        return
                    }
                    this._value = newValue!
                }
                this.renderComponent()
            }

            private parseValue(value: string): EditorComponentData[] {
                if (this._data === null) {
                    try {
                        const json = JSON.parse(value)
                        this._data = indexify(json).map((value: EditorComponentData) => {
                            return fillDefaults(value, components[value._name]?.fields ?? [])
                        })
                    } catch (e) {
                        console.error(translate('core-cms.admin.editor.parse.error'), value, e)
                        alert(translate('core-cms.admin.editor.parse.error'))
                        this._data = []
                    }
                }
                return this._data!
            }

            private renderComponent() {
                const data = this.parseValue(this._value)
                const hiddenCategories =
                    this.getAttribute('hidden-categories')?.split(';') ?? []
                const inner = this.innerHTML;
                createRoot(this).render(
                    <StoreProvider
                        data={data}
                        definitions={components}
                        templates={templates}
                        hiddenCategories={hiddenCategories}
                        rootElement={this}
                        insertPosition={
                            (this.getAttribute('insertPosition') ??
                                InsertPosition.End) as InsertPosition
                        }
                    >
                        <EditorManager
                            element={this}
                            inner={inner}
                            value={data}
                            previewUrl={this.getAttribute('preview') ?? ''}
                            iconsUrl={this.getAttribute('iconsUrl') ?? '/'}
                            name={this.getAttribute('name') ?? ''}
                            visible={this.getAttribute('hidden') !== null}
                            onChange={(value: string) => {
                                if (this._value === value) {
                                    return
                                }
                                this._value = value
                                this.dispatchEvent(
                                    new CustomEvent('change', {
                                        detail: value,
                                    }),
                                )
                            }}
                        />
                    </StoreProvider>,
                )
            }
        }

        customElements.define(elementName, EditorElement);
    }

    setOptions(options: SelectOption[] = []) {
        this.options = options;
    }

    registerComponent(name: string, definition: EditorComponentDefinition) {
        components[name] = {label: definition.title, ...definition}
    }

    registerTemplate (template: EditorComponentTemplate) {
        templates.push(template)
    }

    titleField(name: string, label: string) {
        return this.layouts.Dropdown([
            this.fields.Text(name, {
                label: label,
                multiline: false,
                canAnimate: true,
                default: 'Lorem ipsum dolor sit amet'
            } as TextFieldArgs),
            this.fields.Select(`${name}-level`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.title.level'),
                options: [
                    {
                        label: 'H1',
                        value: 'h1'
                    },
                    {
                        label: 'H2',
                        value: 'h2'
                    },
                    {
                        label: 'H3',
                        value: 'h3'
                    },
                    {
                        label: 'H4',
                        value: 'h4'
                    },
                ],
                default: 'h2'
            } as SelectFieldArgs),
            this.fields.Color(`${name}-color`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.title.color'),
                colors: Object.values(this.colors()),
                default: 'transparent'
            } as ColorFieldArgs),
            this.fields.Select(`${name}-border-style`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.border.style.value'),
                options: [
                    {
                        label: '',
                        value: ''
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.style.solid'),
                        value: 'solid'
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.style.dotted'),
                        value: 'dotted'
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.style.dashed'),
                        value: 'dashed'
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.style.wavy'),
                        value: 'wavy'
                    },
                ],
                default: ''
            } as SelectFieldArgs),
            this.fields.Select(`${name}-border-line`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.border.line.value'),
                options: [
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.line.underline'),
                        value: 'underline'
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.line.overline'),
                        value: 'overline'
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.line.blink'),
                        value: 'blink'
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.line.line-through'),
                        value: 'line-through'
                    },
                ],
                default: 'underline'
            } as SelectFieldArgs).when(`${name}-border-style`),
            this.fields.Color(`${name}-border-color`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.border.color'),
                colors: Object.values(this.colors()),
                default: 'transparent'
            } as ColorFieldArgs).when(`${name}-border-style`),
        ], {
            collapsed: label
        });
    }

    mediaField(name: string) {
        return [
            this.layouts.Dropdown([
                this.fields.Media(name, {
                    label: translate('core-cms.admin.editor.sidebar.tabs.media.value'),
                    canAnimate: true,
                    default: {
                        id: "",
                        alt: "",
                        height: "",
                        opacity: "1"
                    },
                } as MediaFieldArgs),
            ] as Array<FieldDefinition>, {
                collapsed: translate('core-cms.admin.editor.sidebar.tabs.media.value')
            }),
        ]
    }

    animationField(key: string, label: string) {
        return this.layouts.Dropdown([
            this.fields.Select(`${key}_animation`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.value'),
                options: [
                    {
                        value: '',
                        label: 'Aucune animation'
                    },
                    // FADE BLUR
                    {
                        value: 'fade-blur',
                        label: 'Fade Blur'
                    },
                    {
                        value: 'fade-blur-left',
                        label: 'Fade Blur Left'
                    },
                    {
                        value: 'fade-blur-right',
                        label: 'Fade Blur Right'
                    },
                    {
                        value: 'fade-blur-top',
                        label: 'Fade Blur Top'
                    },
                    {
                        value: 'fade-blur-bottom',
                        label: 'Fade Blur Bottom'
                    },
                    // SLIDE
                    {
                        value: 'slide-left',
                        label: 'Slide Left'
                    },
                    {
                        value: 'slide-right',
                        label: 'Slide Right'
                    },
                    {
                        value: 'slide-top',
                        label: 'Slide Top'
                    },
                    {
                        value: 'slide-bottom',
                        label: 'Slide Bottom'
                    },
                    // ZOOM
                    {
                        value: 'zoom-in',
                        label: 'Zoom In'
                    },
                    {
                        value: 'zoom-out',
                        label: 'Zoom Out'
                    },
                    // ROTATE
                    {
                        value: 'rotate-left',
                        label: 'Rotate Left'
                    },
                    {
                        value: 'rotate-right',
                        label: 'Rotate Right'
                    },
                    // FLIP
                    {
                        value: 'flip-x',
                        label: 'Flip X'
                    },
                    {
                        value: 'flip-y',
                        label: 'Flip Y'
                    },
                    // SKEW
                    {
                        value: 'skew-left',
                        label: 'Skew Left'
                    },
                    {
                        value: 'skew-right',
                        label: 'Skew Right'
                    },
                    {
                        value: 'skew-top',
                        label: 'Skew Top'
                    },
                    {
                        value: 'skew-bottom',
                        label: 'Skew Bottom'
                    },
                    // REVEAL
                    {
                        value: 'reveal-left',
                        label: 'Reveal Left'
                    },
                    {
                        value: 'reveal-right',
                        label: 'Reveal Right'
                    },
                    {
                        value: 'reveal-top',
                        label: 'Reveal Top'
                    },
                    {
                        value: 'reveal-bottom',
                        label: 'Reveal Bottom'
                    },
                    {
                        value: 'reveal-center',
                        label: 'Reveal Center'
                    },
                    // BOUNCE
                    {
                        value: 'bounce-left',
                        label: 'Bounce Left'
                    },
                    {
                        value: 'bounce-right',
                        label: 'Bounce Right'
                    },
                    {
                        value: 'bounce-top',
                        label: 'Bounce Top'
                    },
                    {
                        value: 'bounce-bottom',
                        label: 'Bounce Bottom'
                    },
                    // SCALE
                    {
                        value: 'scale-top-left',
                        label: 'Scale Top Left'
                    },
                    {
                        value: 'scale-top-right',
                        label: 'Scale Top Right'
                    },
                    {
                        value: 'scale-bottom-left',
                        label: 'Scale Bottom Left'
                    },
                    {
                        value: 'scale-bottom-right',
                        label: 'Scale Bottom Right'
                    }
                ]
            } as SelectFieldArgs),
            this.fields.Number(`${key}_delay`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.animation.delay'),
                step: 0.1,
                min: 0,
                default: "0"
            } as NumberFieldArgs).when(`${key}_animation`),
            this.fields.Text(`${key}-transition-name`, {
                label: translate('core-cms.admin.editor.sidebar.tabs.animation.view-transition-name'),
                multiline: false,
                canAnimate: false
            } as TextFieldArgs).when(`${key}_animation`, ''),
        ], {
            label: label
        });
    }

    baseTabs(fields: Array<FieldDefinition<any, any>>, tabs = {content: true, animation: true, background: true, appearance: true}) {
        const animationFields = [this.animationField('general', translate('core-cms.admin.editor.sidebar.tabs.animation.general'))];


        fields.map(field => {
            if (!field.group && "name" in field) {
                //@ts-ignore
                if (field.options.canAnimate && field.shouldRender) {
                    const newField = this.animationField(field.name, field.options.label);
                    //@ts-ignore
                    newField.conditions = field.conditions;
                    animationFields.push(newField);
                }
            }

            if (field.group && "fields" in field) {
                field.fields.map(subField => {
                    if (!subField.group && "name" in subField) {
                        if (subField.options.canAnimate) {
                            //@ts-ignore
                            const newField = this.animationField(subField.name, subField.options.label);
                            //@ts-ignore
                            newField.conditions = field.conditions;
                            animationFields.push(newField);
                        }
                    }
                })
            }
        });

        const availableTabs = {
            content: {
                label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                fields: fields,
            } as TabDefinition,
            animation: {
                label: translate('core-cms.admin.editor.sidebar.tabs.animation.value'),
                fields: animationFields,
            } as TabDefinition,
            background: {
                label: translate('core-cms.admin.editor.sidebar.tabs.background.value'),
                fields: [
                  this.layouts.Row([
                      this.fields.Color('background-color', {
                          label: translate('core-cms.admin.editor.sidebar.tabs.background.color'),
                          colors: Object.values(this.colors()),
                          default: 'transparent'
                      } as ColorFieldArgs),
                      this.fields.Media('background-image', {
                          label: translate('core-cms.admin.editor.sidebar.tabs.background.image.value')
                      } as MediaFieldArgs),
                      this.fields.Number('background-image-opacity', {
                          label: translate('core-cms.admin.editor.sidebar.tabs.background.image.opacity'),
                          default: "1",
                          min: 0,
                          max: 1,
                          step: 0.01
                      } as NumberFieldArgs),
                  ] as Array<FieldDefinition<any, any>>),
                  this.layouts.Row([
                      this.fields.Select('background-image-size', {
                          label: translate('core-cms.admin.editor.sidebar.tabs.background.image.size.value'),
                          options: [
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.size.auto'),
                                  value: 'auto'
                              },
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.size.cover'),
                                  value: 'cover'
                              },
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.size.contain'),
                                  value: 'contain'
                              }
                          ],
                          default: 'auto'
                      } as SelectFieldArgs),
                      this.fields.Select('background-image-repeat', {
                          label: translate('core-cms.admin.editor.sidebar.tabs.background.image.repeat.value'),
                          options: [
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.repeat.no'),
                                  value: 'no-repeat'
                              },
                              {
                                  label: 'X',
                                  value: 'repeat-x'
                              },
                              {
                                  label: 'Y',
                                  value: 'repeat-y'
                              },
                              {
                                  label: 'X & Y',
                                  value: 'repeat'
                              }
                          ],
                          default: 'auto'
                      } as SelectFieldArgs),
                  ] as Array<FieldDefinition<any, any>>).when('background-image'),
                  this.layouts.Row([
                      this.fields.Select('background-image-position-x', {
                          label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.x'),
                          options: [
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.left'),
                                  value: 'left'
                              },
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.center'),
                                  value: 'center'
                              },
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.right'),
                                  value: 'right'
                              },
                          ],
                          default: 'center'
                      } as SelectFieldArgs),
                      this.fields.Select('background-image-position-y', {
                          label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.y'),
                          options: [
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.top'),
                                  value: 'top'
                              },
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.center'),
                                  value: 'center'
                              },
                              {
                                  label: translate('core-cms.admin.editor.sidebar.tabs.background.image.position.bottom'),
                                  value: 'bottom'
                              },
                          ],
                          default: 'center'
                      } as SelectFieldArgs),
                  ] as Array<FieldDefinition<any, any>>).when('background-image'),
              ],
            } as TabDefinition,
            appearance: {
                label: translate('core-cms.admin.editor.sidebar.tabs.appearance'),
                fields: [
                    this.layouts.Row([
                        this.fields.Checkbox('use-container', {
                            label: translate('core-cms.admin.editor.sidebar.tabs.appearance.container.use'),
                            default: true,
                        }),
                        this.fields.Text('additional-classes', {
                            label: translate('core-cms.admin.editor.sidebar.tabs.appearance.classes.additional'),
                            default: "",
                        }),
                        this.fields.Text('id', {
                            label: translate('core-cms.admin.editor.sidebar.tabs.appearance.id'),
                            default: "",
                        })
                    ] as Array<FieldDefinition<any, any>>, {
                    }),
                    this.layouts.Row([
                        this.fields.Range('padding-block', {
                            label: translate('core-cms.admin.editor.sidebar.tabs.padding.block'),
                            step: 1,
                            min: 0,
                            max: 20,
                            default: "6"
                        } as RangeFieldArgs),
                        this.fields.Range('padding-inline', {
                            label: translate('core-cms.admin.editor.sidebar.tabs.padding.inline'),
                            step: 1,
                            min: 0,
                            max: 20,
                            default: "0"
                        } as RangeFieldArgs),
                    ] as Array<FieldDefinition<any, any>>, {label: translate('core-cms.admin.editor.category.layout')}),
                    this.layouts.Row([
                        this.fields.Range('border-top-left-radius', {
                            min: 0,
                            max: 20,
                            default: "0",
                            label: translate('core-cms.admin.editor.sidebar.tabs.border.radius.topleft')
                        } as RangeFieldArgs),
                        this.fields.Range('border-top-right-radius', {
                            min: 0,
                            max: 20,
                            default: "0",
                            label: translate('core-cms.admin.editor.sidebar.tabs.border.radius.topright')
                        } as RangeFieldArgs),
                        this.fields.Range('border-bottom-left-radius', {
                            min: 0,
                            max: 20,
                            default: "0",
                            label: translate('core-cms.admin.editor.sidebar.tabs.border.radius.bottomleft')
                        } as RangeFieldArgs),
                        this.fields.Range('border-bottom-right-radius', {
                            min: 0,
                            max: 20,
                            default: "0",
                            label: translate('core-cms.admin.editor.sidebar.tabs.border.radius.bottomright')
                        } as RangeFieldArgs),
                    ] as Array<FieldDefinition<any, any>>, {
                        label: translate('core-cms.admin.editor.sidebar.tabs.border.radius.value')
                    })
                ],
            } as TabDefinition,
        };

        const activeTabs = Object.entries(tabs)
            .filter(([key, isActive]) => isActive)
            .map(([key]) => availableTabs[key]);

        return this.layouts.Tabs(...activeTabs);
    }

    links = (additionalFields: Array<FieldDefinition<any, any>> = []) => {
        const fields = [
            this.fields.Text('label', {
                label: translate('core-cms.admin.editor.sidebar.tabs.label.value'),
                help: translate('core-cms.admin.editor.sidebar.tabs.label.help'),
                multiline: false,
            } as TextFieldArgs),
            this.fields.Select('type', {
                label: translate('core-cms.admin.editor.sidebar.tabs.link.type.value'),
                default: 'internal',
                options: [
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.link.type.internal'),
                        value: 'internal',
                    },
                    {
                        label: translate('core-cms.admin.editor.sidebar.tabs.link.type.external'),
                        value: 'external',
                    },
                ],
            } as SelectFieldArgs),
            this.fields
                .Select('url', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.link.value'),
                    options: [
                        {
                            value: '',
                            label: '',
                        },
                        {
                            value: JSON.stringify({
                                path: 'home',
                                label: translate('core-cms.admin.editor.sidebar.tabs.link.home'),
                            }),
                            label: translate('core-cms.admin.editor.sidebar.tabs.link.home'),
                        },
                        {
                            value: JSON.stringify({
                                path: 'blog.index',
                                label: translate('core-cms.admin.editor.sidebar.tabs.link.blog'),
                            }),
                            label: translate('core-cms.admin.editor.sidebar.tabs.link.blog'),
                        },
                        {
                            value: JSON.stringify({
                                path: 'profile.index',
                                label: translate('core-cms.admin.editor.sidebar.tabs.link.profile'),
                            }),
                            label: translate('core-cms.admin.editor.sidebar.tabs.link.profile'),
                        },
                        {
                            value: JSON.stringify({
                                path: 'login',
                                label: translate('core-cms.admin.editor.sidebar.tabs.link.login'),
                            }),
                            label: translate('core-cms.admin.editor.sidebar.tabs.link.login'),
                        },
                        ...this.options,
                    ],
                })
                .when('type', 'internal'),
            this.fields
                .Text('url', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.link.url'),
                    multiline: false,
                })
                .when('type', 'external'),
            ...additionalFields
        ]

        return this.baseTabs(fields, {content: true, animation: true, appearance: false, background: false})
    }

    ctas() {
        const fields: Array<FieldDefinition<any, any>> = [
            this.fields.Select('cta_type', {
                label: translate('core-cms.admin.editor.sidebar.tabs.link.cta.type'),
                options: [
                    {
                        value: 'primary',
                        label: translate('core-cms.admin.editor.sidebar.tabs.link.cta.primary'),
                    },
                    {
                        value: 'accent',
                        label: translate('core-cms.admin.editor.sidebar.tabs.link.cta.accent'),
                    },
                    {
                        value: 'outline',
                        label: translate('core-cms.admin.editor.sidebar.tabs.link.cta.outline'),
                    },
                ],
            } as SelectFieldArgs)
        ];
        return this.links(fields)
    }

    generateConditionalFields(components: EditorComponentDefinition[]) {
        const fields: FieldDefinition[] = [];

        components.map(component =>
            component.fields.map(field => fields.push(field.when('item-type', component._id)))
        );

        return fields
    };

    generateTemplateItem(components: EditorComponentDefinition[], name: string) {
        return this.fields.Repeater(name, {
            collapsed: 'item-type',
            fields: [
                this.baseTabs([
                    this.fields.Select('item-type', {
                        label: translate('core-cms.admin.editor.sidebar.template.choose'),
                        options: [
                            {
                                value: '',
                                label: '',
                            },
                            ...components.map(component => {
                                return {
                                    value: component._id,
                                    label: component.title,
                                }
                            })
                        ],
                    } as SelectFieldArgs),
                    ...this.generateConditionalFields(components),
                ] as Array<FieldDefinition<any, any>>)
            ],
            addLabel: translate('core-cms.admin.add'),
            label: translate('core-cms.admin.editor.sidebar.item')
        } as RepeaterFieldArgs)
    }

    registerComponents(components: EditorComponentDefinition[]) {
        this.components = components;

        this.components.push({ _id: 'hero',
            title: translate('core-cms.admin.editor.sidebar.tabs.hero'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: true,
            fields: [
                this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                this.titleField('sub-title', translate('theme.admin.editor.sidebar.tabs.sub-title')),
                this.fields.HtmlText('content', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                    colors: Object.values(this.colors()),
                    multiline: true,
                    canAnimate: true
                } as HtmlTextFieldArgs),
                this.fields.Repeater('ctas', {
                    addLabel: translate('core-cms.admin.add'),
                    fields: [
                        this.ctas()
                    ],
                    label: translate('core-cms.admin.editor.sidebar.tabs.ctas')
                } as RepeaterFieldArgs),
            ]
        } as EditorComponentDefinition);

        this.components.push({
            _id: 'header',
            title: translate('core-cms.admin.editor.sidebar.tabs.header'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: false,
            fields: [
                this.fields.Repeater('links', {
                    addLabel: translate('core-cms.admin.add'),
                    fields: [
                        this.links()
                    ],
                    label: translate('core-cms.admin.editor.sidebar.tabs.links')
                } as RepeaterFieldArgs),
            ]
        } as EditorComponentDefinition);

        this.components.push({
            _id: 'card',
            title: translate('core-cms.admin.editor.sidebar.tabs.card'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: true,
            fields: [
                ...this.mediaField('media'),
                this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                this.fields.HtmlText('content', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                    multiline: true,
                    colors: Object.values(this.colors()),
                    canAnimate: true
                } as HtmlTextFieldArgs),
                this.fields.Repeater('ctas', {
                    addLabel: translate('core-cms.admin.add'),
                    fields: [
                        this.ctas()
                    ],
                    label: translate('core-cms.admin.editor.sidebar.tabs.ctas')
                } as RepeaterFieldArgs),
            ]
        } as EditorComponentDefinition);

        this.components.push({
            _id: 'section',
            title: translate('core-cms.admin.editor.sidebar.tabs.section'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: true,
            fields: [
                ...this.mediaField('media'),
                this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                this.fields.HtmlText('content', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                    colors: Object.values(this.colors()),
                    multiline: true,
                    canAnimate: true
                } as HtmlTextFieldArgs),
                this.fields.Repeater('ctas', {
                    addLabel: translate('core-cms.admin.add'),
                    fields: [
                        this.ctas()
                    ],
                    label: translate('core-cms.admin.editor.sidebar.tabs.ctas')
                } as RepeaterFieldArgs),
            ]
        } as EditorComponentDefinition);

        this.components.push({
            _id: 'links',
            title: translate('core-cms.admin.editor.sidebar.tabs.links'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: true,
            fields: [
                this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                this.fields.Repeater('links', {
                    addLabel: translate('core-cms.admin.add'),
                    fields: [
                        this.links()
                    ],
                    label: translate('core-cms.admin.editor.sidebar.tabs.links')
                } as RepeaterFieldArgs),
            ]
        } as EditorComponentDefinition);

        this.components.push({
            _id: 'automatic-gallery',
            title: translate('core-cms.admin.editor.sidebar.tabs.automatic-gallery.value'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: true,
            fields: [
                this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                this.fields.HtmlText('content', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                    colors: Object.values(this.colors()),
                    multiline: true,
                    canAnimate: true
                } as HtmlTextFieldArgs),
                this.fields.Number('row-height', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.automatic-gallery.row.height'),
                    default: "350"
                } as NumberFieldArgs),
                this.fields.Number('gap', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.automatic-gallery.gap'),
                    default: "1",
                    step: .5
                } as NumberFieldArgs),
                this.fields.Repeater('medias', {
                    addLabel: translate('core-cms.admin.add'),
                    fields: [
                        ...this.mediaField('media'),
                    ],
                    label: translate('core-cms.admin.editor.sidebar.tabs.medias')
                } as RepeaterFieldArgs),
            ]
        } as EditorComponentDefinition);

        this.components.push({
            _id: 'media',
            title: translate('core-cms.admin.editor.sidebar.tabs.media.value'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: true,
            fields: [
                ...this.mediaField('media'),
            ]
        } as EditorComponentDefinition);

        this.components.push({
            _id: 'carousel',
            title: translate('core-cms.admin.editor.sidebar.tabs.carousel.value'),
            category: translate('core-cms.admin.editor.category.template'),
            canEditAppearance: true,
            fields: [
                this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                this.fields.HtmlText('content', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                    colors: Object.values(this.colors()),
                    multiline: true,
                    canAnimate: true
                } as HtmlTextFieldArgs),
                this.fields.Number('items-per-page', {
                    label: translate('core-cms.admin.editor.sidebar.tabs.carousel.items-per-page'),
                    default: "4"
                } as NumberFieldArgs),
                this.generateTemplateItem(this.components, 'layout-items')
            ]
        } as EditorComponentDefinition);

        this.registerComponent('layouts.grid-auto-fit', {
            _id: 'layouts.grid-auto-fit',
            title: translate('core-cms.admin.editor.sidebar.tabs.grid.value'),
            category: translate('core-cms.admin.editor.category.layout'),
            fields: [
                this.baseTabs([
                    this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                    this.fields.HtmlText('content', {
                        label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                        colors: Object.values(this.colors()),
                        canAnimate: true
                    } as HtmlTextFieldArgs),
                    this.fields.Number('min-item-size', {
                        label: translate('core-cms.admin.editor.sidebar.tabs.grid.item.size.min'),
                        default: "250"
                    } as NumberFieldArgs),
                    this.fields.Number('gap', {
                        label: translate('core-cms.admin.editor.sidebar.tabs.grid.gap'),
                        default: "1.5",
                        step: .5
                    } as NumberFieldArgs),
                    this.generateTemplateItem(this.components, 'layout-items')
                ] as Array<FieldDefinition<any, any>>),
            ]
        } as EditorComponentDefinition);

        this.registerComponent('layouts.even-columns', {
            _id: 'layouts.even-columns',
            title: translate('core-cms.admin.editor.sidebar.tabs.even-columns'),
            category: translate('core-cms.admin.editor.category.layout'),
            fields: [
                this.baseTabs([
                    this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                    this.generateTemplateItem(this.components, 'layout-items')
                ] as Array<FieldDefinition<any, any>>),
            ]
        } as EditorComponentDefinition);

        this.registerComponent('contact', {
            _id: 'contact',
            title: translate('core-cms.admin.editor.sidebar.tabs.contact.value'),
            category: translate('core-cms.admin.editor.category.template'),
            fields: [
                this.baseTabs([
                    this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                    this.fields.HtmlText('content', {
                        label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                        colors: Object.values(this.colors()),
                    } as HtmlTextFieldArgs),
                    this.fields.Repeater('subjects', {
                        label: translate('core-cms.admin.editor.sidebar.tabs.contact.subject.value'),
                        fields: [
                            this.fields.Text('option', {
                                label: translate('core-cms.admin.editor.sidebar.tabs.contact.subject.option')
                            } as TextFieldArgs),
                        ]
                    } as RepeaterFieldArgs)
                ] as Array<FieldDefinition<any, any>>),
            ]
        } as EditorComponentDefinition);

        this.registerComponent('form', {
            _id: 'form',
            title: translate('core-cms.admin.editor.sidebar.tabs.form.value'),
            category: translate('core-cms.admin.editor.category.template'),
            fields: [
                this.baseTabs([
                    this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                    this.fields.HtmlText('content', {
                        label: translate('core-cms.admin.editor.sidebar.tabs.content'),
                        colors: Object.values(this.colors()),
                    } as HtmlTextFieldArgs),
                    this.fields.Repeater('sections', {
                        label: translate('core-cms.admin.editor.sidebar.tabs.form.sections.value'),
                        fields: [
                            this.titleField('title', translate('core-cms.admin.editor.sidebar.tabs.title.value')),
                            this.fields.Checkbox('visible', {
                                label: translate('core-cms.admin.editor.sidebar.tabs.form.sections.visible'),
                                default: true
                            }),
                            this.fields.Repeater('fields', {
                                label: translate('core-cms.admin.editor.sidebar.tabs.form.fields.value'),
                                fields: [
                                    this.fields.Select('type', {
                                        label: translate('core-cms.admin.editor.sidebar.tabs.form.fields.type'),
                                        options: [
                                            {
                                                label: 'Input',
                                                value: 'text'
                                            },
                                            {
                                                label: 'Textarea',
                                                value: 'textarea'
                                            },
                                            {
                                                label: 'Select',
                                                value: 'select'
                                            },
                                            {
                                                label: 'Checkbox',
                                                value: 'checkbox'
                                            },
                                        ],
                                        default: 'text'
                                    } as SelectFieldArgs),
                                    this.fields.Repeater('options', {
                                        label: translate('core-cms.admin.editor.sidebar.tabs.form.fields.options'),
                                        fields: [
                                            this.fields.Text('option', {
                                                label: translate('core-cms.admin.editor.sidebar.tabs.form.fields.options')
                                            } as TextFieldArgs),
                                        ]
                                    } as RepeaterFieldArgs).when('type', 'select'),
                                    this.fields.Text('label', {
                                        label: translate('core-cms.admin.editor.sidebar.tabs.form.fields.label')
                                    } as TextFieldArgs),
                                    this.fields.Text('help', {
                                        label: translate('core-cms.admin.editor.sidebar.tabs.form.fields.help')
                                    } as TextFieldArgs),
                                ]
                            } as RepeaterFieldArgs)
                        ]
                    } as RepeaterFieldArgs),
                ] as Array<FieldDefinition<any, any>>),
            ]
        } as EditorComponentDefinition);

        this.components.map(component => {
            this.registerComponent(component['_id'], {
                _id: component['_id'],
                title: component['title'],
                label: component['label'] ?? component['title'],
                category: component['category'],
                fields: [
                    this.baseTabs(component['fields'])
                ],
            } as EditorComponentDefinition);
        })
    }

    colors() {
        const ret: any[] = [];

        const colors = [
            'primary',
            'accent',
            'orange',
            'yellow',
            'yellowgreen',
            'green',
            'lime',
            'turquoise',
            'cyan',
            'skyblue',
            'blue',
            'purple',
            'magenta',
            'pink',
            'deeppink',
            'red',
            'neutral',
        ];

        colors.forEach(color => {
            const intensities = ['100', '200', '300', '400', '500', '600', '700', '800', '900'];

            intensities.forEach(intensity => {
                // @ts-ignore
                ret[`${color}-${intensity}`] = `var(--${color}-${intensity})`;
            })
        })

        return ret;
    }


    layouts = {
        Tabs,
        Row,
        Dropdown
    }

    fields = {
        Repeater,
        Text,
        Select,
        Media,
        DatePicker,
        Number,
        Range,
        Checkbox,
        Color,
        HtmlText
    }
}
