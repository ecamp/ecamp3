import { describe, expect, test } from 'vitest'
import repairConfig from '../repairPrintConfig.js'
import PicassoConfig from '../config/PicassoConfig.vue'
import ActivityConfig from '../config/ActivityConfig.vue'
import CoverConfig from '../config/CoverConfig.vue'
import ProgramConfig from '../config/ProgramConfig.vue'
import StoryConfig from '../config/StoryConfig.vue'
import SafetyConsiderationsConfig from '../config/SafetyConsiderationsConfig.vue'
import TocConfig from '../config/TocConfig.vue'
import ActivityListConfig from '../config/ActivityListConfig.vue'

describe('repairConfig', () => {
  const camp = {
    _meta: { self: '/camps/1a2b3c4d' },
    shortTitle: 'test camp',
    periods: () => ({
      _meta: { loading: false },
      items: [
        {
          _meta: { self: '/periods/1a2b3c4d' },
          days: () => ({
            _meta: { loading: false },
            items: [
              {
                _meta: { self: '/days/1a2b3c4d' },
              },
            ],
          }),
        },
      ],
    }),
    categories: () => ({
      _meta: { loading: false },
      items: [
        {
          _meta: { self: '/categories/1a2b3c4d' },
        },
      ],
    }),
    campCollaborations: () => ({
      _meta: { loading: false },
      items: [
        {
          _meta: { self: '/camp_collaborations/1a2b3c4d' },
        },
      ],
    }),
    progressLabels: () => ({
      _meta: { loading: false },
      items: [
        {
          _meta: { self: '/progress_labels/1a2b3c4d' },
        },
      ],
    }),
  }
  const multiPeriodCamp = {
    ...camp,
    periods: () => ({
      _meta: { loading: false },
      items: [
        {
          _meta: { self: '/periods/1a2b3c4d' },
          days: () => ({
            _meta: { loading: false },
            items: [
              {
                _meta: { self: '/days/1a2b3c4d' },
              },
            ],
          }),
        },
        {
          _meta: { self: '/periods/11223344' },
          days: () => ({
            _meta: { loading: false },
            items: [
              {
                _meta: { self: '/days/bbbbbbbb' },
              },
            ],
          }),
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
      ActivityListConfig,
    ].map((component) => [component.name.replace(/Config$/, ''), component.repairConfig])
  )
  const defaultFilter = {
    period: null,
    day: [],
    category: [],
    progressLabel: [],
    responsible: [],
    activityCount: 0,
  }
  const defaultContents = [
    {
      type: 'Picasso',
      options: {
        periods: ['/periods/1a2b3c4d'],
        orientation: 'L',
        filter: defaultFilter,
      },
    },
  ]
  const defaultOptions = {
    pageNumbers: false,
    pageSize: 'A4',
  }
  const args = [camp, availableLocales, 'en', componentRepairers, defaultContents]
  const multiPeriodArgs = [
    multiPeriodCamp,
    availableLocales,
    'en',
    componentRepairers,
    defaultContents,
  ]

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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'foobar',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'foobar',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: '',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'en-GB',
    })
  })

  test('adds missing options', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'de-CH-scout',
    })
  })

  test('adds missing pageNumbers option', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageSize: 'A4',
      },
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'de-CH-scout',
    })
  })

  test('allows enabling pageNumbers', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: true,
        pageSize: 'A4',
      },
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: true,
        pageSize: 'A4',
      },
      language: 'de-CH-scout',
    })
  })

  test('allows disabling pageNumbers', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: false,
        pageSize: 'A4',
      },
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: false,
        pageSize: 'A4',
      },
      language: 'de-CH-scout',
    })
  })

  test('adds missing pageSize option', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: false,
      },
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'de-CH-scout',
    })
  })

  test.each(['A5', 'A4', 'A3'])('allows pageSize %p', async (pageSize) => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: false,
        pageSize,
      },
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: false,
        pageSize,
      },
      language: 'de-CH-scout',
    })
  })

  test('repairs invalid pageSize', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: {
        pageNumbers: false,
        pageSize: 'tiny',
      },
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'de-CH-scout',
    })
  })

  test('overwrites camp URI', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d?something',
      contents: [
        {
          type: 'Picasso',
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'en-GB',
    })
  })

  test('overwrites invalid contents with default', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: {},
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'en-GB',
    })
  })

  test('overwrites null contents with default', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: null,
      documentName: 'test camp',
      options: defaultOptions,
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
          options: {
            periods: ['/periods/1a2b3c4d'],
            orientation: 'L',
            filter: defaultFilter,
          },
        },
      ],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'en-GB',
    })
  })

  test('leaves empty content alone', async () => {
    // given
    const config = {
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      options: defaultOptions,
      language: 'en-GB',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      options: defaultOptions,
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
      options: defaultOptions,
      language: 'en-GB',
    }

    // when
    const result = repairConfig(config, ...args)

    // then
    expect(result).toEqual({
      camp: '/camps/1a2b3c4d',
      contents: [],
      documentName: 'test camp',
      options: defaultOptions,
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
        options: defaultOptions,
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
        options: defaultOptions,
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
        options: defaultOptions,
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
        options: defaultOptions,
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
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'L',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'L',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'L',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'P',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'P',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: { periods: [], orientation: 'L', filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...multiPeriodArgs)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: [], orientation: 'L', filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    test('does not allow empty periods if there is only one period in the camp', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Picasso',
            options: { periods: [], orientation: 'L', filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'L',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'hello',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'L',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: ['/periods/1a2b3c4d'],
              orientation: 'L',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: [],
              dayOverview: true,
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...multiPeriodArgs)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: [],
              dayOverview: true,
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    test('does not allow empty periods if there is only one period in the camp', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Program',
            options: {
              periods: ['/periods/1a2b3c4d'],
              dayOverview: true,
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    describe('filter', () => {
      test('leaves valid filter as is', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds default filter if missing', () => {
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
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds period if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid period', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: '/periods/00000000',
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds responsible filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid responsible', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: ['/camp_collaborations/00000000'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds category filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid category', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: [],
                  category: ['/categories/00000000'],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds day filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  category: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid day', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: ['/days/00000000'],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds progressLabel filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid progressLabel', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: ['/progress_labels/00000000'],
                  responsible: [],
                  activityCount: 0,
                },
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: { pageNumbers: false, pageSize: 'A4' },
          language: 'en-GB',
        })
      })

      test('adds dummy activityCount if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Program',
              options: {
                periods: ['/periods/1a2b3c4d'],
                dayOverview: false,
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
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
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: { periods: [], contentType: 'Storycontext', filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...multiPeriodArgs)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: { periods: [], contentType: 'Storycontext', filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    test('does not allow empty periods if there is only one period in the camp', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'Story',
            options: {
              periods: ['/periods/1a2b3c4d'],
              contentType: 'Storycontext',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    describe('filter', () => {
      test('leaves valid filter as is', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds default filter if missing', () => {
        // given
        const config = {
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
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds period if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid period', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: '/periods/00000000',
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds responsible filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid responsible', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: ['/camp_collaborations/00000000'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds category filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid category', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: [],
                  category: ['/categories/00000000'],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds day filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  category: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid day', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: ['/days/00000000'],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds progressLabel filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid progressLabel', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: ['/progress_labels/00000000'],
                  responsible: [],
                  activityCount: 0,
                },
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: { pageNumbers: false, pageSize: 'A4' },
          language: 'en-GB',
        })
      })

      test('adds dummy activityCount if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'Story',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'Storycontext',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
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
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
            options: {
              periods: [],
              contentType: 'SafetyConsiderations',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...multiPeriodArgs)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: {
              periods: [],
              contentType: 'SafetyConsiderations',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    test('does not allow empty period when there is only one period in the camp', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'SafetyConsiderations',
            options: {
              periods: [],
              contentType: 'SafetyConsiderations',
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
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
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    describe('filter', () => {
      test('leaves valid filter as is', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds default filter if missing', () => {
        // given
        const config = {
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
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds period if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid period', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: '/periods/00000000',
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds responsible filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid responsible', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: ['/camp_collaborations/00000000'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds category filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid category', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: [],
                  category: ['/categories/00000000'],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds day filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  category: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid day', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: ['/days/00000000'],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds progressLabel filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid progressLabel', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: ['/progress_labels/00000000'],
                  responsible: [],
                  activityCount: 0,
                },
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: { pageNumbers: false, pageSize: 'A4' },
          language: 'en-GB',
        })
      })

      test('adds dummy activityCount if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'SafetyConsiderations',
              options: {
                periods: ['/periods/1a2b3c4d'],
                contentType: 'SafetyConsiderations',
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
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
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
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
        options: defaultOptions,
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
        options: defaultOptions,
        language: 'en-GB',
      })
    })
  })

  describe('activityList', () => {
    test('adds missing options', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
            options: { periods: ['/periods/1a2b3c4d'], filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    test('allows empty periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
            options: { periods: [], filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...multiPeriodArgs)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
            options: { periods: [], filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    test('does not allow empty period if there is only one period in the camp', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
            options: { periods: [], filter: defaultFilter },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
            options: {
              periods: ['/periods/1a2b3c4d'],
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    test('filters out unknown periods', async () => {
      // given
      const config = {
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
            options: {
              periods: ['/periods/11112222', '/periods/1a2b3c4d'],
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      }

      // when
      const result = repairConfig(config, ...args)

      // then
      expect(result).toEqual({
        camp: '/camps/1a2b3c4d',
        contents: [
          {
            type: 'ActivityList',
            options: {
              periods: ['/periods/1a2b3c4d'],
              filter: defaultFilter,
            },
          },
        ],
        documentName: 'test camp',
        options: defaultOptions,
        language: 'en-GB',
      })
    })

    describe('filter', () => {
      test('leaves valid filter as is', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: '/periods/1a2b3c4d',
                  day: ['/days/1a2b3c4d'],
                  category: ['/categories/1a2b3c4d'],
                  progressLabel: ['/progress_labels/1a2b3c4d'],
                  responsible: ['/camp_collaborations/1a2b3c4d'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds default filter if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds period if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid period', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: '/periods/00000000',
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds responsible filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid responsible', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: ['/camp_collaborations/00000000'],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds category filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid category', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: [],
                  category: ['/categories/00000000'],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds day filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  category: [],
                  responsible: [],
                  progressLabel: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid day', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: ['/days/00000000'],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('adds progressLabel filter when missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  responsible: [],
                  activityCount: 0,
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })

      test('removes invalid progressLabel', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: ['/progress_labels/00000000'],
                  responsible: [],
                  activityCount: 0,
                },
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
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: { pageNumbers: false, pageSize: 'A4' },
          language: 'en-GB',
        })
      })

      test('adds dummy activityCount if missing', () => {
        // given
        const config = {
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: {
                  period: null,
                  day: [],
                  category: [],
                  progressLabel: [],
                  responsible: [],
                },
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        }

        // when
        const result = repairConfig(config, ...args)

        // then
        expect(result).toEqual({
          camp: '/camps/1a2b3c4d',
          contents: [
            {
              type: 'ActivityList',
              options: {
                periods: ['/periods/1a2b3c4d'],
                filter: defaultFilter,
              },
            },
          ],
          documentName: 'test camp',
          options: defaultOptions,
          language: 'en-GB',
        })
      })
    })
  })
})
