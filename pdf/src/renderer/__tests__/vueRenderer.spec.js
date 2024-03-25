import { afterEach, beforeEach, expect, it, describe } from 'vitest'
import wrap from './minimalHalJsonVuex.js'
import fullCampStoreContent from './fullCampStoreContent.json'
import activityWithSingleText from './activityWithSingleText.json'
import { renderVueToPdfStructure } from '../vueRenderer.js'
import SimpleDocument from './SimpleDocument.vue'
import CampPrint from '../../CampPrint.vue'
import { createCircularReplacer } from '@/renderer/__tests__/createCircularReplacer'
import { cloneDeep } from 'lodash'
import dayjs from '../../../common/helpers/dayjs.js'
import enCommon from '../../../common/locales/en.json'
import {
  compileToFunction,
  createCoreContext,
  fallbackWithLocaleChain,
  resolveValue,
  translate,
} from '@intlify/core'

const context = createCoreContext({
  locale: 'en',
  fallbackLocale: 'en',
  messages: { en: enCommon },
  messageResolver: resolveValue,
  messageCompiler: compileToFunction,
  localeFallbacker: fallbackWithLocaleChain,
})

const tcMock = (...args) => {
  return translate(context, ...args)
}

it('renders a simple Vue component', () => {
  // given

  // when
  const result = renderVueToPdfStructure(SimpleDocument)

  // then
  expect(result).toMatchFileSnapshot(
    './__snapshots__/simple_Vue_component.spec.json.snap'
  )
})

describe('rendering a full camp', () => {
  it('renders the cover page', async () => {
    // given
    const store = wrap(fullCampStoreContent)

    // when
    const result = renderVueToPdfStructure(CampPrint, {
      store,
      $tc: tcMock,
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
    expect(result).toMatchFileSnapshot('./__snapshots__/cover_page.spec.json.snap')
  })

  describe.each(['UTC', 'Europe/Zurich', 'Pacific/Tongatapu'])(
    'in timezone %s',
    (timezone) => {
      afterEach(() => {
        dayjs.tz.setDefault()
      })
      beforeEach(() => {
        dayjs.tz.setDefault(timezone)
      })

      it('renders the picasso', async () => {
        // given
        const store = wrap(fullCampStoreContent)

        // when
        const result = renderVueToPdfStructure(CampPrint, {
          store,
          $tc: tcMock,
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
        expect(result).toMatchFileSnapshot('./__snapshots__/picasso.spec.json.snap')
      })
    }
  )

  it('renders the story overview', async () => {
    // given
    const store = wrap(fullCampStoreContent)

    // when
    const result = renderVueToPdfStructure(CampPrint, {
      store,
      $tc: tcMock,
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
    expect(result).toMatchFileSnapshot('./__snapshots__/story_overview.spec.json.snap')
  })

  it('renders the program', async () => {
    // given
    const store = wrap(fullCampStoreContent)

    // when
    const result = renderVueToPdfStructure(CampPrint, {
      store,
      $tc: tcMock,
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
    expect(result).toMatchFileSnapshot('./__snapshots__/program.spec.json.snap')
  })

  it('renders a single activity', async () => {
    // given
    const store = wrap(fullCampStoreContent)

    // when
    const result = renderVueToPdfStructure(CampPrint, {
      store,
      $tc: tcMock,
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
    expect(result).toMatchFileSnapshot('./__snapshots__/single_activity.spec.json.snap')
  })

  it('renders the table of contents', async () => {
    // given
    const store = wrap(fullCampStoreContent)

    // when
    const result = renderVueToPdfStructure(CampPrint, {
      store,
      $tc: tcMock,
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
    expect(result).toMatchFileSnapshot('./__snapshots__/table_of_contents.spec.json.snap')
  })
})

describe('renders a single activity', () => {
  const campIri = activityWithSingleText['/camps/3c79b99ab424']._meta.self
  const activityIri = activityWithSingleText['/activities/63ad6efc7613']._meta.self
  const scheduleEntryIri =
    activityWithSingleText['/schedule_entries/51c110ddd923']._meta.self

  const thisTextShouldAppear = 'this text should appear'
  it.each([
    {
      name: 'with_empty_lists',
      text: `<p>another text</p>
             <ul>
                <li></li>
             </ul>
             <ul></ul>
             <ul/>
             <p>${thisTextShouldAppear}</p>`,
      textExpectedInOutput: thisTextShouldAppear,
    },
  ])(`with special text %j`, ({ name, text, textExpectedInOutput }) => {
    // given
    const storeWithSingleActivity = cloneDeep(activityWithSingleText)
    storeWithSingleActivity['/content_node/single_texts/4300e3355d22'].data.html = text

    const store = wrap(storeWithSingleActivity)

    // when
    const result = renderVueToPdfStructure(CampPrint, {
      store,
      $tc: tcMock,
      locale: 'de',
      config: {
        language: 'de',
        documentName: 'Morgenturnen.pdf',
        camp: store.get(campIri),
        contents: [
          {
            type: 'Activity',
            options: {
              activity: activityIri,
              scheduleEntry: scheduleEntryIri,
            },
          },
        ],
      },
    })

    // then
    expect(result).toMatchFileSnapshot(
      `./__snapshots__/single_activity_with_special_text_${name}.spec.json.snap`
    )

    expect(JSON.stringify(result, createCircularReplacer())).toMatch(textExpectedInOutput)
  })
})
