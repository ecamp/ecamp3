<template>
  <v-container fluid>
    <content-card
      v-if="category"
      class="ec-category"
      toolbar
      back
      :max-width="isPaperDisplaySize ? '944px' : ''"
    >
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          <CategoryChip :category="category" dense large />
          {{ category.name }}
        </v-toolbar-title>
      </template>

      <template #title-actions>
        <TogglePaperSize v-model="isPaperDisplaySize" />
        <v-menu offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="copyUrlToClipboard">
              <v-list-item-icon>
                <v-icon>mdi-content-copy</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc('views.category.category.copyCategory') }}
              </v-list-item-title>
            </v-list-item>
            <CopyCategoryInfoDialog ref="copyInfoDialog" />
            <DialogEntityDelete
              v-if="isManager"
              :entity="category"
              :warning-text-entity="category.name"
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
              <template v-if="findActivities(category).length > 0" #error>
                <ErrorExistingActivitiesList
                  :camp="camp"
                  :existing-activities="findActivities(category)"
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
            <CategoryProperties :category="category" :disabled="!isManager" />
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
              :category="category"
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
import router, { categoryRoute } from '@/router.js'
import CopyCategoryInfoDialog from '@/components/category/CopyCategoryInfoDialog.vue'

export default {
  name: 'Category',
  components: {
    CopyCategoryInfoDialog,
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
      camp: this.camp,
      isPaperDisplaySize: () => this.isPaperDisplaySize,
    }
  },
  props: {
    camp: {
      type: Object,
      default: null,
      required: false,
    },
    category: {
      type: Object,
      default: null,
      required: false,
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
      return this.category.contentNodes()
    },
    preferredContentTypes() {
      return this.category.preferredContentTypes()
    },
    isPaperDisplaySize: {
      get() {
        return this.$store.getters.getPaperDisplaySize(this.camp._meta.self)
      },
      set(value) {
        this.$store.commit('setPaperDisplaySize', {
          campUri: this.camp._meta.self,
          paperDisplaySize: value,
        })
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
    await this.category._meta.load // wait if category is being loaded as part of a collection
    await this.category.$reload() // reload as single entity to ensure all embedded entities are included in a single network request
    this.loading = false
  },
  methods: {
    findActivities(category) {
      return this.camp
        .activities()
        .items.filter(
          (activity) => activity.category()._meta.self === category._meta.self
        )
    },
    goToActivityAdmin() {
      this.$router.replace({ name: 'admin/activity', params: { campId: this.camp.id } })
    },
    async copyUrlToClipboard() {
      try {
        const res = await navigator.permissions.query({ name: 'clipboard-read' })
        if (res.state === 'prompt') {
          this.$refs.copyInfoDialog.open()
        }
      } catch {
        console.warn('clipboard permission not requestable')
      }

      const category = categoryRoute(this.camp, this.category)
      const url = window.location.origin + router.resolve(category).href
      await navigator.clipboard.writeText(url)

      this.$toast.info(
        this.$tc('global.toast.copied', null, { source: this.category.name }),
        {
          timeout: 2000,
        }
      )
    },
  },
}
</script>

<style>
.ec-category {
  transition: max-width 0.7s ease;
}
</style>
