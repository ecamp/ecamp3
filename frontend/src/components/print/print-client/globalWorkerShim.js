/**
 * This file provides the `global` and `window` variables in web workers.
 * It must be its own file, so it can be imported at the very top of any worker scripts that need it,
 * above all other imports.
 */

// eslint-disable-next-line no-undef
if (typeof WorkerGlobalScope !== 'undefined' && self instanceof WorkerGlobalScope) {
  self.global = self
  self.window = self
}
