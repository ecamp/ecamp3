import { createLocalVue, mount } from '@vue/test-utils'
import ApiStorePlugin from '@/plugins/store/apiPlugin'
import store from '@/plugins/store'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import VueAxios from 'vue-axios'
import Vuex from 'vuex'
import { cloneDeep } from 'lodash'
import embeddedSingleEntity from './resources/embedded-single-entity'
import referenceToSingleEntity from './resources/reference-to-single-entity'
import embeddedCollection from './resources/embedded-collection'
import linkedSingleEntity from './resources/linked-single-entity'
import linkedCollection from './resources/linked-collection'
import collectionFirstPage from './resources/collection-firstPage'
import collectionPage1 from './resources/collection-page1'
import circularReference from './resources/circular-reference'
import multipleReferencesToUser from './resources/multiple-references-to-user'
import templatedLink from './resources/templated-link'

async function letNetworkRequestFinish () {
  await new Promise(resolve => {
    setTimeout(() => resolve())
  })
}

jest.unmock('lodash')

describe('API store', () => {
  let localVue
  let axiosMock
  let vm
  const stateCopy = cloneDeep(store.state)

  beforeEach(() => {
    localVue = createLocalVue()
    localVue.use(ApiStorePlugin)
    localVue.use(Vuex)
    axiosMock = new MockAdapter(axios)
    localVue.use(VueAxios, axiosMock)
    // Restore the initial state before each test
    store.replaceState(cloneDeep(stateCopy))
    vm = mount({ localVue, store, template: '<div></div>' }).vm
    vm.api = localVue.prototype.api
  })

  it('imports embedded single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('http://localhost/camps/1')
    expect(vm.api.get('/camps/1').campType()._meta.self).toEqual('http://localhost/campTypes/20')
    expect(vm.api.get('/campTypes/20')._meta.self).toEqual('http://localhost/campTypes/20')
    done()
  })

  it('imports reference to single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, referenceToSingleEntity.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(referenceToSingleEntity.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('http://localhost/camps/1')
    done()
  })

  it('imports embedded collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedCollection.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(embeddedCollection.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('http://localhost/camps/1')
    expect(vm.api.get('/camps/1').periods().items[0]._meta.self).toEqual('http://localhost/periods/104')
    expect(vm.api.get('/camps/1').periods().items[1]._meta.self).toEqual('http://localhost/periods/128')
    expect(vm.api.get('/periods/104')._meta.self).toEqual('http://localhost/periods/104')
    expect(vm.api.get('/periods/104').camp()._meta.self).toEqual('http://localhost/camps/1')
    expect(vm.api.get('/periods/128')._meta.self).toEqual('http://localhost/periods/128')
    expect(vm.api.get('/periods/128').camp()._meta.self).toEqual('http://localhost/camps/1')
    done()
  })

  it('imports linked single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedSingleEntity.serverResponse)
    const mainLeader = {
      serverResponse: { id: 83, name: 'Smiley', _links: { self: { href: '/users/83' } } },
      storeState: { id: 83, name: 'Smiley', _meta: { self: '/users/83' } }
    }
    axiosMock.onGet('http://localhost/users/83').reply(200, mainLeader.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(linkedSingleEntity.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('http://localhost/camps/1')
    // expect(vm.api.get('/camps/1').main_leader()._meta.self).toEqual('http://localhost/users/83')
    expect(vm.api.get('/camps/1').main_leader()._meta.loading).toEqual(true)
    await letNetworkRequestFinish()
    expect(vm.api.get('/camps/1').main_leader()._meta).toMatchObject({ self: 'http://localhost/users/83' })
    expect(vm.api.get('/users/83')._meta.self).toEqual('http://localhost/users/83')
    done()
  })

  it('imports paginatable collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedCollection.serverResponse)
    const activities = {
      serverResponse: {
        _embedded: {
          items: [
            { id: 1234, title: 'LS Volleyball', _links: { self: { href: '/activities/1234' } } },
            { id: 1236, title: 'LA Blachen', _links: { self: { href: '/activities/1236' } } }
          ]
        },
        _links: { self: { href: '/camps/1/activities' }, first: { href: '/camps/1/activities' } },
        _page: 0,
        _per_page: -1,
        _total: 2,
        page_count: 1
      },
      storeState: {
        items: [
          {
            href: '/activities/1234'
          },
          {
            href: '/activities/1236'
          }
        ],
        first: {
          href: '/camps/1/activities'
        },
        _page: 0,
        _per_page: -1,
        _total: 2,
        page_count: 1,
        _meta: {
          self: '/camps/1/activities'
        }
      }
    }
    axiosMock.onGet('http://localhost/camps/1/activities').reply(200, activities.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(linkedCollection.storeState)
    expect(vm.api.get('/camps/1').activities().items).toBeInstanceOf(Array)
    expect(vm.api.get('/camps/1').activities().items.length).toEqual(0)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api['/camps/1/activities']).toMatchObject(activities.storeState)
    expect(vm.api.get('/camps/1').activities().items.length).toEqual(2)
    expect(vm.api.get('/camps/1').activities().items[0]._meta.self).toEqual('http://localhost/activities/1234')
    expect(vm.api.get('/camps/1').activities().items[1]._meta.self).toEqual('http://localhost/activities/1236')
    done()
  })

  it('imports paginatable collection with multiple pages', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1/activities?page=0&page_size=2').reply(200, collectionFirstPage.serverResponse)
    axiosMock.onGet('http://localhost/camps/1/activities?page=1&page_size=2').reply(200, collectionPage1.serverResponse)

    // when
    vm.api.get('/camps/1/activities?page_size=2&page=0')

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1/activities?page=0&page_size=2': { _meta: { self: '/camps/1/activities?page=0&page_size=2', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(collectionFirstPage.storeState)
    expect(vm.api.get('/camps/1/activities?page_size=2&page=0').items.length).toEqual(2)

    // when
    vm.api.get('/camps/1/activities?page_size=2&page=1')

    // then
    expect(vm.$store.state.api).toMatchObject({
      ...collectionFirstPage.storeState,
      '/camps/1/activities?page=1&page_size=2': { _meta: { self: '/camps/1/activities?page=1&page_size=2', loading: true } }
    })
    expect(vm.api.get('/camps/1/activities?page_size=2&page=0').items.length).toEqual(2)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject({ ...collectionFirstPage.storeState, ...collectionPage1.storeState })
    expect(vm.api.get('/camps/1/activities?page_size=2&page=0').items.length).toEqual(2)
    expect(vm.api.get('/camps/1/activities?page_size=2&page=1').items.length).toEqual(1)
    done()
  })

  it('allows redundantly using get with an object', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)

    // when
    vm.api.get({ _meta: { self: '/camps/1' } })

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    done()
  })

  it('allows using get with a loading object with known URI', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    const loadingObject = vm.api.get('/camps/1')

    // when
    vm.api.get(loadingObject)

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    done()
  })

  it('allows using get with a loading object with unknown URI', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    const loadingObject = vm.api.get('/camps/1').campType()

    // when
    vm.api.get(loadingObject)

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    done()
  })

  it('allows accessing _meta in a loading object with unknown URI', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    const loadingObject = vm.api.get('/camps/1').campType()

    // when
    const meta = vm.api.get(loadingObject)._meta

    // then
    expect(`${meta}`).toEqual('')
    done()
  })

  it('returns the correct object when awaiting._meta.load on a loadingProxy', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    const loadingProxy = vm.api.get('/camps/1')
    expect(loadingProxy[Symbol.for('isLoadingProxy')]).toBe(true)

    // when
    loadingProxy._meta.load.then(loadedData => {
      // then
      expect(loadedData).toMatchObject({ id: 1, _meta: { self: 'http://localhost/camps/1' } })

      done()
    })

    letNetworkRequestFinish()
  })

  it('returns the correct object when awaiting._meta.load on a loaded object', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    const camp = vm.api.get('/camps/1')
    expect(camp[Symbol('isLoadingProxy')]).not.toBe(true)

    // when
    camp._meta.load.then(loadedData => {
      // then
      expect(loadedData).toMatchObject({ id: 1, _meta: { self: 'http://localhost/camps/1' } })

      done()
    })
  })

  it('returns the correct load promise when reloading an object', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, {
      id: 1,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    })
    axiosMock.onGet('http://localhost/camps/1').reply(200, {
      id: 2,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    })
    const camp = vm.api.get('/camps/1')
    await letNetworkRequestFinish()

    // when
    const load = vm.api.reload(camp)

    // then
    await letNetworkRequestFinish()
    const result = await load
    expect(result).toMatchObject({ id: 2, _meta: { self: 'http://localhost/camps/1' } })
    done()
  })

  it('returns the correct load promise when prematurely reloading an object', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, {
      id: 1,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    })
    axiosMock.onGet('http://localhost/camps/1').reply(200, {
      id: 2,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    })

    // when
    const load = vm.api.reload(vm.api.get('/camps/1'))

    // then
    await letNetworkRequestFinish()
    const result = await load
    expect(result).toMatchObject({ id: 1, _meta: { self: 'http://localhost/camps/1' } })
    done()
  })

  it('returns the correct load promise when getting an object that is currently reloading', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, {
      id: 1,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    })
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, {
      id: 2,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    })
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, {
      id: 3,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    })
    const loaded = vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    vm.api.reload(loaded)

    // when
    const load = vm.api.get(loaded)._meta.load

    // then
    await letNetworkRequestFinish()
    const result = await load
    expect(result).toMatchObject({ id: 2, _meta: { self: 'http://localhost/camps/1' } })
    done()
  })

  it('throws when trying to access _meta in an invalid object', () => {
    // given

    // when
    expect(() => vm.api.get({})._meta)

      // then
      .toThrow(Error)
  })

  it('purges and later re-fetches a URI from the store', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    axiosMock.onGet('http://localhost/campTypes/20').reply(200, embeddedSingleEntity.serverResponse._embedded.campType)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    const storeStateWithoutCampType = cloneDeep(embeddedSingleEntity.storeState)
    delete storeStateWithoutCampType['/campTypes/20']

    // when
    vm.api.purge('/campTypes/20')

    // then
    expect(vm.$store.state.api).toMatchObject(storeStateWithoutCampType)
    expect(vm.api.get('/camps/1').campType()._meta.loading).toEqual(true)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    done()
  })

  it('purges and later re-fetches an object from the store', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    axiosMock.onGet('http://localhost/campTypes/20').reply(200, embeddedSingleEntity.serverResponse._embedded.campType)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    const campType = vm.api.get('/camps/1').campType()
    const storeStateWithoutCampType = cloneDeep(embeddedSingleEntity.storeState)
    delete storeStateWithoutCampType['/campTypes/20']

    // when
    vm.api.purge(campType)

    // then
    expect(vm.$store.state.api).toMatchObject(storeStateWithoutCampType)
    expect(vm.api.get('/camps/1').campType()._meta.loading).toEqual(true)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    done()
  })

  it('reloads a URI from the store', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    const campType = {
      serverResponse: {
        id: 20,
        name: 'Nicht-J+S-Lager',
        js: false,
        targetGroup: 'Teens',
        _links: {
          self: {
            href: '/campTypes/20'
          }
        }
      },
      storeState: {
        id: 20,
        name: 'Nicht-J+S-Lager',
        js: false,
        targetGroup: 'Teens',
        _meta: {
          self: '/campTypes/20'
        }
      }
    }
    axiosMock.onGet('http://localhost/campTypes/20').reply(200, campType.serverResponse)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()

    // when
    vm.api.reload('/campTypes/20')

    // then
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api['/campTypes/20']).toEqual(campType.storeState)
    done()
  })

  it('reloads an object from the store', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    const campTypeData = {
      serverResponse: {
        id: 20,
        name: 'Nicht-J+S-Lager',
        js: false,
        targetGroup: 'Teens',
        _links: {
          self: {
            href: '/campTypes/20'
          }
        }
      },
      storeState: {
        id: 20,
        name: 'Nicht-J+S-Lager',
        js: false,
        targetGroup: 'Teens',
        _meta: {
          self: '/campTypes/20'
        }
      }
    }
    axiosMock.onGet('http://localhost/campTypes/20').reply(200, campTypeData.serverResponse)
    vm.api.get('/camps/1').campType()
    await letNetworkRequestFinish()
    const campType = vm.api.get('/camps/1').campType()

    // when
    vm.api.reload(campType)

    // then
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api['/campTypes/20']).toEqual(campTypeData.storeState)
    done()
  })

  it('reloads an embedded collection from the store by reloading the superordinate object', async done => {
    // given
    const campData = {
      serverResponse: {
        id: 20,
        _embedded: {
          activityTypes: [
            {
              id: 123,
              name: 'LS',
              _links: {
                self: {
                  href: '/activityTypes/123'
                }
              }
            },
            {
              id: 124,
              name: 'LP',
              _links: {
                self: {
                  href: '/activityTypes/124'
                }
              }
            }
          ]
        },
        _links: {
          self: {
            href: '/campTypes/20'
          }
        }
      },
      serverResponse2: {
        id: 20,
        _embedded: {
          activityTypes: [
            {
              id: 123,
              name: 'LS',
              _links: {
                self: {
                  href: '/activityTypes/123'
                }
              }
            }
          ]
        },
        _links: {
          self: {
            href: '/campTypes/20'
          }
        }
      },
      storeState: [
        {
          href: '/activityTypes/123'
        }
      ]
    }
    axiosMock.onGet('http://localhost/camps/1').reply(200, campData.serverResponse)
    axiosMock.onGet('http://localhost/camps/1').reply(200, campData.serverResponse2)
    vm.api.get('/camps/1').activityTypes()
    await letNetworkRequestFinish()
    const embeddedCollection = vm.api.get('/camps/1').activityTypes()

    // when
    vm.api.reload(embeddedCollection)

    // then
    expect(embeddedCollection._meta.self).toBeUndefined()
    await letNetworkRequestFinish()
    expect(vm.$store.state.api['/camps/1'].activityTypes).toMatchObject(campData.storeState)
    done()
  })

  it('deletes an URI from the store and reloads all entities referencing it', async done => {
    // given
    axiosMock.onGet('http://localhost/groups/99').replyOnce(200, multipleReferencesToUser)
    axiosMock.onGet('http://localhost/groups/99').reply(200, {
      id: 99,
      name: 'Pfadi Züri',
      _links: {
        self: {
          href: '/groups/99'
        }
      }
    })
    axiosMock.onGet('http://localhost/camps/123').reply(200, {
      id: 123,
      _links: {
        self: {
          href: '/camps/123'
        }
      }
    })
    axiosMock.onDelete('http://localhost/users/1').replyOnce(204)
    vm.api.get('/groups/99')
    await letNetworkRequestFinish()

    // when
    vm.api.del('/users/1')

    // then
    await letNetworkRequestFinish()
    expect(axiosMock.history.delete.length).toEqual(1)
    expect(axiosMock.history.get.length).toEqual(3)
    done()
  })

  it('deletes an object from the store and reloads all entities referencing it', async done => {
    // given
    axiosMock.onGet('http://localhost/groups/99').replyOnce(200, multipleReferencesToUser)
    axiosMock.onGet('http://localhost/groups/99').reply(200, {
      id: 99,
      name: 'Pfadi Züri',
      _links: {
        self: {
          href: '/groups/99'
        }
      }
    })
    axiosMock.onGet('http://localhost/camps/123').reply(200, {
      id: 123,
      _links: {
        self: {
          href: '/camps/123'
        }
      }
    })
    axiosMock.onDelete('http://localhost/users/1').replyOnce(204)
    vm.api.get('/groups/99')
    await letNetworkRequestFinish()
    const user = vm.api.get('/users/1')

    // when
    vm.api.del(user)

    // then
    await letNetworkRequestFinish()
    expect(axiosMock.history.delete.length).toEqual(1)
    expect(axiosMock.history.get.length).toEqual(3)
    done()
  })

  it('breaks circular dependencies when deleting an entity in the reference circle', async done => {
    // given
    axiosMock.onDelete('http://localhost/periods/1').replyOnce(204)
    axiosMock.onGet('http://localhost/periods/1').replyOnce(200, circularReference.serverResponse)
    axiosMock.onGet('http://localhost/periods/1').networkError()
    axiosMock.onGet('http://localhost/days/2').reply(404)
    const load = vm.api.get('/periods/1')._meta.load
    await letNetworkRequestFinish()
    const period = await load
    expect(vm.$store.state.api).toMatchObject(circularReference.storeState)

    // when
    vm.api.del(period)

    // then
    await letNetworkRequestFinish()
    expect(axiosMock.history.get.length).toBe(2)
    done()
  })

  it('breaks circular dependencies when deleting an entity outside the reference circle', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/3').replyOnce(200, circularReference.campServerResponse)
    axiosMock.onGet('http://localhost/periods/1').replyOnce(200, circularReference.serverResponse)
    axiosMock.onDelete('http://localhost/camps/3').replyOnce(204)
    axiosMock.onGet('http://localhost/periods/1').replyOnce(200, circularReference.serverResponse)
    axiosMock.onGet('http://localhost/periods/1').networkError()
    axiosMock.onGet('http://localhost/days/2').reply(404)
    const load = vm.api.get('/camps/3')._meta.load
    const period = vm.api.get('/periods/1')._meta.load
    await letNetworkRequestFinish()
    const camp = await load
    expect(vm.$store.state.api).toMatchObject(circularReference.storeState)

    // when
    vm.api.del(camp)

    // then
    await letNetworkRequestFinish()
    expect(axiosMock.history.get.length).toBe(3)
    await period
    done()
  })

  it('automatically filters deleting items from standalone collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1/activities').reply(200, collectionFirstPage.serverResponse)
    axiosMock.onDelete('http://localhost/activities/2394').replyOnce(204)
    vm.api.get('/camps/1/activities')
    await letNetworkRequestFinish()
    const activity = vm.api.get('/activities/2394')

    // when
    vm.api.del(activity)

    // then
    expect(vm.api.get('/activities/2394')._meta.deleting).toBeTruthy()
    const standaloneCollection = vm.api.get('/camps/1/activities')
    expect(standaloneCollection.items.some(item => item._meta.deleting)).toBeFalsy()
    expect(standaloneCollection.items.length).toEqual(1)
    expect(standaloneCollection.allItems.some(item => item._meta.deleting)).toBeTruthy()
    expect(standaloneCollection.allItems.length).toEqual(2)
    done()
  })

  it('automatically filters deleting items from embedded collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedCollection.serverResponse)
    axiosMock.onDelete('http://localhost/periods/104').replyOnce(204)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    const period = vm.api.get('/periods/104')

    // when
    vm.api.del(period)

    // then
    expect(vm.api.get('/periods/104')._meta.deleting).toBeTruthy()
    const embCollection = vm.api.get('/camps/1').periods()
    expect(embCollection.items.some(item => item._meta.deleting)).toBeFalsy()
    expect(embCollection.items.length).toEqual(1)
    expect(embCollection.allItems.some(item => item._meta.deleting)).toBeTruthy()
    expect(embCollection.allItems.length).toEqual(2)
    done()
  })

  it('patches entity and stores the response into the store', async done => {
    // given
    const after = {
      _embedded: {
        campType: {
          id: 20,
          name: 'course',
          js: false,
          targetGroup: 'Kids',
          _links: {
            self: {
              href: '/campTypes/20'
            }
          }
        }
      },
      id: 2,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    }
    axiosMock.onPatch('http://localhost/camps/1').reply(200, after)

    // when
    const load = vm.api.patch('/camps/1', { some: 'thing' })

    // then
    expect(vm.$store.state.api['/camps/1']._meta.loading).toBe(true)
    await letNetworkRequestFinish()
    expect(await load).toMatchObject({ id: 2, _meta: { self: 'http://localhost/camps/1' } })
    expect(vm.api.get('/camps/1')).toMatchObject({ id: 2, _meta: { self: 'http://localhost/camps/1' } })
    expect(vm.api.get('/campTypes/20')).toMatchObject({ id: 20, name: 'course', js: false, targetGroup: 'Kids', _meta: { self: 'http://localhost/campTypes/20' } })
    done()
  })

  it('still returns old instance from store while patch is in progress', async done => {
    // given
    const before = {
      _embedded: {
        campType: {
          id: 20,
          name: 'camp',
          js: true,
          targetGroup: 'Kids',
          _links: {
            self: {
              href: '/campTypes/20'
            }
          }
        }
      },
      id: 1,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    }
    const after = {
      _embedded: {
        campType: {
          id: 20,
          name: 'course',
          js: false,
          targetGroup: 'Kids',
          _links: {
            self: {
              href: '/campTypes/20'
            }
          }
        }
      },
      id: 2,
      _links: {
        self: {
          href: '/camps/1'
        }
      }
    }
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, before)
    axiosMock.onGet('http://localhost/camps/1').reply(200, after)
    axiosMock.onPatch('http://localhost/camps/1').reply(200, after)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()

    // when
    const load = vm.api.patch('/camps/1', { some: 'thing' })

    // then
    expect(vm.$store.state.api['/camps/1']).toMatchObject({ id: 1, _meta: { self: '/camps/1' } })
    expect(vm.$store.state.api['/campTypes/20']).toMatchObject({ id: 20, name: 'camp', js: true, targetGroup: 'Kids', _meta: { self: '/campTypes/20' } })
    await letNetworkRequestFinish()
    expect(await load).toMatchObject({ id: 2, _meta: { self: 'http://localhost/camps/1' } })
    expect(vm.api.get('/camps/1')).toMatchObject({ id: 2, _meta: { self: 'http://localhost/camps/1' } })
    expect(vm.api.get('/campTypes/20')).toMatchObject({ id: 20, name: 'course', js: false, targetGroup: 'Kids', _meta: { self: 'http://localhost/campTypes/20' } })
    done()
  })

  it('posts entity and stores the response into the store', async done => {
    // given
    axiosMock.onPost('http://localhost/camps').reply(200, embeddedSingleEntity.serverResponse)

    // when
    const load = vm.api.post('/camps', { some: 'thing' })

    // then
    await letNetworkRequestFinish()
    expect(await load).toMatchObject({ id: 1, _meta: { self: 'http://localhost/camps/1' } })
    expect(vm.api.get('/camps/1')).toMatchObject({ id: 1, _meta: { self: 'http://localhost/camps/1' } })
    expect(vm.api.get('/campTypes/20')).toMatchObject({ id: 20, name: 'camp', js: true, targetGroup: 'Kids', _meta: { self: 'http://localhost/campTypes/20' } })
    done()
  })

  it('gets the href of a linked entity without fetching the entity itself', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, linkedSingleEntity.serverResponse)
    axiosMock.onGet('http://localhost/users/83').networkError()

    // when
    const load = vm.api.href('/camps/1', 'main_leader')

    // then
    await letNetworkRequestFinish()
    expect(await load).toEqual('http://localhost/users/83')
    done()
  })

  it('gets the href of a templated linked entity without fetching the entity itself', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, templatedLink.linkingServerResponse)
    axiosMock.onGet('http://localhost/users/83').networkError()

    // when
    const load = vm.api.href('/camps/1', 'users', { id: 83 })

    // then
    await letNetworkRequestFinish()
    expect(await load).toEqual('http://localhost/camps/1/users/83')
    expect(vm.$store.state.api).toMatchObject(templatedLink.storeStateBeforeLinkedLoaded)
    done()
  })

  it('imports templated link to single entity when linking entity is still loading', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, templatedLink.linkingServerResponse)
    axiosMock.onGet('http://localhost/camps/1/users/83').reply(200, templatedLink.linkedServerResponse)
    const loadingCamp = vm.api.get('/camps/1')

    // when
    const load = loadingCamp.users({ id: 83 })._meta.load

    // then
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(templatedLink.storeStateAfterLinkedLoaded)
    expect(await load).toMatchObject({ id: 83, name: 'Pflock', _meta: { self: 'http://localhost/camps/1/users/83' } })
    done()
  })

  it('imports templated link to single entity when linking entity is already loaded', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, templatedLink.linkingServerResponse)
    axiosMock.onGet('http://localhost/camps/1/users/83').reply(200, templatedLink.linkedServerResponse)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    const camp = vm.api.get('/camps/1')

    // when
    const load = camp.users({ id: 83 })._meta.load

    // then
    expect(vm.$store.state.api).toMatchObject(templatedLink.storeStateBeforeLinkedLoaded)
    expect(vm.$store.state.api).not.toMatchObject(templatedLink.storeStateAfterLinkedLoaded)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toMatchObject(templatedLink.storeStateAfterLinkedLoaded)
    expect(await load).toMatchObject({ id: 83, name: 'Pflock', _meta: { self: 'http://localhost/camps/1/users/83' } })
    done()
  })

  it('sets property loading on LoadingProxy to true', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    const loadingProxy = vm.api.get('/camps/1')
    expect(loadingProxy.loading).toBe(true)
    done()
  })

  it('returns error when `get` encounters network error', () => {
    // given
    axiosMock.onGet('http://localhost/camps/1').networkError()
    // when
    const load = vm.api.get('/camps/1')._meta.load
    // then
    return expect(load).rejects.toThrow('Could not connect to server')
  })

  it('returns error when `get` encounters network timeout', () => {
    // given
    axiosMock.onGet('http://localhost/camps/1').timeoutOnce()
    // when
    const load = vm.api.get('/camps/1')._meta.load
    // then
    return expect(load).rejects.toThrow('Could not connect to server')
  })

  it('returns error when `get` encounters 404 Not Found', async () => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, { id: 1, _links: { self: { href: '/camps/1' } } })
    axiosMock.onGet('http://localhost/camps/1').replyOnce(404)

    // when
    await vm.api.get('/camps/1')._meta.load
    // then
    expect(vm.$store.state.api['/camps/1']).toMatchObject({ id: 1, _meta: { self: '/camps/1' } })

    // when
    const load = vm.api.reload('/camps/1')
    // then
    await expect(load).rejects.toThrow('"/camps/1" has been deleted')
    expect(vm.$store.state.api['/camps/1']).toBeUndefined()
  })

  it('returns error when `get` encounters 403 Forbidden', () => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(403)
    // when
    const load = vm.api.get('/camps/1')._meta.load
    // then
    return expect(load).rejects.toThrow('No permission')
  })

  it('returns error when `patch` encounters network error', () => {
    // given
    axiosMock.onPatch('http://localhost/camps/1').networkError()
    // when
    const load = vm.api.patch('/camps/1', {})
    // then
    return expect(load).rejects.toThrow('Could not connect to server')
  })

  it('returns error when `patch` encounters network timeout', () => {
    // given
    axiosMock.onPatch('http://localhost/camps/1').timeoutOnce()
    // when
    const load = vm.api.patch('/camps/1', {})
    // then
    return expect(load).rejects.toThrow('Could not connect to server')
  })

  it('returns error when `patch` encounters 404 Not Found', async () => {
    // given
    axiosMock.onGet('http://localhost/camps/1').replyOnce(200, { id: 1, _links: { self: { href: '/camps/1' } } })
    axiosMock.onPatch('http://localhost/camps/1').replyOnce(404)

    // when
    await vm.api.get('/camps/1')._meta.load
    // then
    expect(vm.$store.state.api['/camps/1']).toMatchObject({ id: 1, _meta: { self: '/camps/1' } })

    // when
    const load = vm.api.patch('/camps/1', {})
    // then
    await expect(load).rejects.toThrow('"/camps/1" has been deleted')
    expect(vm.$store.state.api['/camps/1']).toBeUndefined()
  })

  it('returns error when `patch` encounters 403 Forbidden', () => {
    // given
    axiosMock.onPatch('http://localhost/camps/1').replyOnce(403)
    // when
    const load = vm.api.patch('/camps/1', {})
    // then
    return expect(load).rejects.toThrow('No permission')
  })

  it('returns error when `patch` encounters 422 Unprocessable Entity (Validation error)', () => {
    // given
    axiosMock.onPatch('http://localhost/camps/1').replyOnce(422, {
      validation_messages: { title: { stringLengthTooShort: 'The input is less than 10 characters long' } },
      type: 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
      title: 'Unprocessable Entity',
      status: 422,
      detail: 'Failed Validation'
    }, {
      'content-type': 'application/problem+json'
    })

    // when
    const load = vm.api.patch('/camps/1', {})

    // then
    return expect(load).rejects.toThrow('Failed Validation')
  })
})
