<template>
  <v-menu location="top right">
    <template #activator="{ props }">
      <v-btn
        id="languageSwitcher"
        variant="text"
        class="my-n2"
        size="small"
        :aria-label="$tc('global.changeLanguage')"
        v-bind="props"
      >
        <v-icon start size="small">mdi-translate</v-icon>
        {{ $tc('global.language') }}
      </v-btn>
    </template>
    <v-list density="compact" tag="ul" aria-labelledby="languageSwitcher">
      <v-list-item
        v-for="item in $root.$i18n.availableLocales"
        :key="item"
        tag="li"
        :lang="item"
        @click="changeLang(item)"
      >
        <v-list-item-title>{{ $tc('global.language', 1, item) }}</v-list-item-title>
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
