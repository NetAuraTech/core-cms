import { ComponentChildren, h } from "preact";
import { useState } from "preact/hooks";
import { Children, CSSProperties, ReactElement } from "preact/compat";

type TabsProps = {
    children: ComponentChildren;
    className?: string;
    style?: CSSProperties;
}

type TabProps = {
    children: ComponentChildren;
    title: string;
    className?: string;
    style?: CSSProperties;
}

export function Tabs({ children, className, style, ...props }: TabsProps) {
    const childrenArray = Children.toArray(
        children,
    ) as ReactElement<TabProps>[];

    const [currentTab, setCurrentTab] = useState(childrenArray[0]?.props.title || '');

    return (
        <div className={className} style={style} {...props}>
            <div className={'flex-group align-items-center'}>
                {childrenArray.map(child => (
                    <button
                        className="button"
                        type="button"
                        data-type={currentTab === child.props.title ? 'accent' : 'transparent'}
                        onClick={() => setCurrentTab(child.props.title)}
                        key={child.props.title}
                        style={{fontWeight: 'normal', textTransform: 'capitalize'} as CSSProperties}
                    >
                        {child.props.title}
                    </button>
                ))}
            </div>

            {childrenArray.map(child => (
                <div
                    key={child.props.title}
                    className="padding-block-4 padding-inline-1"
                    style={{ display: currentTab === child.props.title ? 'block' : 'none' }}
                >
                    {child}
                </div>
            ))}
        </div>
    );
}

function Tab(props: TabProps) {
    return <div {...props}>{props.children}</div>;
}

Tabs.Tab = Tab;
