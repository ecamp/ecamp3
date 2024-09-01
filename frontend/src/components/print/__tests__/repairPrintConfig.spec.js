import repairConfig from '../repairPrintConfig.js'
import PicassoConfig from '../config/PicassoConfig.vue'
import ActivityConfig from '../config/ActivityConfig.vue'
import CoverConfig from '../config/CoverConfig.vue'
import ProgramConfig from '../config/ProgramConfig.vue'
import StoryConfig from '../config/StoryConfig.vue'
import SafetyConsiderationsConfig from '../config/SafetyConsiderationsConfig.vue'
import TocConfig from '../config/TocConfig.vue'

describe('repairConfig', () => {
  const camp = {
    _meta: { self: '/camps/1a2b3c4d' },
    shortTitle: 'test camp',
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
      SafetyConsiderationsConfig,
      StoryConfig,
      TocConfig,
    ].map((component) => [component.name.replace(/Config$/, ''), component.repairConfig])
  )
  const defaultContents = [
    { type: 'Picasso', options: { periods: ['/periods/1a2b3c4d'], orientation: 'L' } },
  ]
  const args = [camp, availableLocales, 'en', componentRepairers, defaultContents]

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
      language: 'en-GB',
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
      language: 'en-GB',
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
      language: 'en-GB',
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

  test('replaces invalid language with fallback language', async () => {
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
    const result = repairConfig(
      config,
      camp,
      availableLocales,
      'de-CH-scout',
      componentRepairers,
      defaultContents
    )

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

  test('replaces invalid language with any valid language if fallback language is also invalid', async () => {
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
    const result = repairConfig(
      config,
      camp,
      availableLocales,
      'definitely-not-a-valid-language',
      componentRepairers,
      defaultContents
    )

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
      language: 'en-GB',
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
      language: 'en-GB',
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
      language: 'en-GB',
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
      language: 'en-GB',
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
      language: 'en-GB',
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
      language: 'en-GB',
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
      language: 'en-GB',
    })
  })

  test('overwrites invalid contents with default', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: {},
      documentName: 'test camp',
      language: 'en-GB',
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
      language: 'en-GB',
    })
  })

  test('overwrites null contents with default', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: null,
      documentName: 'test camp',
      language: 'en-GB',
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
      language: 'en-GB',
    })
  })

  test('leaves empty content alone', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      language: 'en-GB',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      language: 'en-GB',
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
      language: 'en-GB',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: [], contentType: 'Storycontext' },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      })
    })

    test('allows empty periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: [], contentType: 'Storycontext' },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: [], contentType: 'Storycontext' },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
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
              contentType: 'Storycontext',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: {
              periods: ['/periods/1a2b3c4d'],
              contentType: 'Storycontext',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      })
    })

    test('uses known contentType', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: {
              periods: ['/periods/1a2b3c4d'],
              contentType: 'Storyboard',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: {
              periods: ['/periods/1a2b3c4d'],
              contentType: 'Storycontext',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      })
    })
  })

  describe('safetyConsiderations', () => {
    test('adds missing options', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: { periods: [], contentType: 'SafetyConsiderations' },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      })
    })

    test('allows empty periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: { periods: [], contentType: 'SafetyConsiderations' },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: { periods: [], contentType: 'SafetyConsiderations' },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      })
    })

    test('filters out unknown periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: {
              periods: ['/periods/11112222', '/periods/1a2b3c4d'],
              contentType: 'SafetyConsiderations',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: {
              periods: ['/periods/1a2b3c4d'],
              contentType: 'SafetyConsiderations',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      })
    })

    test('uses known contentType', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: {
              periods: ['/periods/1a2b3c4d'],
              contentType: 'Storyboard',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: {
              periods: ['/periods/1a2b3c4d'],
              contentType: 'SafetyConsiderations',
            },
          },
        ],
        documentName: 'test camp',
        language: 'en-GB',
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
        language: 'en-GB',
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
        language: 'en-GB',
      })
    })
  })
})
