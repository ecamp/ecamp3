import { describe, expect, test } from 'vitest'
import { mount as mountComponent } from '@vue/test-utils'
import EPasswordField from '../EPasswordField.vue'
import ETextField from '../ETextField.vue'
import { screen } from '@testing-library/vue'
import { setupVuetify } from '/tests/setupVuetify.js'

setupVuetify()

describe('An EPasswordField', () => {
  const mount = (options) => {
    const app = {
      components: { EPasswordField, ETextField },
      data: function () {
        return {
          data: null,
        }
      },
      template: `
        <div data-app>
          <e-password-field label="test" v-model="data">
            ${options?.children ?? ''}
          </e-password-field>
        </div>
      `,
    }
    return mountComponent(app, {
      attachTo: document.body,
      global: { mocks: { $t: (key) => key } },
      ...options,
    })
  }

  test('masks the input by default', async () => {
    const wrapper = mount()

    expect(wrapper.find('input[type=password]').exists()).toBe(true)
    expect(wrapper.find('input[type=text]').exists()).toBe(false)
  })

  test('reveals the password when the toggle button is clicked', async () => {
    const wrapper = mount()

    const toggle = wrapper.find('button[aria-label="global.button.showPassword"]')
    await toggle.trigger('click')

    expect(wrapper.find('input[type=text]').exists()).toBe(true)
    expect(wrapper.find('input[type=password]').exists()).toBe(false)
    expect(wrapper.find('button[aria-label="global.button.hidePassword"]').exists()).toBe(
      true
    )
  })

  test('masks again when the toggle button is clicked twice', async () => {
    const wrapper = mount()

    const showToggle = wrapper.find('button[aria-label="global.button.showPassword"]')
    await showToggle.trigger('click')
    const hideToggle = wrapper.find('button[aria-label="global.button.hidePassword"]')
    await hideToggle.trigger('click')

    expect(wrapper.find('input[type=password]').exists()).toBe(true)
    expect(wrapper.find('input[type=text]').exists()).toBe(false)
  })

  test('updates the value when vModel changes', async () => {
    const wrapper = mount()
    const input = wrapper.find('input[type=password]')

    await wrapper.setData({ data: 'MyPassword' })
    expect(input.element.value).toBe('MyPassword')
  })

  test('updates vModel when the input value changes', async () => {
    const wrapper = mount()
    const input = wrapper.find('input')

    input.element.value = 'secret123'
    await input.trigger('input')

    expect(wrapper.vm.data).toBe('secret123')
  })

  test('forwards consumer slots other than append-inner', async () => {
    mount({
      children: `
        <template #loader>
          <span>loader</span>
        </template>
      `,
    })

    expect(await screen.findByText('loader')).toBeVisible()
  })
})
