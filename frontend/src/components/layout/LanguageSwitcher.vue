<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        text small
        id="languageSwitcher"
        :aria-label="$t('changeLanguage')"
        v-bind="attrs"
        v-on="on">
        <v-icon left small>mdi-translate</v-icon>
        {{ i18nLocales[$root.$i18n.locale] }}
      </v-btn>
    </template>
    <v-list dense tag="ul"
            aria-labelledby="languageSwitcher">
      <v-list-item
        tag="li"
        v-for="item in $root.$i18n.availableLocales"
        :key="item"
        :lang="item"
        @click="changeLang(item)">
        <v-list-item-title>{{ i18nLocales[item] }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<i18n>
  {
    "de": {
      "changeLanguage": "Sprache ändern"
    },
    "en": {
      "changeLanguage": "Change language"
    },
    "fr": {
      "changeLanguage": "Changer la langue"
    },
    "it": {
      "changeLanguage": "Cambia lingua"
    }
  }
</i18n>

<script>
export default {
  name: 'LanguageSwitcher',
  data () {
    return {
      i18nLocales: {
        de: 'Deutsch',
        en: 'English',
        fr: 'Français',
        it: 'Italiano'
      }
    }
  },
  methods: {
    changeLang (lang) {
      this.$root.$i18n.locale = lang
      this.axios.defaults.headers.common['Accept-Language'] = lang
      document.querySelector('html').setAttribute('lang', lang)
    }
  }
}
</script>

<style scoped>

</style>
