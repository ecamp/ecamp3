function createRandomId(length = 5) {
  const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
  const bytes = new Uint8Array(length)

  crypto.getRandomValues(bytes)

  return Array.from(bytes, (byte) => chars[byte % chars.length]).join('')
}

export const runIdFixture = {
  createRandomId: async ({}, use: (a: () => string) => Promise<void>) => {
    await use(createRandomId)
  },
  runId: async ({}, use: (a: string) => Promise<void>) => {
    const runId = createRandomId()
    await use(runId)
  },
}
