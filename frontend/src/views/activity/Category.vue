<template>
  <v-container fluid>
    <content-card toolbar>
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          <template v-if="!category()._meta.loading">
            <v-chip
              :color="category().color"
              dark>
              {{ category().short }}
            </v-chip>
            {{ category().name }}
          </template>
          <template v-else>
            loading...
          </template>
        </v-toolbar-title>
      </template>

      <template #title-actions>
        <v-btn v-if="!layoutMode"
               color="primary"
               outlined
               @click="layoutMode = true">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-puzzle-edit-outline</v-icon>
            {{ $tc('views.activity.activity.changeLayout') }}
          </template>
          <template v-else>{{ $tc('views.activity.activity.layout') }}</template>
        </v-btn>
        <v-btn v-else
               color="success"
               outlined
               @click="layoutMode = false">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-check</v-icon>
            {{ $tc('views.activity.activity.backToContents') }}
          </template>
          <template v-else>{{ $tc('views.activity.activity.back') }}</template>
        </v-btn>
      </template>
      <v-card-text class="px-0 py-0">
        <v-skeleton-loader v-if="loading" type="article" />

        <root-node
          v-if="!loading"
          :content-node="category().rootContentNode()"
          :layout-mode="layoutMode" />
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import RootNode from '@/components/activity/RootNode.vue'

export default {
  name: 'Category',
  components: {
    ContentCard,
    RootNode
  },
  provide () {
    return {
      preferredContentTypes: () => this.preferredContentTypes,
      allContentNodes: () => this.contentNodes,
      camp: () => this.camp
    }
  },
  props: {
    category: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      layoutMode: true,
      loading: true
    }
  },
  computed: {
    camp () {
      return this.category().camp()
    },
    contentNodes () {
      return this.category().contentNodes()
    },
    preferredContentTypes () {
      return this.category().preferredContentTypes()
    }
  },

  // reload data every time user navigates to Category view
  async mounted () {
    this.loading = true
    await this.category()._meta.load // wait if category is being loaded as part of a collection
    await this.category().$reload() // reload as single entity to ensure all embedded entities are included in a single network request
    this.loading = false
  }
}
</script>

<style scoped>
</style>
