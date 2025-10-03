import { beforeEach, afterEach } from 'vitest'
/**
 * @param { "ClipboardEvent" | "DragEvent" } eventName
 */
export function mockEventClass(eventName) {
  const eventBackup = window[eventName]
  const eventClass = class extends Event {
    constructor(type, eventInitDict) {
      // noinspection JSCheckFunctionSignatures
      super(type, eventInitDict)
    }
  }

  beforeEach(() => {
    if (!eventBackup) {
      window[eventName] = eventClass
    } else {
      console.error(
        `you don't need to mock ${eventName} anymore, the test runner supports it now natively`
      )
    }
  })

  afterEach(() => {
    window[eventName] = eventBackup
  })
}
