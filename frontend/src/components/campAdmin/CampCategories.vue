<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <v-skeleton-loader v-if="camp().categories()._meta.loading " type="article" />
  <v-list v-else class="py-0">
    <template v-if="!disabled">
      <dialog-category-edit v-for="category in categories.items"
                            :key="category._meta.self"
                            :camp="camp()"
                            :category="category">
        <template #activator="{ on }">
          <v-list-item
            class="px-0"
            v-on="on">
            <v-list-item-content class="chip mr-2">
              <v-chip dark :color="category.color">
                (1.{{ category.numberingStyle }}) {{ category.short }}
              </v-chip>
            </v-list-item-content>
            <v-list-item-content>
              <v-list-item-title>
                {{ category.name }}
              </v-list-item-title>
            </v-list-item-content>

            <v-list-item-action v-if="!disabled" style="display: inline">
              <v-item-group>
                <button-edit
                  class="mr-1"
                  icon="mdi-view-dashboard-variant"
                  :to="categoryRoute(camp(), category)">
                  {{ $tc('components.camp.campCategories.editLayout') }}
                </button-edit>
              </v-item-group>
            </v-list-item-action>

            <v-menu v-if="!disabled" offset-y>
              <template #activator="{ onOpen, attrs }">
                <v-btn icon v-bind="attrs" v-on="onOpen">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-card>
                <v-item-group>
                  <v-list-item-action>
                    <dialog-entity-delete :entity="category">
                      {{ $tc('components.camp.CampCategories.deleteCategoryQuestion') }}
                      <ul>
                        <li>
                          {{ category.short }}: {{ category.name }}
                        </li>
                      </ul>
                      <template #activator="{ onDelete }">
                        <button-delete v-on="onDelete" />
                      </template>
                      <template v-if="findActivities(category).length > 0" #error>
                        {{ $tc('components.camp.CampCategories.deleteCategoryNotPossibleInUse') }}
                        <ul>
                          <li v-for="activity in findActivities(category)" :key="activity._meta.self">
                            {{ activity.title }}
                            <ul>
                              <li v-for="scheduleEntry in activity.scheduleEntries().items"
                                  :key="scheduleEntry._meta.self">
                                <router-link
                                  :to="{ name: 'activity', params: { campId: camp().id, scheduleEntryId: scheduleEntry.id } }">
                                  {{ rangeShort(scheduleEntry.start, scheduleEntry.end) }}
                                </router-link>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </template>
                    </dialog-entity-delete>
                  </v-list-item-action>
                </v-item-group>
              </v-card>
            </v-menu>
          </v-list-item>
        </template>
      </dialog-category-edit>
    </template>
  </v-list>
</template>

<script>
import { categoryRoute } from '@/router.js'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import DialogCategoryEdit from './DialogCategoryEdit.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { rangeShort } from '@/common/helpers/dateHelperUTCFormatted.js'

export default {
  name: 'CampCategories',
  components: {
    ButtonEdit,
    ButtonDelete,
    DialogCategoryEdit,
    DialogEntityDelete
  },
  props: {
    camp: {
      type: Function,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {}
  },
  computed: {
    categories () {
      return this.camp().categories()
    }
  },
  methods: {
    rangeShort,
    categoryRoute,
    findActivities (category) {
      const activities = this.camp().activities()
      return activities.items.filter(a => a.category().id === category.id)
    }
  }
}
</script>

<style scoped>
.chip {
  flex-basis: auto;
  flex-shrink: 1;
  flex-grow: 0;
}
</style>
