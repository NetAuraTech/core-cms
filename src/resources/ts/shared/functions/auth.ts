/**
 * Checks if the user is admin
 * @return {boolean}
 */
export function isAdmin() {
  //@ts-ignore
  return window.auth.ADMIN === true
}

/**
 * Checks if the user is logged in
 * @return {boolean}
 */
export function isAuthenticated() {
  //@ts-ignore
  return window.auth.USER !== null
}

export function lastNotificationRead() {
  //@ts-ignore
  return window.auth.NOTIFICATION
}

/**
 * Returns the user id
 * @return {number|null}
 */
export function getUserId() {
  //@ts-ignore
  return window.auth.USER
}

/**
 * Checks whether the logged-in user matches the id passed in parameter
 * @return {boolean}
 */
export function canManage(userId?: string) {
  if (isAdmin()) {
    return true
  }
  if (!userId) {
    return false
  }
  //@ts-ignore
  return window.auth.USER === parseInt(userId, 10)
}
