function mockPromiseResolving (value) {
  return new Promise((resolve) => {
    const timer = setTimeout(() => {
      clearTimeout(timer)
      resolve(value)
    }, 100)
  })
}

class MockStubbing {
  constructor (fieldName, value) {
    this._fieldName = fieldName
    this._value = value
  }

  forFieldName (fieldName) {
    this._fieldName = fieldName
    return this
  }

  get fieldName () {
    return this._fieldName
  }

  get value () {
    return this._value
  }
}

class ApiMockState {
  constructor () {
    this._get = jest.fn()
    this._patch = jest.fn()
  }

  getMocks () {
    return {
      get: this._get,
      patch: this._patch
    }
  }

  get () {
    const apiMock = this
    return {
      thenReturn (mockStubbing) {
        if (!(mockStubbing instanceof MockStubbing)) {
          throw new Error('apiMock must be instance of MockStubbing')
        }
        if (mockStubbing.fieldName === undefined || mockStubbing.value === undefined) {
          throw new Error('fieldName and value must be defined')
        }
        apiMock._get.mockReturnValue({
          [mockStubbing.fieldName]: mockStubbing.value,
          _meta: {
            load: Promise.resolve(mockStubbing.value)
          }
        })
      }
    }
  }

  patch () {
    const apiMock = this
    return {
      thenReturn (mockStubbing) {
        if (!(mockStubbing instanceof MockStubbing)) {
          throw new Error('apiMock must be instance of MockStubbing')
        }
        if (mockStubbing.fieldName !== undefined || mockStubbing.value === undefined) {
          throw new Error('fieldName must be undefined and value must be defined')
        }
        apiMock._patch.mockReturnValue(mockPromiseResolving(mockStubbing.value))
      }
    }
  }
}

export class ApiMock {
  static create () {
    return new ApiMockState()
  }

  static success (value) {
    return new MockStubbing(undefined, value)
  }
}
