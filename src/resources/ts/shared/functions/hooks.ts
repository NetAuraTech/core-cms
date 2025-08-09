import {RefObject, useCallback, useEffect, useMemo, useRef, useState} from "preact/compat";
import {ApiError, jsonFetch} from "./api";
import Alert from "../elements/Alert";
import {strToDom} from "./dom";
import {debounce} from "lodash";
import {uniqId} from "./string";

/**
 * Alternate a value
 */
export function useToggle(initialValue: boolean = false) {
  const [value, setValue] = useState(initialValue)
  return [value, useCallback(() => setValue(v => !v), []), setValue] as const
}

/**
 * Value with the option of pushing an additional value
 */
export function usePrepend(initialValue: any = []) {
  const [value, setValue] = useState(initialValue)
  return [
    value,
    useCallback((item: any) => {
      setValue((v: any) => [item, ...v])
    }, []),
  ]
}

/**
 * Hook effect to detect clicking outside an element
 */
export function useClickOutside(ref: any, cb: () => void) {
  useEffect(() => {
    if (cb === null) {
      return
    }
    const escCb = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && ref.current) {
        cb()
      }
    }
    const clickCb = (e: Event) => {
      if (ref.current && !ref.current.contains(e.target)) {
        cb()
      }
    }
    document.addEventListener('click', clickCb)
    document.addEventListener('keyup', escCb)
    return function cleanup() {
      document.removeEventListener('click', clickCb)
      document.removeEventListener('keyup', escCb)
    }
  }, [ref, cb])
}

/**
 * Focus the first field in the element corresponding to the ref
 */
export function useAutofocus(ref: any, focus: boolean) {
  useEffect(() => {
    if (focus && ref.current) {
      const input = ref.current.querySelector('input, textarea')
      if (input) {
        input.focus()
      }
    }
  }, [focus, ref])
}

/**
 * Hook calling fetch and flash on error/success
 * @return {{data: Object|null, fetch: fetch, loading: boolean, done: boolean}}
 */
export function useJsonFetchOrFlash(url: string, params = {}) {
  const [state, setState] = useState({
    loading: false,
    data: null,
    done: false,
  })
  const fetch = useCallback(
    async (localUrl: string, localParams: string) => {
      setState(s => ({ ...s, loading: true }))
      try {
        const response = await jsonFetch(localUrl || url, localParams || params)
        setState(s => ({ ...s, loading: false, data: response, done: true }))
        return response
      } catch (e) {
        const result = (e as Error).message
        if (e instanceof ApiError) {
          Alert.flash(e.name, 'danger', 4)
        } else {
          Alert.flash(result, 'danger', 4)
        }
      }
      setState(s => ({ ...s, loading: false }))
    },
    [url, params],
  )
  return { ...state, fetch }
}

/**
 * useEffect for an asynchronous function
 */
export function useAsyncEffect(fn: () => void, deps: any[] = []) {
  useEffect(() => {
    fn()
  }, deps)
}

export const PROMISE_PENDING = 0
export const PROMISE_DONE = 1
export const PROMISE_ERROR = -1

/**
 * Decorate a promise and return its status
 */
export function usePromiseFn(fn: (...args: any[]) => void) {
  const [state, setState] = useState(-10)
  const resetState = useCallback(() => {
    setState(-10)
  }, [])

  const wrappedFn = useCallback(
      // @ts-ignore
    async (...args: any[]) => {
      setState(PROMISE_PENDING)
      try {
        await fn(...args)
        setState(PROMISE_DONE)
      } catch (e) {
        setState(PROMISE_ERROR)
        throw e
      }
    },
    [fn],
  )

  return [state, wrappedFn, resetState]
}

/**
 * HHook to detect when an element becomes visible on the screen
 *
 * @export
 * @returns {object} visibility
 */
export function useVisibility(node: any, once = true, options = {}) {
  const [visible, setVisibilty] = useState(false)
  const isIntersecting = useRef(null)

  const handleObserverUpdate = (entries: any) => {
    const ent = entries[0]

    if (isIntersecting.current !== ent.isIntersecting) {
      setVisibilty(ent.isIntersecting)
      isIntersecting.current = ent.isIntersecting
    }
  }

  const observer =
    once && visible
      ? null
      : new IntersectionObserver(handleObserverUpdate, options)

  useEffect(() => {
    const element = node instanceof HTMLElement ? node : node.current

    if (!element || observer === null) {
      return
    }

    observer.observe(element)

    return function cleanup() {
      observer.unobserve(element)
    }
  })

  return visible
}

let favIconBadge: HTMLElement | ChildNode | null = null

export function useNotificationCount(n: number) {
  // @ts-ignore
  useAsyncEffect(async () => {
    if (favIconBadge === null) {
      if (n === 0) {
        return
      }
      //await import('favicon-badge')
      favIconBadge = strToDom(
        `<favicon-badge src="${document
          .querySelector('link[rel=icon]')
          ?.getAttribute('href')}" badge="true" badgeSize="6"/>`,
      )
      document.head.appendChild(favIconBadge)
      return
    }
    if ('setAttribute' in favIconBadge) {
      favIconBadge.setAttribute('badge', n === 0 ? 'false' : 'true')
    }
  }, [n])
}

export function useEffectDebounced(
  callback: Function,
  deps: any[],
  time: number,
) {
  const callbackRef = useRef<Function>(callback)
  const debouncedCallback = useMemo(() => {
    return debounce((...args: any[]) => callbackRef.current(...args), time)
  }, [])
  callbackRef.current = callback

  useEffect(() => {
    debouncedCallback()
  }, deps)
}

/**
 * Delay the visibility change for a component
 */
export function useStateDelayed(
  originalState: boolean,
  duration = 700,
  onlyOnFalse = true,
): boolean {
  const [delayedState, setDelayedState] = useState(originalState)
  useEffect(() => {
    if (originalState && onlyOnFalse) {
      setDelayedState(originalState)
    } else {
      const timer = window.setTimeout(() => setDelayedState(originalState), 700)
      return () => window.clearTimeout(timer)
    }
  }, [originalState])

  return delayedState
}

type EventNames = keyof HTMLElementEventMap

const stopPropagation = (e: Event) => e.stopPropagation()

export function useStopPropagation(
  ref: RefObject<HTMLElement>,
  eventNames: EventNames[],
) {
  useEffect(() => {
    if (!ref.current) {
      return
    }
    eventNames.map(eventName => {
      ref.current!.addEventListener(eventName, stopPropagation)
    })
    return () => {
      if (!ref.current) {
        return
      }
      eventNames.map(eventName => {
        ref.current!.removeEventListener(eventName, stopPropagation)
      })
    }
  })
}

export function useUniqId(prefix: string = ''): string {
  return useMemo(() => prefix + uniqId(), [])
}

export function useUpdateEffect(cb: Function, deps: any[]): void {
  const isFirstRender = useRef(true)

  useEffect(() => {
    if (isFirstRender.current) {
      isFirstRender.current = false
      return
    }
    return cb()
  }, deps)
}
