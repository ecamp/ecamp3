import { createLocalVue, mount } from '@vue/test-utils'
import store, { api } from '@/store'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import VueAxios from 'vue-axios'
import Vuex from 'vuex'
import embeddedSingleEntity from './server-responses/embedded-single-entity'
import storedEmbeddedSingleEntity from './server-responses/stored-embedded-single-entity'
import embeddedCollection from './server-responses/embedded-collection'
import storedEmbeddedCollection from './server-responses/stored-embedded-collection'

const flushPromises = () => new Promise(resolve => setTimeout(resolve))

describe('API store', () => {
  const localVue = createLocalVue()
  const axiosMock = new MockAdapter(axios)

  beforeAll(() => {
    localVue.use(Vuex)
    localVue.use(VueAxios, axiosMock)
    localVue.mixin({ api })
  })

  it('imports embedded single entity', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedSingleEntity)
    let wrapper = mount({ localVue, store, template: '<div></div>' })

    // when
    wrapper.vm.api('http://localhost/camps/1')

    // then
    expect(wrapper.vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(wrapper.vm.$store.state.api).toEqual(storedEmbeddedSingleEntity)
    done()
  })

  it('imports embedded collection', async done => {
    // given
    axiosMock.onGet('http://localhost/camps/1').reply(200, embeddedCollection)
    let wrapper = mount({ localVue, store, template: '<div></div>' })

    // when
    wrapper.vm.api('http://localhost/camps/1')

    // then
    expect(wrapper.vm.$store.state.api).toEqual({ '/camps/1': { '_loading': true, self: '/camps/1' } })
    await flushPromises()
    expect(wrapper.vm.$store.state.api).toEqual(storedEmbeddedCollection)
    done()
  })
})
