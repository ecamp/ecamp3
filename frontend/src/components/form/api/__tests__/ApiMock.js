function mockPromiseResolving(value) {
  return new Promise((resolve) => {
    const timer = setTimeout(() => {
      clearTimeout(timer)
      resolve(value)
    }, 100)
  })
}

class MockStubbing {
  constructor(fieldName, value) {
    this._fieldName = fieldName
    this._value = value
  }

  forFieldName(fieldName) {
    this._fieldName = fieldName
    return this
  }

  get fieldName() {
    return this._fieldName
  }

  get value() {
    return this._value
  }
}

class NetworkErrorMockStubbing extends MockStubbing {
  constructor() {
    super()
  }
}

class ApiMockState {
  constructor() {
    this._get = jest.fn()
    this._patch = jest.fn()
  }

  getMocks() {
    return {
      get: this._get,
      patch: this._patch,
    }
  }

  get() {
    const apiMock = this
    return {
      thenReturn(mockStubbing) {
        if (!(mockStubbing instanceof MockStubbing)) {
          throw new Error('apiMock must be instance of MockStubbing')
        }
        if (mockStubbing instanceof NetworkErrorMockStubbing) {
          if (mockStubbing.fieldName === undefined || mockStubbing.value !== undefined) {
            throw new Error('fieldName must be defined and value must be undefined')
          }
          const result = {
            _meta: {
              load: Promise.reject({
                name: 'Network error',
                message: 'A network error occurred.',
              }),
            },
          }
          result[mockStubbing.fieldName] = () => result
          apiMock._get.mockReturnValue(result)
          return this
        }
        if (mockStubbing.fieldName === undefined || mockStubbing.value === undefined) {
          throw new Error('fieldName and value must be defined')
        }
        apiMock._get.mockReturnValue({
          [mockStubbing.fieldName]: mockStubbing.value,
          _meta: {
            load: Promise.resolve(mockStubbing.value),
          },
        })
        return this
      },
    }
  }

  patch() {
    const apiMock = this
    return {
      thenReturn(mockStubbing) {
        if (!(mockStubbing instanceof MockStubbing)) {
          throw new Error('apiMock must be instance of MockStubbing')
        }
        if (mockStubbing instanceof NetworkErrorMockStubbing) {
          apiMock._patch.mockImplementation(() => {
            throw {
              name: 'NetworkError',
              message: 'A network error occurred.',
            }
          })
          return this
        }
        if (mockStubbing.fieldName !== undefined || mockStubbing.value === undefined) {
          throw new Error('fieldName must be undefined and value must be defined')
        }
        apiMock._patch.mockReturnValue(mockPromiseResolving(mockStubbing.value))
        return this
      },
    }
  }
}

export class ApiMock {
  static create() {
    return new ApiMockState()
  }

  static success(value) {
    return new MockStubbing(undefined, value)
  }
  static networkError() {
    return new NetworkErrorMockStubbing()
  }
}
