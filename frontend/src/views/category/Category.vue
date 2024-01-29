<template>
  <v-container fluid>
    <content-card
      v-if="category()"
      class="ec-category"
      toolbar
      back
      :max-width="isPaperDisplaySize ? '944px' : ''"
    >
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          <CategoryChip :category="category()" dense large />
          {{ category().name }}
        </v-toolbar-title>
      </template>

      <template #title-actions>
        <TogglePaperSize v-model="isPaperDisplaySize" />
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
              :success-handler="goToActivityAdmin"
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
      <v-expansion-panels :value="openPanels" flat multiple accordion>
        <v-expansion-panel>
          <v-expansion-panel-header>
            <h3>
              {{ $tc('views.category.category.properties') }}
            </h3>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <CategoryProperties :category="category()" :disabled="!isManager" />
          </v-expansion-panel-content>
        </v-expansion-panel>

        <v-expansion-panel>
          <v-expansion-panel-header>
            <h3>
              {{ $tc('views.category.category.template') }}
            </h3>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <CategoryTemplate
              :category="category()"
              :layout-mode="layoutMode"
              :loading="loading"
              :disabled="!isManager"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import ErrorExistingActivitiesList from '@/components/campAdmin/ErrorExistingActivitiesList.vue'
import CategoryProperties from '@/components/category/CategoryProperties.vue'
import CategoryTemplate from '@/components/category/CategoryTemplate.vue'
import TogglePaperSize from '@/components/activity/TogglePaperSize.vue'

export default {
  name: 'Category',
  components: {
    TogglePaperSize,
    CategoryTemplate,
    CategoryProperties,
    ErrorExistingActivitiesList,
    DialogEntityDelete,
    CategoryChip,
    ContentCard,
  },
  mixins: [campRoleMixin],
  provide() {
    return {
      preferredContentTypes: () => this.preferredContentTypes,
      allContentNodes: () => this.contentNodes,
      camp: () => this.camp(),
      isPaperDisplaySize: () => this.isPaperDisplaySize,
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
      openPanels: [0, 1],
    }
  },
  computed: {
    contentNodes() {
      return this.category().contentNodes()
    },
    preferredContentTypes() {
      return this.category().preferredContentTypes()
    },
    isPaperDisplaySize: {
      get() {
        console.log('calling getter')
        return this.$store.getters.getPaperDisplaySize(this.camp()._meta.self)
      },
      set(value) {
        this.$store.commit('setPaperDisplaySize', {
          campUri: this.camp()._meta.self,
          paperDisplaySize: value,
        })
        console.log('calling setter')
      },
    },
  },

  // reload data every time user navigates to Category view
  async mounted() {
    // hide properties panel if new category has been created and user is redirected here
    const url = new URL(window.location)
    if (url.searchParams.get('new') === 'true') {
      url.searchParams.delete('new')
      window.history.replaceState({}, '', url)
      this.openPanels = [1]
    }
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

<style>
.ec-category {
  transition: max-width 0.7s ease;
}
</style>
