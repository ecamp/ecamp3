import { createLocalVue, mount } from '@vue/test-utils'
import store, { api, state } from '@/store'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import VueAxios from 'vue-axios'
import Vuex from 'vuex'
import embeddedSingleEntity from './resources/embedded-single-entity'
import referenceToSingleEntity from './resources/reference-to-single-entity'
import embeddedCollection from './resources/embedded-collection'
import linkedSingleEntity from './resources/linked-single-entity'
import linkedCollection from './resources/linked-collection'
import collectionFirstPage from './resources/collection-firstPage'
import collectionPage1 from './resources/collection-page1'

async function letNetworkRequestFinish () {
  await new Promise(resolve => {
    setTimeout(() => resolve())
  })
}

describe('API store', () => {
  let localVue
  let axiosMock
  let vm
  const stateCopy = JSON.parse(JSON.stringify(state))

  beforeEach(() => {
    localVue = createLocalVue()
    localVue.use(Vuex)
    axiosMock = new MockAdapter(axios)
    localVue.use(VueAxios, axiosMock)
    localVue.mixin({ api })
    // Restore the initial state before each test
    store.replaceState(JSON.parse(JSON.stringify(stateCopy)))
    vm = mount({ localVue, store, template: '<div></div>' }).vm
  })

  it('imports embedded single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('/camps/1')
    expect(vm.api.get('/camps/1').camp_type()._meta.self).toEqual('/campTypes/20')
    expect(vm.api.get('/campTypes/20')._meta.self).toEqual('/campTypes/20')
    done()
  })

  it('imports reference to single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, referenceToSingleEntity.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(referenceToSingleEntity.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('/camps/1')
    done()
  })

  it('imports embedded collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedCollection.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedCollection.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('/camps/1')
    expect(vm.api.get('/camps/1').periods().items[0]._meta.self).toEqual('/periods/104')
    expect(vm.api.get('/camps/1').periods().items[1]._meta.self).toEqual('/periods/128')
    expect(vm.api.get('/periods/104')._meta.self).toEqual('/periods/104')
    expect(vm.api.get('/periods/104').camp()._meta.self).toEqual('/camps/1')
    expect(vm.api.get('/periods/128')._meta.self).toEqual('/periods/128')
    expect(vm.api.get('/periods/128').camp()._meta.self).toEqual('/camps/1')
    done()
  })

  it('imports linked single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedSingleEntity.serverResponse)
    const mainLeader = {
      serverResponse: { 'id': 83, 'name': 'Smiley', '_links': { 'self': { 'href': '/users/83' } } },
      storeState: { 'id': 83, 'name': 'Smiley', '_meta': { 'self': '/users/83' } }
    }
    axiosMock.onGet('http://localhost/users/83').reply(200, mainLeader.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(linkedSingleEntity.storeState)
    expect(vm.api.get('/camps/1')._meta.self).toEqual('/camps/1')
    // expect(vm.api.get('/camps/1').main_leader()._meta.self).toEqual('/users/83')
    expect(vm.api.get('/camps/1').main_leader()._meta.loading).toEqual(true)
    await letNetworkRequestFinish()
    expect(vm.api.get('/camps/1').main_leader()._meta).toEqual({ self: '/users/83' })
    expect(vm.api.get('/users/83')._meta.self).toEqual('/users/83')
    done()
  })

  it('imports linked collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedCollection.serverResponse)
    const events = {
      serverResponse: {
        '_embedded': {
          'items': [
            { 'id': 1234, 'title': 'LS Volleyball', '_links': { 'self': { 'href': '/events/1234' } } },
            { 'id': 1236, 'title': 'LA Blachen', '_links': { 'self': { 'href': '/events/1236' } } }
          ]
        },
        '_links': { 'self': { 'href': '/camps/1/events' }, 'first': { 'href': '/camps/1/events' } },
        '_page': 0,
        '_per_page': 3,
        '_total': 2,
        'page_count': 1
      },
      storeState: {
        'items': [
          {
            'href': '/events/1234'
          },
          {
            'href': '/events/1236'
          }
        ],
        'first': {
          'href': '/camps/1/events'
        },
        '_page': 0,
        '_per_page': 3,
        '_total': 2,
        'page_count': 1,
        '_meta': {
          'self': '/camps/1/events'
        }
      }
    }
    axiosMock.onGet('http://localhost/camps/1/events').reply(200, events.serverResponse)

    // when
    vm.api.get('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(linkedCollection.storeState)
    expect(vm.api.get('/camps/1').events().items).toEqual([])
    await letNetworkRequestFinish()
    expect(vm.$store.state.api['/camps/1/events']).toEqual(events.storeState)
    expect(vm.api.get('/camps/1').events().items.length).toEqual(2)
    expect(vm.api.get('/camps/1').events().items[0]._meta.self).toEqual('/events/1234')
    expect(vm.api.get('/camps/1').events().items[1]._meta.self).toEqual('/events/1236')
    done()
  })

  it('imports linked collection with multiple pages', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1/events').reply(200, collectionFirstPage.serverResponse)
    axiosMock.onGet('http://localhost/camps/1/events?page=1').reply(200, collectionPage1.serverResponse)

    // when
    vm.api.get('/camps/1/events')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1/events': { _meta: { self: '/camps/1/events', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(collectionFirstPage.storeState)
    expect(vm.api.get('/camps/1/events').items.length).toEqual(2)
    await letNetworkRequestFinish()
    expect(vm.api.get('/camps/1/events').items.length).toEqual(3)
    expect(vm.api.get('/camps/1/events').items[0]._meta.self).toEqual('/events/2394')
    expect(vm.api.get('/camps/1/events').items[1]._meta.self).toEqual('/events/2362')
    expect(vm.api.get('/camps/1/events').items[2]._meta.self).toEqual('/events/2402')
    done()
  })

  it('allows redundantly using get with an object', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)

    // when
    vm.api.get({ _meta: { self: '/camps/1' } })

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
    done()
  })

  it('allows using get with a loading object with known URI', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    let loadingObject = vm.api.get('/camps/1')

    // when
    vm.api.get(loadingObject)

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
    done()
  })

  it('allows using get with a loading object with unknown URI', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    let loadingObject = vm.api.get('/camps/1').camp_type()

    // when
    vm.api.get(loadingObject)

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
    done()
  })

  it('purges and later re-fetches a URI from the store', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    axiosMock.onGet('http://localhost/campTypes/20').reply(200, embeddedSingleEntity.serverResponse._embedded.camp_type)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    const storeStateWithoutCampType = JSON.parse(JSON.stringify(embeddedSingleEntity.storeState))
    delete storeStateWithoutCampType['/campTypes/20']

    // when
    vm.api.purge('/campTypes/20')

    // then
    expect(vm.$store.state.api).toEqual(storeStateWithoutCampType)
    expect(vm.api.get('/camps/1').camp_type()._meta.loading).toEqual(true)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
    done()
  })

  it('purges and later re-fetches an object from the store', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity.serverResponse)
    axiosMock.onGet('http://localhost/campTypes/20').reply(200, embeddedSingleEntity.serverResponse._embedded.camp_type)
    vm.api.get('/camps/1')
    await letNetworkRequestFinish()
    const campType = vm.api.get('/camps/1').camp_type()
    const storeStateWithoutCampType = JSON.parse(JSON.stringify(embeddedSingleEntity.storeState))
    delete storeStateWithoutCampType['/campTypes/20']

    // when
    vm.api.purge(campType)

    // then
    expect(vm.$store.state.api).toEqual(storeStateWithoutCampType)
    expect(vm.api.get('/camps/1').camp_type()._meta.loading).toEqual(true)
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
    done()
  })
})
