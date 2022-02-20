/**
 * This file provides the `global` and `window` variables in web workers.
 * It must be its own file so it can be imported at the very top of any worker scripts that need it,
 * above all other imports.
 * This shim supplements the react-pdf vite shim from https://github.com/exogee-technology/vite-plugin-shim-react-pdf
 */

// eslint-disable-next-line no-undef
if (typeof WorkerGlobalScope !== 'undefined' && self instanceof WorkerGlobalScope) {
  self.global = self
  self.window = self
}
