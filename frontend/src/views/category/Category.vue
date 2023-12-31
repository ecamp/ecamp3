<template>
  <v-container fluid>
    <content-card v-if="category()" toolbar back>
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          <CategoryChip :category="category()" dense large />
          {{ category().name }}
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
        <v-menu v-if="isManager" offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list>
            <DialogEntityDelete
              :entity="category()"
              :warning-text-entity="category().name"
              :dialog-title="$tc('views.category.category.deleteCategory')"
              @submit="goToActivityAdmin"
            >
              <template #activator="{ on }">
                <v-list-item v-on="on">
                  <v-list-item-icon>
                    <v-icon>mdi-delete</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>
                    {{ $tc('views.category.category.deleteCategory') }}
                  </v-list-item-title>
                </v-list-item>
              </template>
              <template v-if="findActivities(category()).length > 0" #error>
                <ErrorExistingActivitiesList
                  :camp="camp()"
                  :existing-activities="findActivities(category())"
                />
              </template>
            </DialogEntityDelete>
          </v-list>
        </v-menu>
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
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import ErrorExistingActivitiesList from '@/components/campAdmin/ErrorExistingActivitiesList.vue'
export default {
  name: 'Category',
  components: {
    ErrorExistingActivitiesList,
    DialogEntityDelete,
    CategoryChip,
    ContentCard,
    RootNode,
  },
  mixins: [campRoleMixin],
  provide() {
    return {
      preferredContentTypes: () => this.preferredContentTypes,
      allContentNodes: () => this.contentNodes,
      camp: () => this.camp(),
    }
  },
  props: {
    camp: {
      type: Function,
      required: true,
    },
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
  methods: {
    findActivities(category) {
      return this.camp()
        .activities()
        .items.filter(
          (activity) => activity.category()._meta.self === category._meta.self
        )
    },
    goToActivityAdmin() {
      this.$router.push({ name: 'admin/activity', params: { campId: this.camp().id } })
    },
  },
}
</script>

<style scoped>
.v-application .create-layout-help {
  border-bottom: 1px solid #90caf9 !important;
}
</style>
