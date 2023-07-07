import { expect, it, describe } from 'vitest'
import wrap from './minimalHalJsonVuex.js'
import fullCampStoreContent from './fullCampStoreContent.json'
import { renderVueToPdfStructure } from '../vueRenderer.js'
import SimpleDocument from './SimpleDocument.vue'
import CampPrint from '../../CampPrint.vue'

it('renders a simple Vue component', () => {
  // given

  // when
  const result = renderVueToPdfStructure(SimpleDocument)

  // then
  expect(result).toMatchSnapshot()
})

describe('rendering a full camp', () => {
  it('renders the cover page', async () => {
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
        ],
      },
    })

    // then
    expect(result).toMatchSnapshot()
  })

  it('renders the picasso', async () => {
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
            type: 'Picasso',
            options: {
              periods: ['/periods/16b2fcffdd8e'],
              orientation: 'L',
            },
          },
        ],
      },
    })

    // then
    expect(result).toMatchSnapshot()
  })

  it('renders the story overview', async () => {
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
            type: 'Story',
            options: {
              periods: ['/periods/16b2fcffdd8e'],
            },
          },
        ],
      },
    })

    // then
    expect(result).toMatchSnapshot()
  })

  it('renders the program', async () => {
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
            type: 'Program',
            options: {
              periods: ['/periods/16b2fcffdd8e'],
            },
          },
        ],
      },
    })

    // then
    expect(result).toMatchSnapshot()
  })

  it('renders a single activity', async () => {
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
            type: 'Activity',
            options: {
              activity: '/activities/7f33c504d878',
              scheduleEntry: '/schedule_entries/4bc1873a73f2',
            },
          },
        ],
      },
    })

    // then
    expect(result).toMatchSnapshot()
  })

  it('renders the table of contents', async () => {
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
            type: 'Toc',
            options: {},
          },
        ],
      },
    })

    // then
    expect(result).toMatchSnapshot()
  })
})
