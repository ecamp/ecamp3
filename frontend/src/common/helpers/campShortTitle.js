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
    regex: /(Bez)irks?(So|Ab|Au|Auf|He|Pfi)La|(Ka)ntonslager|(Bu)ndeslager/gi,
    text: '$1$2$3$4La',
  },
  {
    regex:
      /(?<start>.*?\b)?(?<camp>(?:[A-Za-z]{2,}La)|(?:[A-Za-z]{2,}lager))(?<middle>(?:\s(?!19|20))|.*?)\s?(?:(?:19|20)(?<year>\d{2}))(?<end>.*)/g,
    text: '$<start>$<camp>$<year>$<middle>$<end>',
  },
  { regex: /(Pfadi|Pio|Rover)stufe(?!n)/g, text: '$1s' },
  { regex: /(B)ie?berstufe(?!n)/gi, text: '$1iberli' },
  { regex: /(W)olfs?stufe(?!n)/gi, text: '$1ölfli' },
  { regex: /(E)sploratori/gi, text: '$1splos' },
  {
    regex: /(Bie?ber|Wolfs?|Pfadi|Pio|Rover)stufen(So|Ab|Au|Auf|He|Pfi)La/gi,
    text: '$1$2La',
  },
  {
    regex: /Campo? (.*? ?)(?:(?: ?d[ei] )?(Pentec[ôo]te|Pentecoste|primavera|printemps|p[âa]ques|pasqua)|(?:d')?([ée]t[eé]|automne|hiver)|(estivo|autunnale|invernale)|(?:de[l ]l[' ])?(Ascensione?))? (.*? ?)*?((?:19|20)\d{2})?/gi,
    text: '$2$3$4$5 $1$6$7',
  },
  { regex: /\bPrintemps/gi, text: 'Prt', },
  { regex: /\bPrimavera/gi, text: 'Prim', },
  { regex: /\bP[aâ]ques/gi, text: 'Pâq', },
  { regex: /\bPasqua/gi, text: 'Pas', },
  { regex: /\bAutomne/gi, text: 'Aut', },
  { regex: /\bAutunno/gi, text: 'Aut', },
  { regex: /\bHiver/gi, text: 'Hiv', },
  { regex: /\bInverno/gi, text: 'Inv', },
  { regex: /\bPentec[oô]s?te/gi, text: 'Pent', },
  { regex: /\bAscensione?/gi, text: 'Asc', },
  { regex: /\bEstate/gi, text: 'Est', },

  { regex: /\b(?:und|et?)\b/g, text: '&' },
  { regex: /\b(?:19|20)(\d{2})\b/g, text: '$1' },
  { regex: /\s/g, text: '' },
]

function campShortTitle(camp) {
  if (camp?.shortTitle != null && camp.shortTitle !== '' && typeof camp.shortTitle !== 'symbol') {
    return `${camp.shortTitle}`.substring(0, 16)
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
