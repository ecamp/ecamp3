import { createLocalVue, mount } from '@vue/test-utils'
import store, { api, state } from '@/store'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import VueAxios from 'vue-axios'
import Vuex from 'vuex'
import embeddedSingleEntity from './resources/embedded-single-entity'
import embeddedCollection from './resources/embedded-collection'
import linkedSingleEntity from './resources/linked-single-entity'
import linkedCollection from './resources/linked-collection'

const flushPromises = () => new Promise(resolve => setTimeout(resolve))

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
    expect(vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    expect(vm.api('/camps/1')).toMatchObject(embeddedSingleEntity.storeState['/camps/1'])
    expect(vm.api('/camps/1').campType()).toEqual(embeddedSingleEntity.storeState['/campTypes/20'])
    expect(vm.api('/campTypes/20')).toEqual(embeddedSingleEntity.storeState['/campTypes/20'])
    done()
  })

  it('imports embedded collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedCollection.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(vm.$store.state.api).toMatchObject(embeddedCollection.storeState)
    expect(vm.api('/camps/1')).toMatchObject(embeddedCollection.storeState['/camps/1'])
    expect(vm.api('/camps/1').periods().items[0]()).toMatchObject(embeddedCollection.storeState['/periods/104'])
    expect(vm.api('/camps/1').periods().items[1]()).toMatchObject(embeddedCollection.storeState['/periods/128'])
    expect(vm.api('/periods/104')).toMatchObject(embeddedCollection.storeState['/periods/104'])
    expect(vm.api('/periods/104').camp()).toMatchObject(embeddedCollection.storeState['/camps/1'])
    expect(vm.api('/periods/128')).toMatchObject(embeddedCollection.storeState['/periods/128'])
    expect(vm.api('/periods/128').camp()).toMatchObject(embeddedCollection.storeState['/camps/1'])
    done()
  })

  it('imports linked single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedSingleEntity.serverResponse)
    const mainLeader = {
      serverResponse: { 'name': 'Smiley', '_links': { 'self': { 'href': '/users/83' } } },
      storeState: { 'name': 'Smiley', 'self': '/users/83' }
    }
    axiosMock.onGet('http://localhost/users/83').reply(200, mainLeader.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(vm.$store.state.api).toMatchObject(linkedSingleEntity.storeState)
    expect(vm.api('/camps/1')).toMatchObject(linkedSingleEntity.storeState['/camps/1'])
    expect(vm.api('/camps/1').mainLeader()).toEqual({ '_loading': true, self: '/users/83' })
    await flushPromises()
    expect(vm.api('/camps/1').mainLeader()).toEqual(mainLeader.storeState)
    expect(vm.api('/users/83')).toEqual(mainLeader.storeState)
    done()
  })

  it('imports linked collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedCollection.serverResponse)
    const events = {
      serverResponse: { '_embedded': { 'items': [
        { 'title': 'LS Volleyball', '_links': { 'self': { 'href': '/events/1234' } } },
        { 'title': 'LA Blachen', '_links': { 'self': { 'href': '/events/1236' } } }
      ] },
      '_links': { 'self': { 'href': '/camps/1/events' }, 'first': { 'href': '/camps/1/events' } } },
      storeState: { 'items': [
        null, // accessor functions will be replaced with null to compare them in Jest
        null
      ],
      'self': '/camps/1/events' }
    }
    axiosMock.onGet('http://localhost/camps/1/events').reply(200, events.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(vm.$store.state.api).toMatchObject(linkedCollection.storeState)
    expect(vm.api('/camps/1').events()).toEqual({ '_loading': true, self: '/camps/1/events' })
    await flushPromises()
    expect(vm.api('/camps/1').events()).toEqual(events.storeState)
    expect(vm.api('/camps/1/events')).toEqual(events.storeState)
    // TODO vm.api('...').events(0)
    done()
  })
})
