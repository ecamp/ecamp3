import { campShortTitle } from '../campShortTitle.js'

describe('campShortTitle', () => {
  it.each([
    [{ shortTitle: null, title: 'Sommerlager Pfadistufe 2024' }, 'SoLa24 Pfadis'],
    [{ shortTitle: null, title: 'HeLa Wolfsstufe 2024' }, 'HeLa24 Wölfli'],
    [{ shortTitle: null, title: 'Bezirkspfingstlager Rover 2025' }, 'BezPfiLa25 Rover'],
    [{ shortTitle: null, title: 'PfiLa Pfadistufe 2024' }, 'PfiLa24 Pfadis'],
    [{ shortTitle: null, title: 'Pfingstlager 2024' }, 'PfiLa 2024'],
    [{ shortTitle: null, title: 'Pio SoLa 2024' }, 'Pio SoLa 2024'],
    [{ shortTitle: null, title: 'Herbstlager 2024' }, 'HeLa 2024'],
    [{ shortTitle: null, title: 'Auffahrtslager 2025 Pfadi Olymp' }, 'AufLa25PfadiOlym'],
    [{ shortTitle: null, title: 'Sola 2024 Jubla BuechBerg' }, 'SoLa24JublaBuech'],
    [{ shortTitle: null, title: 'Pfingstlager PTA Bern 2024' }, 'PfiLa24 PTA Bern'],
    [{ shortTitle: null, title: 'PfiLa 24 -Wolfsstufe Falkenstein' }, 'PfiLa24-WölfliFa'],
    [{ shortTitle: null, title: 'MoBe Klassenlager 2018 Scuol' }, 'MoBeKlaLa18Scuol'],
    [{ shortTitle: null, title: 'Cevilager Thun 2024' }, 'Cevilager24 Thun'],
    [{ shortTitle: null, title: 'Sommerlager Blauring 2024' }, 'SoLa24 Blauring'],
    [{ shortTitle: null, title: 'Sola24 Pfadistufe und Wölfli' }, 'SoLa24Pfadis&Wöl'],
    [{ shortTitle: null, title: 'Dracheburg Pfila 2024' }, 'DracheburgPfiLa2'],
    [{ shortTitle: null, title: 'Pio Bezirkspfila 2024' }, 'Pio BezPfiLa24'],
    [{ shortTitle: null, title: 'Piostufensola 12.12.2026' }, 'PioSoLa26 12.12.'],

    [{ shortTitle: 'Pfila 2024', title: 'Pfingstlager 2024' }, 'Pfila 2024'],
    [{ shortTitle: 'Pfila 2024', title: null }, 'Pfila 2024'],
    [{ shortTitle: null, title: null }, ''],
    [{ shortTitle: undefined, title: null }, ''],
    [{ shortTitle: '0', title: 'Sola24' }, '0'],
    [{ shortTitle: '', title: 'Sola24' }, 'SoLa24'],
    [null, ''],
    [undefined, ''],
  ])('maps "%s" to "%s"', (input, expected) => {
    expect(campShortTitle(input)).toEqual(expected)
  })
})
