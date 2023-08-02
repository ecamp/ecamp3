import { getQueryAsString } from '@/helpers/querySyncHelper'

describe('getQueryAsString', () => {
  it('should return an empty string for an empty query object', () => {
    const result = getQueryAsString({})
    expect(result).toBe('')
  })

  it('should convert a simple query object to a query string', () => {
    const queryObj = {
      responsible: '3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2',
      category: 'c829d2d0-7e81-4b6e-8f68-9b4d2c58a109',
    }
    const result = getQueryAsString(queryObj)
    expect(result).toBe(
      '?responsible=3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2&category=c829d2d0-7e81-4b6e-8f68-9b4d2c58a109'
    )
  })

  it('should handle array values in the query object', () => {
    const queryObj = {
      responsible: [
        '3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2',
        '65cdde81-32bc-4ff3-9f59-9e23c1a6ec6a',
      ],
      category: 'c829d2d0-7e81-4b6e-8f68-9b4d2c58a109',
      progressLabel: ['f4d40dd5-049b-4ae0-9a04-18c34f35b4a5'],
    }
    const result = getQueryAsString(queryObj)
    expect(result).toBe(
      '?responsible=3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2&responsible=65cdde81-32bc-4ff3-9f59-9e23c1a6ec6a&category=c829d2d0-7e81-4b6e-8f68-9b4d2c58a109&progressLabel=f4d40dd5-049b-4ae0-9a04-18c34f35b4a5'
    )
  })

  it('should properly encode special characters in keys and values', () => {
    const queryObj = {
      'responsible@1': '3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2',
      'category&2': 'c829d2d0-7e81-4b6e-8f68-9b4d2c58a109',
      progressLabel$3: 'f4d40dd5-049b-4ae0-9a04-18c34f35b4a5',
    }
    const result = getQueryAsString(queryObj)
    expect(result).toBe(
      '?responsible%401=3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2&category%262=c829d2d0-7e81-4b6e-8f68-9b4d2c58a109&progressLabel%243=f4d40dd5-049b-4ae0-9a04-18c34f35b4a5'
    )
  })

  it('should handle a mix of string and array values', () => {
    const queryObj = {
      responsible: [
        '3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2',
        '65cdde81-32bc-4ff3-9f59-9e23c1a6ec6a',
      ],
      category: 'c829d2d0-7e81-4b6e-8f68-9b4d2c58a109',
      progressLabel: ['f4d40dd5-049b-4ae0-9a04-18c34f35b4a5'],
    }
    const result = getQueryAsString(queryObj)
    expect(result).toBe(
      '?responsible=3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2&responsible=65cdde81-32bc-4ff3-9f59-9e23c1a6ec6a&category=c829d2d0-7e81-4b6e-8f68-9b4d2c58a109&progressLabel=f4d40dd5-049b-4ae0-9a04-18c34f35b4a5'
    )
  })

  it('should ignore key-value pairs with null values', () => {
    const queryObj = {
      responsible: '3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2',
      category: null,
      progressLabel: 'f4d40dd5-049b-4ae0-9a04-18c34f35b4a5',
    }
    const result = getQueryAsString(queryObj)
    expect(result).toBe(
      '?responsible=3bde8a8a-4dd5-45aa-8b21-f3f9811b5ea2&progressLabel=f4d40dd5-049b-4ae0-9a04-18c34f35b4a5'
    )
  })
})
