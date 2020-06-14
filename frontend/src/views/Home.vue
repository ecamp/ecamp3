<template>
  <v-container>
    <content-card :title="$t('views.home.home')" max-width="600">
      <p class="mx-4">
        <v-skeleton-loader type="text" :loading="api.get()._meta.loading">
          {{ $t('views.home.welcome', { user: api.get().profile().displayName }) }}
        </v-skeleton-loader>
      </p>
      <v-list class="pt-0">
        <v-list-item :to="{ name: 'camps' }">
          <v-list-item-icon>
            <v-icon>mdi-format-list-bulleted-triangle</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>{{ $tc('views.home.myCamps', api.get().camps().items.length) }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </content-card>
  </v-container>
</template>

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
