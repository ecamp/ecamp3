import { createLocalVue, mount } from '@vue/test-utils'
import store, { state, api } from '@/store'
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
    expect(vm.$store.state.api).toEqual(embeddedSingleEntity.storeState)
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
    expect(vm.$store.state.api).toEqual(embeddedCollection.storeState)
    done()
  })

  it('imports linked single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedSingleEntity.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(vm.$store.state.api).toEqual(linkedSingleEntity.storeState)
    done()
  })

  it('imports linked collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, linkedCollection.serverResponse)

    // when
    vm.api('/camps/1')

    // then
    expect(vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(vm.$store.state.api).toEqual(linkedCollection.storeState)
    done()
  })
})
