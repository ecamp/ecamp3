const AUTOMATIC_SHORTNERS = [
  { regex: /So(?:mmer)?la(?:ger)?/gi, text: 'SoLa' },
  { regex: /Pfi(?:ngst)?la(?:ger)?/gi, text: 'PfiLa' },
  { regex: /He(?:rbst)?la(?:ger)?/gi, text: 'HeLa' },
]

const ADDITIONAL_SHORTNERS = [
  { regex: /Auslandssola/gi, text: 'AuLa' },
  { regex: /Auffahrtslager/gi, text: 'AufLa' },
  { regex: /Abteilungslager/gi, text: 'AbLa' },
  { regex: /Klassenlager/gi, text: 'KlaLa' },
  {
    regex: /(Bez)irks(So|Ab|Au|Auf|He|Pfi)La|(Ka)ntonslager|(Bu)ndeslager/gi,
    text: '$1$2$3$4La',
  },
  {
    regex:
      /(?<start>.*?\b)?(?<camp>[a-z]{2,}La(?:ger)?)(?<middle>(?:\s(?!19|20))|.*?)\s?(?:(?:19|20)(?<year>\d{2}))(?<end>.*)/gi,
    text: '$<start>$<camp>$<year>$<middle>$<end>',
  },
  { regex: /(Pfadi|Pio|Rover)stufe(?!n)/g, text: '$1s' },
  { regex: /(B)ieberstufe(?!n)/g, text: '$1ieberli' },
  { regex: /(W)olfs?stufe(?!n)/g, text: '$1ölfli' },
  {
    regex: /(Bieber|Wolfs|Pfadi|Pio|Rover)stufen(So|Ab|Au|Auf|He|Pfi)La/gi,
    text: '$1$2La',
  },
  { regex: /\bund\b/g, text: '&' },
  { regex: /\b(?:19|20)(\d{2})\b/g, text: '$1' },
  { regex: /\s/g, text: '' },
]

function campShortTitle(camp) {
  if (camp?.shortTitle != null && camp.shortTitle !== '') {
    return camp.shortTitle.substring(0, 16)
  }

  let title = camp?.title ?? ''

  for (const { regex, text } of AUTOMATIC_SHORTNERS) {
    title = title.replace(regex, text)
  }

  if (title.length <= 16) {
    return title
  }

  for (const { regex, text } of ADDITIONAL_SHORTNERS) {
    title = title.replace(regex, text)
    if (title.length <= 16) {
      return title
    }
  }
  return title.substring(0, 16)
}

export { campShortTitle }

export default campShortTitle
