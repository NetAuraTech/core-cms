import Icon from '../shared/components/Icon'
import { ComponentChildren } from 'preact';
import {slideUp} from "../shared/functions/animation";
import {useEffect, useRef, useState} from "preact/compat";

interface Props {
  type: string
  message: string
  duration?: number
  isFloating: boolean
  onClose?: () => void
  className?: string
  children?: ComponentChildren
}

export function Alert({
  type = 'success',
  message = '',
  duration = 3,
  isFloating = false,
}: Props) {
  const ref = useRef<HTMLDivElement>(null)
  const [alertType, setAlertType] = useState(type)
  const [icon, setIcon] = useState('')
  const spanRef = useRef<HTMLSpanElement>(null)

  useEffect(() => {
    if (type !== '' || true) {
      setAlertType(type)
      setIcon(type)
    }
    if (type === 'error' || type === '') {
      setAlertType('danger')
      setIcon('warning')
    }
    if (type === 'success') {
      setIcon('check')
    }
  }, [type])

  useEffect(() => {
    if (spanRef.current) {
      spanRef.current.innerHTML = message
    }
  }, [spanRef.current, message])

  const close = (e: any) => {
    if (ref.current) {
      const alertElement = ref.current as HTMLDivElement
      ref.current.classList.add('out')
      window.setTimeout(async () => {
        await slideUp(alertElement)
        alertElement.dispatchEvent(new CustomEvent('close'))
      }, 500)
    }
    e.preventDefault()
  }

  useEffect(() => {
    if (duration != 0) {
      const timer = window.setTimeout(close, duration * 1000)
      return () => clearTimeout(timer)
    }
  }, [duration])

  return (
      <div
          ref={ref}
          className={`alert ${isFloating ? 'is-floating' : ''}`}
      >
          <div
              className={`alert__wrapper`}
              data-type={alertType}
          >
              <Icon name={icon} additionalClass={"small"}/>
              <span ref={spanRef}/>
              <button className='alert__close button padding-0' data-type={"transparent"} onClick={close}>
                  <Icon name={'cross'} additionalClass={"small"}/>
              </button>
              {duration != 0 && (
                  <div
                      className='alert__progress'
                      style={{animationDuration: `${duration}s`}}
                  ></div>
              )}
          </div>
      </div>
  )
}
