// this avoids the error "Cannot stringify arbitrary non-POJOs"

export default definePayloadPlugin(() => {
  definePayloadReducer('JSONifiable', (data) => {
    if (data && typeof data === 'object' && 'toJSON' in data) {
      return JSON.stringify(data.toJSON())
    }
  })
  definePayloadReviver('JSONifiable', (data) => JSON.parse(data))
})
