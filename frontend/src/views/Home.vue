<template>
  <v-container fluid>
    <content-card :title="$tc('views.home.home')" max-width="800" toolbar>
      <v-card-text>
        <v-skeleton-loader type="text" :loading="user._meta.loading">
          {{ $tc('views.home.welcome', 1, { user: user.displayName }) }} <v-icon>$vuetify.icons.ecamp</v-icon>
        </v-skeleton-loader>
      </v-card-text>
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
import ContentCard from '@/components/layout/ContentCard.vue'

export default {
  name: 'Home',
  components: { ContentCard },
  computed: {
    user () {
      return this.$auth.user()
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
