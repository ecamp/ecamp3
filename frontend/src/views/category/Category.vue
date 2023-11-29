<template>
  <v-container fluid>
    <content-card toolbar back class="mb-4">
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          <template v-if="!category()._meta.loading">
            <CategoryChip :category="category()" dense large />
            {{ category().name }}
          </template>
          <template v-else> loading...</template>
        </v-toolbar-title>
      </template>
      <template #title-actions>
        <v-menu v-if="!disabled" offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list>
            <DialogEntityDelete :entity="category()">
              <template #activator="{ on }">
                <v-list-item v-on="on">
                  <v-list-item-icon>
                    <v-icon>mdi-delete</v-icon>
                  </v-list-item-icon>
                  <v-list-item-title>
                    {{ $tc('global.button.delete') }}
                  </v-list-item-title>
                </v-list-item>
              </template>
              {{ $tc('components.campAdmin.campCategories.deleteCategoryQuestion') }}
              <ul>
                <li>{{ category.short }}: {{ category.name }}</li>
              </ul>
              <template v-if="findActivities(category).length > 0" #error>
                {{
                  $tc(
                    'components.campAdmin.campCategories.deleteCategoryNotPossibleInUse'
                  )
                }}
                <ul>
                  <li
                    v-for="activity in findActivities(category)"
                    :key="activity._meta.self"
                  >
                    {{ activity.title }}
                    <ul>
                      <li
                        v-for="scheduleEntry in activity.scheduleEntries().items"
                        :key="scheduleEntry._meta.self"
                      >
                        <router-link
                          :to="{
                            name: 'activity',
                            params: {
                              campId: camp().id,
                              scheduleEntryId: scheduleEntry.id,
                            },
                          }"
                        >
                          {{ rangeShort(scheduleEntry.start, scheduleEntry.end) }}
                        </router-link>
                      </li>
                    </ul>
                  </li>
                </ul>
              </template>
            </DialogEntityDelete>
          </v-list>
        </v-menu>
      </template>
      <v-card-text>
        <DialogCategoryForm :camp="camp" :is-new="false" :category="category()" />
      </v-card-text>
      <v-divider />
      <v-card-title>Vorlage</v-card-title>
      <v-card-text>
        <i18n path="views.category.category.createLayoutHelp">
          <template #categoryShort>
            <CategoryChip :category="category()" dense />
          </template>
          <template #br><br /></template>
        </i18n>
      </v-card-text>
      <div class="pa-4">
        <div class="area rounded">
          <v-tabs
            v-model="layoutMode"
            class="ec-category-layoutmode-tabs"
            centered
            hide-slider
          >
            <v-tab :tab-value="true">
              <v-icon left>mdi-view-compact-outline</v-icon>
              Layout
            </v-tab>
            <v-tab :tab-value="false">
              <v-icon left>mdi-text</v-icon>
              Inhalt
            </v-tab>
          </v-tabs>
          <v-divider />
          <v-tabs-items v-model="layoutMode" class="transparent rounded-b">
            <v-tab-item :value="true" :transition="null">
              <v-skeleton-loader v-if="loading" type="article" />
              <root-node
                v-else
                :content-node="category().rootContentNode()"
                :layout-mode="true"
              />
            </v-tab-item>
            <v-tab-item :value="false" :transition="null">
              <v-skeleton-loader v-if="loading" type="article" />
              <div
                v-else-if="category().rootContentNode().children().items.length === 0"
                class="pa-2 text-center"
              >
                Keine Vorlage vorhanden.
              </div>
              <root-node
                v-else
                :content-node="category().rootContentNode()"
                :layout-mode="false"
              />
            </v-tab-item>
          </v-tabs-items>
        </div>
      </div>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import RootNode from '@/components/activity/RootNode.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import DialogCategoryForm from '@/components/campAdmin/DialogCategoryForm.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { rangeShort } from '../../common/helpers/dateHelperUTCFormatted.js'

export default {
  name: 'Category',
  components: {
    DialogEntityDelete,
    DialogCategoryForm,
    CategoryChip,
    ContentCard,
    RootNode,
  },
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
  methods: {
    rangeShort,
    findActivities(category) {
      const activities = this.camp.activities()
      return activities.items.filter((a) => a.category().id === category.id)
    },
  },
}
</script>

<style scoped>
.v-tabs :deep(.v-tabs-slider-wrapper) {
  transition: none;
}

.area {
  border: 2px dashed #ccc;
  background: #eee;
}

.ec-category-layoutmode-tabs :deep(.v-tab::before) {
  border-radius: 999px;
  margin: 8px 4px;
}

.ec-category-layoutmode-tabs :deep(.v-tab--active.v-tab:not(:focus)::before) {
  opacity: 0.12;
}
</style>
