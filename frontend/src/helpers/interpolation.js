/**
 * Curve fitting
 * @param x
 * @param y
 * @param a
 * @returns {number}
 */
export const lerp = (x, y, a) => x * (1 - a) + y * a

/**
 * Clamps value between min and max
 * @param a
 * @param min
 * @param max
 * @returns {number}
 */
export const clamp = (a, min = 0, max = 1) => Math.min(max, Math.max(min, a))

/**
 * Inverse curve fitting function
 * @param x
 * @param y
 * @param a
 * @returns {number}
 */
export const invlerp = (x, y, a) => clamp((a - x) / (y - x))

/**
 * Maps input to output range
 * @param inputStart
 * @param inputEnd
 * @param outputStart
 * @param outputEnd
 * @param input
 * @returns number
 */
export function range(inputStart, inputEnd, outputStart, outputEnd, input) {
  return lerp(outputStart, outputEnd, invlerp(inputStart, inputEnd, input))
}
