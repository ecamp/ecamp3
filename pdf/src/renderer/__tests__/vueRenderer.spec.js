import { expect, it, vi } from 'vitest'
import wrap from './minimalHalJsonVuex.js'
import fullCampStoreContent from './fullCampStoreContent.json'
import { renderVueToPdfStructure } from '../vueRenderer.js'
import SimpleDocument from './SimpleDocument.vue'
import CampPrint from '../../CampPrint.vue'

vi.mock('vuetify/es5/components/VCalendar/modes/column.js', () => ({
  column() {
    return function (_, dayEvents) {
      return dayEvents.map(() => ({
        left: 0,
        width: 100,
      }))
    }
  },
}))
vi.mock('vuetify/es5/components/VCalendar/util/events.js', () => ({
  parseEvent(event) {
    return event
  },
}))

it('renders a simple Vue component', () => {
  // given

  // when
  const result = renderVueToPdfStructure(SimpleDocument)

  // then
  expect(result).toMatchSnapshot()
})

it('renders a full camp', async () => {
  // given
  const store = wrap(fullCampStoreContent)

  // when
  const result = renderVueToPdfStructure(CampPrint, {
    store,
    $tc: (key) => key,
    locale: 'de',
    config: {
      language: 'de',
      documentName: 'Pfila 2023.pdf',
      camp: store.get('/camps/c4cca3a51342'),
      contents: [
        {
          type: 'Cover',
          options: {},
        },
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/16b2fcffdd8e'],
            orientation: 'L',
          },
        },
        {
          type: 'Story',
          options: {
            periods: ['/periods/16b2fcffdd8e'],
          },
        },
        {
          type: 'Program',
          options: {
            periods: ['/periods/16b2fcffdd8e'],
          },
        },
        {
          type: 'Toc',
          options: {},
        },
      ],
    },
  })

  // then
  expect(result).toMatchSnapshot()
})
