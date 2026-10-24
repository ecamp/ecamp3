import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest'
import ApiTextarea from '@/components/form/api/ApiTextarea.vue'
import ApiWrapper from '@/components/form/api/ApiWrapper.vue'
import flushPromises from 'flush-promises'
import { merge } from 'lodash-es'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { i18n } from '@/plugins'
import { mount as mountComponent } from '@vue/test-utils'
import { waitForDebounce } from '@/test/util'
import { mockEventClass } from '@/test/mockEventClass'
import { setupVuetify } from '/tests/setupVuetify.js'

mockEventClass('ClipboardEvent')
mockEventClass('DragEvent')

setupVuetify()

describe('An ApiTextarea', () => {
  let wrapper
  let apiMock

  const path = 'test-field/123'
  const TEXT_1 = 'some text'
  const TEXT_2 = 'another text'

  beforeEach(() => {
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    vi.restoreAllMocks()
    wrapper?.unmount()
  })

  const mount = (options) => {
    const app = {
      components: { ApiTextarea },
      props: {
        path: { type: String, default: path },
      },
      template: `
        <div data-app>
          <api-textarea
            :auto-save="false"
            :path="path"
            uri="test-field/123"
            label="Test field"
            required="true"
          />
        </div>`,
    }
    apiMock.get().thenReturn(ApiMock.success(TEXT_1).forPath(path))
    const defaultOptions = {
      global: {
        mocks: {
          $t: (key) => key,
          api: apiMock.getMocks(),
        },
      },
    }
    return mountComponent(app, {
      i18n,
      attachTo: document.body,
      ...merge(defaultOptions, options),
    })
  }

  test('updates state if value in store is refreshed and has new value', async () => {
    wrapper = mount()
    apiMock.get().thenReturn(ApiMock.success(TEXT_2).forPath(path))

    wrapper.findComponent(ApiWrapper).vm.reload()

    await waitForDebounce()
    await flushPromises()

    expect(wrapper.findComponent(ApiWrapper).vm.localValue).toBe(TEXT_2)
  })
})
