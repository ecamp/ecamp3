<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        id="languageSwitcher" text
        small
        :aria-label="$t('global.changeLanguage')"
        v-bind="attrs"
        v-on="on">
        <v-icon left small>mdi-translate</v-icon>
        {{ $t('global.language') }}
      </v-btn>
    </template>
    <v-list dense tag="ul"
            aria-labelledby="languageSwitcher">
      <v-list-item
        v-for="item in $root.$i18n.availableLocales"
        :key="item"
        tag="li"
        :lang="item"
        @click="changeLang(item)">
        <v-list-item-title>{{ $t('global.language', item) }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  name: 'LanguageSwitcher',
  methods: {
    changeLang (lang) {
      this.$store.commit('setLanguage', lang)
    }
  }
}
</script>

<style scoped>

</style>
