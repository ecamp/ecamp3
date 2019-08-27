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
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
    /*
    expect(vm.api('/camps/1')).toEqual(embeddedSingleEntity.storeState['/camps/1'])
    expect(vm.api('/camps/1').campType()).toEqual(embeddedSingleEntity.storeState['/campTypes/20'])
    expect(vm.api('/campTypes/20')).toEqual(embeddedSingleEntity.storeState['/campTypes/20'])
    */
    done()
  })

  it('imports reference to single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, referenceToSingleEntity.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(referenceToSingleEntity.storeState)
    /*
    expect(vm.api('/camps/1')).toEqual(referenceToSingleEntity.storeState['/camps/1'])
    */
    done()
  })

  it('imports embedded collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedCollection.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(embeddedCollection.storeState)
    /*
    expect(vm.api('/camps/1')).toEqual(embeddedCollection.storeState['/camps/1'])
    expect(vm.api('/camps/1').periods().items[0]()).toEqual(embeddedCollection.storeState['/periods/104'])
    expect(vm.api('/camps/1').periods().items[1]()).toEqual(embeddedCollection.storeState['/periods/128'])
    expect(vm.api('/periods/104')).toEqual(embeddedCollection.storeState['/periods/104'])
    expect(vm.api('/periods/104').camp()).toEqual(embeddedCollection.storeState['/camps/1'])
    expect(vm.api('/periods/128')).toEqual(embeddedCollection.storeState['/periods/128'])
    expect(vm.api('/periods/128').camp()).toEqual(embeddedCollection.storeState['/camps/1'])
    */
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
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(linkedSingleEntity.storeState)
    /*
    expect(vm.api('/camps/1')).toEqual(linkedSingleEntity.storeState['/camps/1'])
    expect(vm.api('/camps/1').mainLeader()).toEqual({ _meta: { self: '/users/83', loaded: {} } })
    await letNetworkRequestFinish()
    expect(vm.api('/camps/1').mainLeader()).toEqual(mainLeader.storeState)
    expect(vm.api('/users/83')).toEqual(mainLeader.storeState)
    */
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
        '_total': 2
      },
      storeState: {
        'items': [],
        '_meta': {
          'self': '/camps/1/events'
        }
      }
    }
    axiosMock.onGet('http://localhost/camps/1/events').reply(200, events.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { _meta: { self: '/camps/1', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(linkedCollection.storeState)
    /*
    expect(vm.api('/camps/1').events()).toEqual({ _meta: { self: '/camps/1/events', loaded: {} } })
    await letNetworkRequestFinish()
    expect(JSON.parse(JSON.stringify(vm.api('/camps/1').events()))).toEqual(events.storeState)
    expect(JSON.parse(JSON.stringify(vm.api('/camps/1/events')))).toEqual(events.storeState)
    */
    done()
  })

  it('imports linked collection with multiple pages', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1/events').reply(200, collectionFirstPage.serverResponse)
    axiosMock.onGet('http://localhost/camps/1/events?page=1').reply(200, collectionPage1.serverResponse)

    // when
    vm.api('/camps/1/events')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1/events': { _meta: { self: '/camps/1/events', loading: true } } })
    await letNetworkRequestFinish()
    expect(vm.$store.state.api).toEqual(collectionFirstPage.storeState)
    /*
    await vm.api('/camps/1/events').load()
    expect(JSON.parse(JSON.stringify(vm.api('/camps/1/events').items.length))).toEqual(3)
    expect(JSON.parse(JSON.stringify(vm.api('/camps/1/events').items[0]()))).toEqual(collectionFirstPage.storeState['/events/2394'])
    expect(JSON.parse(JSON.stringify(vm.api('/camps/1/events').items[1]()))).toEqual(collectionFirstPage.storeState['/events/2362'])
    expect(JSON.parse(JSON.stringify(vm.api('/camps/1/events').items[2]()))).toEqual(collectionPage1.storeState['/events/2402'])
    */
    done()
  })
})
