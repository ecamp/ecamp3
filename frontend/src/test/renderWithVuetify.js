import { render as testingLibraryRender } from '@testing-library/vue'
import i18n from '@/plugins/i18n/index.js'
import dayjs from '@/plugins/dayjs.js'
import Vue from 'vue'
import Vuetify from 'vuetify'
import VueI18n from '../plugins/i18n/index.js'
import formBaseComponents from '@/plugins/formBaseComponents'
import { Wrapper } from '@vue/test-utils'
import { localize } from 'vee-validate'

Vue.use(Vuetify)
Vue.use(formBaseComponents)
Vue.use(dayjs)

export const render = (component, options, callback) => {
  const root = document.createElement('div')
  root.setAttribute('data-app', 'true')

  return testingLibraryRender(
    component,
    {
      container: document.body.appendChild(root),
      vuetify: new Vuetify(),
      i18n,
      ...options,
    },
    callback
  )
}

/**
 * This function emulates the setLanguage mutation on the Vuex store,
 * for use in tests which need to set the application language.
 */
export const setTestLocale = (locale) => {
  VueI18n.locale = locale
  Vue.dayjs.locale(locale)
  localize('en')
}

/**
 * Wraps DOM nodes (such as the container object returned from a vue testing library render)
 * in a vue-test-utils wrapper object, so it is processed by
 */
export const snapshotOf = (testingLibraryContainer) => {
  if (!(testingLibraryContainer instanceof Element)) {
    throw new Error(
      'snapshotOf expects a DOM element as argument, but received ' +
        testingLibraryContainer.toString()
    )
  }
  return new Wrapper(testingLibraryContainer)
}
