import { ComponentChildren, h } from "preact";
import { useEffect, useRef } from "preact/hooks";
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

type TooltipProps = {
  content: ComponentChildren;
  children: ComponentChildren;
  visible?: boolean;
  trigger?: 'mouseenter' | 'click' | 'focus' | 'manual';
  placement?: 'top' | 'bottom' | 'left' | 'right';
  delay?: number | [number, number];
  theme?: string;
  allowHTML?: boolean;
}

export function Tooltip({ content, children, visible, trigger = 'mouseenter', placement = 'top', delay = 0, theme, allowHTML = false }: TooltipProps) {
  const elementRef = useRef<HTMLDivElement>(null);
  const tippyInstanceRef = useRef<any>(null);

  useEffect(() => {
    if (elementRef.current) {
      // @ts-ignore
      tippyInstanceRef.current = tippy(elementRef.current, {
        content: content,
        trigger: trigger,
        placement: placement,
        delay: delay,
        theme: theme,
        allowHTML: allowHTML,
        hideOnClick: trigger === 'click' ? true : 'toggle',
        interactive: trigger === 'click' || trigger === 'manual',
      });
    }

    return () => {
      if (tippyInstanceRef.current) {
        tippyInstanceRef.current.destroy();
        tippyInstanceRef.current = null;
      }
    };
  }, [trigger, placement, delay, theme, allowHTML]);

  useEffect(() => {
    if (tippyInstanceRef.current) {
      tippyInstanceRef.current.setContent(content);

      if (visible !== undefined) {
        if (visible) {
          tippyInstanceRef.current.show();
        } else {
          tippyInstanceRef.current.hide();
        }
      }
    }
  }, [content, visible]);

  return (
      <div ref={elementRef} style={{ display: 'inline-block' }}>
        {children}
      </div>
  );
}
