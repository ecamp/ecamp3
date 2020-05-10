<template>
  <v-container>
    <content-card :title="$t('home')" max-width="600">
      <p class="mx-4">
        <v-skeleton-loader type="text" :loading="api.get()._meta.loading">
          {{ $t('welcome', { user: api.get().user }) }}
        </v-skeleton-loader>
      </p>
      <v-list class="pt-0">
        <v-list-item :to="{ name: 'camps' }">
          <v-list-item-icon>
            <v-icon>mdi-format-list-bulleted-triangle</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>{{ $tc('myCamps', api.get().camps().items.length) }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </content-card>
  </v-container>
</template>

<i18n>
  {
    "de": {
      "welcome": "Hallo {user}. Herzlich willkommen auf eCamp"
    },
    "en": {
      "welcome": "Hey {user}. Welcome to eCamp"
    }
  }
</i18n>

<script>
import ContentCard from '@/components/layout/ContentCard'

export default {
  name: 'Home',
  components: { ContentCard },
  mounted () {
    this.api.get().profile()
  }
}
</script>

<style lang="scss" scoped>
</style>
