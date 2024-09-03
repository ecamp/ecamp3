import { describe, expect, it } from 'vitest'
import {
  halUriToId,
  idToHalUri,
  processRouteQuery,
  transformQueryParam,
  transformValuesToHalId,
} from '@/helpers/querySyncHelper'

describe('querySyncHelper', () => {
  describe('halUriToId', () => {
    it.each([
      ['/camp_collaborations/73558f9c6483', '73558f9c6483'],
      ['/camp_collaborations/fe6557a4b89f', 'fe6557a4b89f'],
      ['/categories/3a54634a4', '3a54634a4'],
      ['/activity_progress_labels/456sfs56sus', '456sfs56sus'],
      ['/periods/45sz56sz5z6', '45sz56sz5z6'],
      ['/categories/56sf5654t', '56sf5654t'],
      ['/periods/65klsdfg59', '65klsdfg59'],
      ['/categories/se456sfs6df6', 'se456sfs6df6'],
      ['/camp_collaborations/sd6fh45z5', 'sd6fh45z5'],
      ['/categories/6s567t7k7i9k', '6s567t7k7i9k'],
      ['/periods/d453atz67', 'd453atz67'],
      ['/camp_collaborations/9234hn45asd850345', '9234hn45asd850345'],
      ['/activity_progress_labels/54s6sdf6f', '54s6sdf6f'],
      ['/periods/548dt6456', '548dt6456'],
      ['/activity_progress_labels/345fgbthsdt4', '345fgbthsdt4'],
    ])('should transform %p to %p', (input, expected) => {
      const result = halUriToId(input)

      expect(result).toEqual(expected)
    })
  })
  describe('idToHalUri', () => {
    it.each([
      [['camp_collaborations', '73558f9c6483'], '/camp_collaborations/73558f9c6483'],
      [['camp_collaborations', 'fe6557a4b89f'], '/camp_collaborations/fe6557a4b89f'],
      [['categories', '3a54634a4'], '/categories/3a54634a4'],
      [
        ['activity_progress_labels', '456sfs56sus'],
        '/activity_progress_labels/456sfs56sus',
      ],
      [['periods', '45sz56sz5z6'], '/periods/45sz56sz5z6'],
      [['categories', '56sf5654t'], '/categories/56sf5654t'],
    ])('should transform %p to %p', (input, expected) => {
      const result = idToHalUri(...input)

      expect(result).toEqual(expected)
    })
  })
  describe('transformQueryParam', () => {
    it.each([
      [
        ['responsible', ['73558f9c6483', 'fe6557a4b89f'], 'camp_collaborations'],
        [
          'responsible',
          ['/camp_collaborations/73558f9c6483', '/camp_collaborations/fe6557a4b89f'],
        ],
      ],
      [
        ['period', '73558f9c6483', 'periods'],
        ['period', '/periods/73558f9c6483'],
      ],
      [
        ['category', ['73558f9c6483', 'fe6557a4b89f'], 'categories'],
        ['category', ['/categories/73558f9c6483', '/categories/fe6557a4b89f']],
      ],
      [
        ['progressLabels', ['73558f9c6483', 'fe6557a4b89f'], 'activity_progress_labels'],
        [
          'progressLabels',
          [
            '/activity_progress_labels/73558f9c6483',
            '/activity_progress_labels/fe6557a4b89f',
          ],
        ],
      ],
      [
        ['responsible', ['73558f9c6483', 'fe6557a4b89f'], 'camp_collaborations'],
        [
          'responsible',
          ['/camp_collaborations/73558f9c6483', '/camp_collaborations/fe6557a4b89f'],
        ],
      ],
      [
        ['category', ['73558f9c6483', 'fe6557a4b89f'], 'categories'],
        ['category', ['/categories/73558f9c6483', '/categories/fe6557a4b89f']],
      ],
      [
        ['progressLabels', ['73558f9c6483', 'fe6557a4b89f'], 'activity_progress_labels'],
        [
          'progressLabels',
          [
            '/activity_progress_labels/73558f9c6483',
            '/activity_progress_labels/fe6557a4b89f',
          ],
        ],
      ],
      [
        ['responsible', ['73558f9c6483', 'fe6557a4b89f'], 'camp_collaborations'],
        [
          'responsible',
          ['/camp_collaborations/73558f9c6483', '/camp_collaborations/fe6557a4b89f'],
        ],
      ],
      [
        ['period', 'fe6557a4b89f', 'periods'],
        ['period', '/periods/fe6557a4b89f'],
      ],
      [
        ['responsible', null, 'camp_collaborations'],
        ['responsible', null],
      ],
    ])('should transform %p to %p', (input, expected) => {
      const result = transformQueryParam(...input)

      expect(result).toEqual(expected)
    })
  })
  describe('processRouteQuery', () => {
    it.each([
      [
        {
          category: ['505e3fdf9e90', 'a47a60594096'],
        },
        {
          category: ['/categories/505e3fdf9e90', '/categories/a47a60594096'],
        },
      ],
      [
        {
          category: ['34az4a5a'],
        },
        {
          category: ['/categories/34az4a5a'],
        },
      ],
      [
        {
          progressLabel: ['34az4a5a'],
        },
        {
          progressLabel: ['/activity_progress_labels/34az4a5a'],
        },
      ],
      [
        {
          period: '34az4a5a',
        },
        {
          period: '/periods/34az4a5a',
        },
      ],
      [
        {
          category: ['505e3fdf9e90', 'a47a60594096'],
          responsible: ['3a5azag84', 'a5aaf4a5', '5asd5as5', 'ad45a45ag'],
        },
        {
          category: ['/categories/505e3fdf9e90', '/categories/a47a60594096'],
          responsible: [
            '/camp_collaborations/3a5azag84',
            '/camp_collaborations/a5aaf4a5',
            '/camp_collaborations/5asd5as5',
            '/camp_collaborations/ad45a45ag',
          ],
        },
      ],
    ])('should transform %p to %p', (input, expected) => {
      const result = processRouteQuery(input)

      expect(result).toEqual(expected)
    })
  })
  describe('transformValuesToHalId', () => {
    it.each([
      [
        {
          category: ['/categories/505e3fdf9e90', '/categories/a47a60594096'],
        },
        {
          category: ['505e3fdf9e90', 'a47a60594096'],
        },
      ],
      [
        {
          category: ['/categories/34az4a5a'],
        },
        {
          category: ['34az4a5a'],
        },
      ],
      [
        {
          progressLabel: ['/activity_progress_labels/34az4a5a'],
        },
        {
          progressLabel: ['34az4a5a'],
        },
      ],
      [
        {
          period: '/periods/34az4a5a',
        },
        {
          period: '34az4a5a',
        },
      ],
      [
        {
          category: ['/categories/505e3fdf9e90', '/categories/a47a60594096'],
          responsible: [
            '/camp_collaborations/3a5azag84',
            '/camp_collaborations/a5aaf4a5',
            '/camp_collaborations/5asd5as5',
            '/camp_collaborations/ad45a45ag',
          ],
        },
        {
          category: ['505e3fdf9e90', 'a47a60594096'],
          responsible: ['3a5azag84', 'a5aaf4a5', '5asd5as5', 'ad45a45ag'],
        },
      ],
    ])('should transform %p to %p', (input, expected) => {
      const result = transformValuesToHalId(input)

      expect(result).toEqual(expected)
    })
  })
})
