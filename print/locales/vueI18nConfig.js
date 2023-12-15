import deepmerge from 'deepmerge'

import it from '@/locales/it.json'
import itCHScout from '@/locales/it-CH-scout.json'
import fr from '@/locales/fr.json'
import frCHScout from '@/locales/fr-CH-scout.json'
import en from '@/locales/en.json'
import enCHScout from '@/locales/en-CH-scout.json'
import de from '@/locales/de.json'
import deCHScout from '@/locales/de-CH-scout.json'

import itCommon from '~/../common/locales/it.json'
import itCHScoutCommon from '~/../common/locales/it-CH-scout.json'
import frCommon from '~/../common/locales/fr.json'
import frCHScoutCommon from '~/../common/locales/fr-CH-scout.json'
import enCommon from '~/../common/locales/en.json'
import enCHScoutCommon from '~/../common/locales/en-CH-scout.json'
import deCommon from '~/../common/locales/de.json'
import deCHScoutCommon from '~/../common/locales/de-CH-scout.json'

export default defineI18nConfig(() => ({
  fallbackLocale: 'en',
  messages: deepmerge(
    {
      it: itCommon,
      'it-CH-scout': itCHScoutCommon,
      fr: frCommon,
      'fr-CH-scout': frCHScoutCommon,
      en: enCommon,
      'en-CH-scout': enCHScoutCommon,
      de: deCommon,
      'de-CH-scout': deCHScoutCommon,
    },
    {
      it,
      'it-CH-scout': itCHScout,
      fr,
      'fr-CH-scout': frCHScout,
      en,
      'en-CH-scout': enCHScout,
      de,
      'de-CH-scout': deCHScout,
    }
  ),
  missingWarn: false,
  fallbackWarn: false,
}))
