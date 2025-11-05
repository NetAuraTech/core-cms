import {FieldDefinition, FieldGroupComponent} from "../../../types";
import {cloneElement} from "preact/compat";
import {Tabs as TabsComponent} from "../../ui/Tabs";
import {defaultFieldProperties} from "../utils";

export type TabDefinition = {
  label: string
  fields: Array<FieldDefinition<any, any>>
}

type FieldOptions = {
  tabs: TabDefinition[]
}

const Component: FieldGroupComponent<FieldOptions> = ({
  children,
  options,
}) => {
  const childrenForTab = (tab: TabDefinition) => {
    return cloneElement(children, {
      fields: tab.fields,
    })
  }

  return (
    <TabsComponent>
      {options.tabs.map(tab => (
        <TabsComponent.Tab key={tab.label} title={tab.label}>
          <div className="grid">{childrenForTab(tab)}</div>
        </TabsComponent.Tab>
      ))}
    </TabsComponent>
  )
}

export function Tabs(...tabs: TabDefinition[]) {
  return {
    ...defaultFieldProperties(),
    group: true as const,
    options: { tabs: tabs },
    render: Component,
    fields: tabs.reduce(
      (acc, tab) => [...acc, ...tab.fields],
      [] as TabDefinition['fields'],
    ) as FieldDefinition<any, any>[],
  }
}
