<template>
  <v-container fluid>
    <content-card :title="$tc('views.home.home')" max-width="800">
      <p class="mx-4">
        <v-skeleton-loader type="text" :loading="api.get().profile()._meta.loading">
          {{ $tc('views.home.welcome', 1, { user: api.get().profile().displayName }) }}
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
  computed: {
    displayName () {
      return this.api.get().profile().displayName
    }
  },
  mounted () {
    this.api.get().profile()
  }
}
</script>

<style lang="scss" scoped>
</style>
