import { createLocalVue, mount } from '@vue/test-utils'
import store, { api, state, sortQueryParams } from '@/store'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import VueAxios from 'vue-axios'
import Vuex from 'vuex'
import embeddedSingleEntity from './resources/embedded-single-entity'
import embeddedCollection from './resources/embedded-collection'
import linkedSingleEntity from './resources/linked-single-entity'
import linkedCollection from './resources/linked-collection'

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
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { '_loading': true, self: '/camps/1', loaded: {} } })
    await vm.$store.state.api['/camps/1'].loaded
    expect(vm.$store.state.api).toMatchObject(embeddedSingleEntity.storeState)
    expect(vm.api('/camps/1')).toMatchObject(embeddedSingleEntity.storeState['/camps/1'])
    expect(vm.api('/camps/1').campType()).toMatchObject(embeddedSingleEntity.storeState['/campTypes/20'])
    expect(vm.api('/campTypes/20')).toMatchObject(embeddedSingleEntity.storeState['/campTypes/20'])
    done()
  })

  it('imports embedded collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedCollection.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { '_loading': true, self: '/camps/1', loaded: {} } })
    await vm.$store.state.api['/camps/1'].loaded
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
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { '_loading': true, self: '/camps/1', loaded: {} } })
    await vm.$store.state.api['/camps/1'].loaded
    expect(vm.$store.state.api).toMatchObject(linkedSingleEntity.storeState)
    expect(vm.api('/camps/1')).toMatchObject(linkedSingleEntity.storeState['/camps/1'])
    expect(vm.api('/camps/1').mainLeader()).toMatchObject({ '_loading': true, self: '/users/83', loaded: {} })
    await vm.$store.state.api['/users/83'].loaded
    expect(vm.api('/camps/1').mainLeader()).toMatchObject(mainLeader.storeState)
    expect(vm.api('/users/83')).toMatchObject(mainLeader.storeState)
    done()
  })

  it('imports linked collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedCollection.serverResponse)
    const events = {
      serverResponse: {
        '_embedded': {
          'items': [
            { 'title': 'LS Volleyball', '_links': { 'self': { 'href': '/events/1234' } } },
            { 'title': 'LA Blachen', '_links': { 'self': { 'href': '/events/1236' } } }
          ]
        },
        '_links': { 'self': { 'href': '/camps/1/events' }, 'first': { 'href': '/camps/1/events' } },
        '_page': 0,
        '_per_page': 3,
        '_total': 2
      },
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
    expect(vm.$store.state.api).toMatchObject({ '/camps/1': { '_loading': true, self: '/camps/1', loaded: {} } })
    await vm.$store.state.api['/camps/1'].loaded
    expect(vm.$store.state.api).toMatchObject(linkedCollection.storeState)
    expect(vm.api('/camps/1').events()).toMatchObject({ '_loading': true, self: '/camps/1/events', loaded: {} })
    await vm.$store.state.api['/camps/1/events'].loaded
    setTimeout(() => {
      expect(JSON.parse(JSON.stringify(vm.api('/camps/1').events()))).toMatchObject(events.storeState)
      expect(JSON.parse(JSON.stringify(vm.api('/camps/1/events')))).toMatchObject(events.storeState)
      done()
    })
  })

  it('sorts query parameters', () => {
    // given
    let examples = {
      '': '',
      '/': '/',
      '/?': '/?',
      '?': '?',
      'http://localhost': 'http://localhost',
      'http://localhost/': 'http://localhost/',
      'https://scout.ch:3000': 'https://scout.ch:3000',
      'https://scout.ch:3000/': 'https://scout.ch:3000/',
      'http://localhost/?': 'http://localhost/?',
      '/camps/1': '/camps/1',
      '/camps/': '/camps/',
      '/camps': '/camps',
      '/camps/1?': '/camps/1?',
      '/camps/?page=0': '/camps/?page=0',
      '/camps/?page=0&abc=123': '/camps/?abc=123&page=0',
      '/camps?page=0&abc=123': '/camps?abc=123&page=0',
      '/camps?page=0&abc=123&page=1': '/camps?abc=123&page=0&page=1',
      '/camps?page=1&abc=123&page=0': '/camps?abc=123&page=1&page=0',
      '/camps?page=0&xyz=123&page=1': '/camps?page=0&page=1&xyz=123',
      '/camps/?e[]=abc&a[]=123&a=test': '/camps/?a=test&a%5B%5D=123&e%5B%5D=abc'
    }

    for (const [ example, expected ] of Object.entries(examples)) {
      // when
      let result = sortQueryParams(example)

      // then
      expect(result).toEqual(expected)
    }
  })
})
