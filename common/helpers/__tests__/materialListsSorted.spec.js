import materialListsSorted from '../materialListsSorted.js'

describe('materialListsSorted', () => {
  const list1 = { campCollaboration: '1a2b3c4d', name: 'B2-name' }
  const list2 = { campCollaboration: '1a2b3c4d', name: 'b1-name' }
  const list3 = { campCollaboration: '1a2b3c4d', name: 'a3-name' }
  const list4 = { campCollaboration: null, name: 'B2-name' }
  const list5 = { campCollaboration: null, name: 'b1-name' }
  const list6 = { campCollaboration: null, name: 'a3-name' }

  it('sorts Non-User bevor User-Lists, then alphabetically', () => {
    expect(materialListsSorted([list1, list2, list3, list4, list5, list6]))
      .toEqual([list6, list5, list4, list3, list2, list1])
  })
})
