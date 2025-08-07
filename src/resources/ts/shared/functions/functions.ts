import { SyntheticEvent } from 'react'
import { Folder } from '../components/filemanager/types'

export function slugify(str: string) {
  return String(str)
    .normalize('NFKD') // split accented characters into their base characters and diacritical marks
    .replace(/[\u0300-\u036f]/g, '') // remove all the accents, which happen to be all in the \u03xx UNICODE block.
    .trim() // trim leading or trailing whitespace
    .toLowerCase() // convert to lowercase
    .replace(/[^a-z0-9 -]/g, '') // remove non-alphanumeric characters
    .replace(/\s+/g, '-') // replace spaces with hyphens
    .replace(/-+/g, '-') // remove consecutive hyphens
}

export function prevent(callback?: Function) {
  if (!callback) {
    return
  }
  return (e: SyntheticEvent) => {
    e.preventDefault()
    callback(e)
  }
}

export function preventPropagation(callback?: Function) {
  if (!callback) {
    return
  }
  return (e: SyntheticEvent) => {
    e.preventDefault()
    e.stopPropagation()
    callback(e)
  }
}

const ko = Math.pow(2, 10)

function ceil(n: number, decimals: number) {
  return Math.ceil(n * Math.pow(10, decimals)) / Math.pow(10, decimals)
}

/**
 * Converts a file size to a human value
 */
export function human(size: number) {
  let k = size / ko
  let unit = 'k'
  if (k > ko) {
    k = k / ko
    unit = 'M'
  }
  k = ceil(k, k > 10 ? 0 : 1)
  return `${k}${unit}`
}

export function uniq(arr: Folder[]) {
  return arr.filter((value, index) => arr.indexOf(value) === index)
}
