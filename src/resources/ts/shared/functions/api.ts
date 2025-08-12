import Alert from "../elements/Alert";


type Params = {
  [key: string]: string | object | FormData
}

/**
 * @return {Promise<Object>}
 */
export async function jsonFetch(url: URL | string, params: Params = {}) {
  // If we receive a FormData, we convert it into an object
  if (params.body instanceof FormData) {
    params.body = Object.fromEntries(params.body)
  }
  // If we receive an object, we convert it into a JSON string
  if (params.body && typeof params.body === 'object') {
    params.body = JSON.stringify(params.body)
  }
  params = {
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    ...params,
  }

  const response = await fetch(url, params)
  if (response.status === 204) {
    return null
  }
  const data = await response.json()
  if (response.ok) {
    return data
  }
  throw new ApiError(data, response.status)
}

/**
 * @return {Promise<Object>}
 */
export async function jsonFetchOrFlash(url: URL | string, params: Params = {}) {
  try {
    return await jsonFetch(url, params)
  } catch (e) {
    const result = (e as Error).message
    if (e instanceof ApiError) {
      Alert.flash(e.name, 'danger', 4)
    } else {
      Alert.flash(result, 'danger', 4)
    }
    return null
  }
}

/**
 * Capture API feedback
 */
export async function catchViolations(p: Promise<any>) {
  try {
    return [await p, null]
  } catch (e) {
    if (e instanceof ApiError) {
      return [null, e.violations]
    }
    throw e
  }
}

type Violation = {
  propertyPath: string
  message: string
}

type Error = Record<string, string[]>;

type Data = {
  title?: string
  message?: string
  detail?: string
  violations?: Violation[]
  errors?: Error[]
}

/**
 * Represents an API error
 * @property {{
 *  violations: {propertyPath: string, message: string}
 * }} data
 */
export class ApiError {
  data: Data
  status: number

  constructor(data: Data, status: number) {
    this.data = data
    this.status = status
  }

  get name() {
    return `${this.data.title} ${this.data.detail || ''}`
  }

  // Returns violations indexed by propertyPath
  get violations() {
    if (!this.data.violations && !this.data.errors) {
      return {
        main: `${this.data.title || this.data.message} ${this.data.detail || ''}`,
      }
    }

    if(this.data.errors) {
      return this.data.errors;
    }


    return this.data.violations.reduce(
      (acc: Record<string, Array<string>>, violation: Violation) => {
        if (acc[violation.propertyPath]) {
          acc[violation.propertyPath]?.push(violation.message)
        } else {
          acc[violation.propertyPath] = [violation.message]
        }
        return acc
      },
      {},
    )
  }

  // Retrieves the violation list for a given field
  violationsFor(field: string) {
    return this.data.violations
      .filter((v: Violation) => v.propertyPath === field)
      .map((v: Violation) => v.message)
  }
}
