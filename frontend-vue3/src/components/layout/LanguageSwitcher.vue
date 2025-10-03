<template>
  <v-menu location="top right">
    <template #activator="{ props }">
      <v-btn
        id="languageSwitcher"
        size="small"
        class="my-n2"
        v-bind="props"
        :aria-label="$t('global.changeLanguage')"
        variant="text"
      >
        <v-icon size="small" start>mdi-translate</v-icon>
        {{ $t('global.language') }}
      </v-btn>
    </template>
    <v-list aria-labelledby="languageSwitcher" density="compact" tag="ul">
      <v-list-item
        v-for="item in $root.$i18n.availableLocales"
        :key="item"
        tag="li"
        :lang="item"
        @click="changeLang(item)"
      >
        <v-list-item-title>{{ $t('global.language', 1, item) }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  name: 'LanguageSwitcher',
  methods: {
    changeLang(lang) {
      this.$store.commit('setLanguage', lang)
    },
  },
}
</script>

<style scoped></style>
