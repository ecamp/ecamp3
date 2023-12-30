<template>
  <v-container fluid>
    <content-card toolbar back>
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          <template v-if="!category()._meta.loading">
            <CategoryChip :category="category()" dense large />
            {{ category().name }}
          </template>
          <template v-else> loading... </template>
        </v-toolbar-title>
      </template>

      <template #title-actions>
        <v-btn v-if="!layoutMode" color="primary" outlined @click="layoutMode = true">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-puzzle-edit-outline</v-icon>
            {{ $tc('views.activity.category.changeLayout') }}
          </template>
          <template v-else>{{ $tc('views.activity.category.layout') }}</template>
        </v-btn>
        <v-btn v-else color="success" outlined @click="layoutMode = false">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-file-document-arrow-right-outline</v-icon>
            {{ $tc('views.activity.category.editContents') }}
          </template>
          <template v-else>{{ $tc('views.activity.category.contents') }}</template>
        </v-btn>
      </template>
      <v-card-text class="px-0 py-0">
        <v-skeleton-loader v-if="loading" type="article" />

        <div
          v-if="!loading && category().rootContentNode().children().items.length === 0"
          class="text-center blue lighten-4 blue--text py-4 px-2 text--darken-4 create-layout-help"
        >
          <i18n path="views.activity.category.createLayoutHelp">
            <template #categoryShort>{{ category().short }}</template>
            <template #br><br /></template>
          </i18n>
        </div>
        <root-node
          v-if="!loading"
          :content-node="category().rootContentNode()"
          :layout-mode="layoutMode"
          :disabled="!isManager"
        />
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import RootNode from '@/components/activity/RootNode.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'

export default {
  name: 'Category',
  components: {
    CategoryChip,
    ContentCard,
    RootNode,
  },
  mixins: [campRoleMixin],
  provide() {
    return {
      preferredContentTypes: () => this.preferredContentTypes,
      allContentNodes: () => this.contentNodes,
      camp: () => this.camp,
    }
  },
  props: {
    category: {
      type: Function,
      required: true,
    },
  },
  data() {
    return {
      layoutMode: true,
      loading: true,
    }
  },
  computed: {
    camp() {
      return this.category().camp()
    },
    contentNodes() {
      return this.category().contentNodes()
    },
    preferredContentTypes() {
      return this.category().preferredContentTypes()
    },
  },

  // reload data every time user navigates to Category view
  async mounted() {
    this.loading = true
    await this.category()._meta.load // wait if category is being loaded as part of a collection
    await this.category().$reload() // reload as single entity to ensure all embedded entities are included in a single network request
    this.loading = false
  },
}
</script>

<style scoped>
.v-application .create-layout-help {
  border-bottom: 1px solid #90caf9 !important;
}
</style>
