import {useEffect, useState} from "preact/compat";

/**
 * Returns window height
 * @return {number}
 */
export function windowHeight() {
  return (
    window.innerHeight ||
    document.documentElement.clientHeight ||
    document.body.clientHeight
  )
}

/**
 * Returns window width
 * @return {number}
 */
export function windowWidth() {
  return (
    window.innerWidth ||
    document.documentElement.clientWidth ||
    document.body.clientWidth
  )
}

const uuid = new Date().getTime().toString()
if (localStorage) {
  localStorage.setItem('windowId', uuid)
  window.addEventListener('focus', function () {
    localStorage.setItem('windowId', uuid)
  })
}

/**
 * Returns true if the window is active or was the last active window
 */
export function isActiveWindow() {
  if (localStorage) {
    return uuid === localStorage.getItem('windowId')
  } else {
    return true
  }
}

export function useWindowSize() {
  const [windowSize, setWindowSize] = useState(() => {
    if (typeof window === 'undefined') {
      return {
        width: undefined,
        height: undefined,
      };
    }
    return {
      width: window.innerWidth,
      height: window.innerHeight,
    };
  });

  useEffect(() => {
    if (typeof window === 'undefined') {
      return;
    }
    function handleResize() {
      setWindowSize({
        width: window.innerWidth,
        height: window.innerHeight,
      });
    }

    window.addEventListener('resize', handleResize);
    handleResize();

    return () => window.removeEventListener('resize', handleResize);
  }, []);

  return windowSize;
}
