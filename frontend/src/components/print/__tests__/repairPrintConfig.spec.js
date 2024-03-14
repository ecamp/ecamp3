import repairConfig from '../repairPrintConfig.js'
import PicassoConfig from '../config/PicassoConfig.vue'
import ActivityConfig from '../config/ActivityConfig.vue'
import CoverConfig from '../config/CoverConfig.vue'
import ProgramConfig from '../config/ProgramConfig.vue'
import StoryConfig from '../config/StoryConfig.vue'
import TocConfig from '../config/TocConfig.vue'

describe('repairConfig', () => {
  const camp = {
    _meta: { self: '/camps/1a2b3c4d' },
    name: 'test camp',
    periods: () => ({
      items: [
        {
          _meta: { self: '/periods/1a2b3c4d' },
        },
      ],
    }),
  }
  const availableLocales = ['en-GB', 'de-CH', 'de-CH-scout']
  const componentRepairers = Object.fromEntries(
    [
      ActivityConfig,
      CoverConfig,
      PicassoConfig,
      ProgramConfig,
      StoryConfig,
      TocConfig,
    ].map((component) => [component.name.replace(/Config$/, ''), component.repairConfig])
  )
  const defaultContents = [
    { type: 'Picasso', options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' } },
  ]
  const args = [camp, availableLocales, componentRepairers, defaultContents]

  test('fills empty config with default data', async () => {
    // given
    const config = {}

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('leaves valid config alone', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('allows valid language', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'de-CH-scout',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'de-CH-scout',
    })
  })

  test('replaces invalid language', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'definitely-not-a-supported-language',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('leaves custom documentName alone', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'foobar',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'foobar',
      language: 'en',
    })
  })

  test('fills in missing documentName', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: '',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('overwrites camp URI', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d?something',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('overwrites invalid contents with default', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: {},
      documentName: 'test camp',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('overwrites null contents with default', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: null,
      documentName: 'test camp',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
        },
      ],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('leaves empty content alone', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      language: 'en',
    })
  })

  test('filters out unknown content', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'SomethingUnsupported',
          options: {},
        },
      ],
      documentName: 'test camp',
      language: 'en',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      language: 'en',
    })
  })

  describe('activity', () => {
    test('leaves config alone', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Activity',
            foo: 'bar',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Activity',
            foo: 'bar',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })
  })

  describe('cover', () => {
    test('leaves config alone', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Cover',
            foo: 'bar',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Cover',
            foo: 'bar',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })
  })

  describe('picasso', () => {
    test('adds missing options', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: [], orientation: 'L' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('allows landscape mode', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('allows portrait mode', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: ['/periods/1a2b3c4d'], orientation: 'P' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: ['/periods/1a2b3c4d'], orientation: 'P' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('allows empty periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: [], orientation: 'L' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: [], orientation: 'L' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('overwrites invalid orientation', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: ['/periods/1a2b3c4d'], orientation: 'hello' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('filters out unknown periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: {
              periods: ['/periods/11112222', '/periods/1a2b3c4d'],
              orientation: 'L',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })
  })

  describe('program', () => {
    test('adds missing options', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: [],
              dayOverview: true,
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('adds missing dayOverview flag', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: ['/periods/1a2b3c4d'],
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: ['/periods/1a2b3c4d'],
              dayOverview: true,
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('allows dayOverview false', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: ['/periods/1a2b3c4d'],
              dayOverview: false,
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: ['/periods/1a2b3c4d'],
              dayOverview: false,
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('allows empty periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: { periods: [], dayOverview: true },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: { periods: [], dayOverview: true },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('filters out unknown periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: ['/periods/11112222', '/periods/1a2b3c4d'],
              dayOverview: true,
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: ['/periods/1a2b3c4d'],
              dayOverview: true,
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })
  })

  describe('story', () => {
    test('adds missing options', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: [] },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('allows empty periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: [] },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: [] },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })

    test('filters out unknown periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: {
              periods: ['/periods/11112222', '/periods/1a2b3c4d'],
            },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: ['/periods/1a2b3c4d'] },
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })
  })

  describe('toc', () => {
    test('leaves config alone', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Toc',
            foo: 'bar',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Toc',
            foo: 'bar',
          },
        ],
        documentName: 'test camp',
        language: 'en',
      })
    })
  })
})
