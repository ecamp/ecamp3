<template>
  <content-group>
    <slot name="title">
      <div class="ec-content-group__title py-1 subtitle-1">
        {{ $tc('components.campAdmin.campCategories.title') }}
        <dialog-category-create v-if="!disabled" :camp="camp()">
          <template #activator="{ on }">
            <button-add
              color="secondary"
              text
              :hide-label="$vuetify.breakpoint.xsOnly"
              class="my-n2"
              v-on="on"
            >
              {{ $tc('components.campAdmin.campCategories.create') }}
            </button-add>
          </template>
        </dialog-category-create>
      </div>
    </slot>
    <v-skeleton-loader v-if="camp().categories()._meta.loading" type="article" />
    <v-list>
      <v-list-item
        v-for="category in categories.items"
        :key="category._meta.self"
        class="px-0"
      >
        <v-list-item-content class="pt-0 pb-2">
          <v-list-item-title>
            <CategoryChip :category="category">
              (1.{{ category.numberingStyle }}) {{ category.short }}: {{ category.name }}

              <dialog-category-edit v-if="!disabled" :camp="camp()" :category="category">
                <template #activator="{ on }">
                  <v-icon class="ml-2" size="150%" v-on="on">mdi-pencil</v-icon>
                </template>
              </dialog-category-edit>
            </CategoryChip>
          </v-list-item-title>
        </v-list-item-content>

        <v-list-item-action v-if="!disabled" style="display: inline">
          <v-item-group>
            <button-edit
              class="mr-1"
              icon="mdi-view-dashboard-variant"
              :to="categoryRoute(camp(), category)"
            >
              {{ $tc('components.campAdmin.campCategories.editLayout') }}
            </button-edit>
          </v-item-group>
        </v-list-item-action>

        <v-menu v-if="!disabled" offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-item-group>
              <v-list-item-action>
                <dialog-entity-delete :entity="category">
                  {{ $tc('components.campAdmin.campCategories.deleteCategoryQuestion') }}
                  <ul>
                    <li>{{ category.short }}: {{ category.name }}</li>
                  </ul>
                  <template #activator="{ on }">
                    <button-delete v-on="on" />
                  </template>
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
                </dialog-entity-delete>
              </v-list-item-action>
            </v-item-group>
          </v-card>
        </v-menu>
      </v-list-item>
    </v-list>
  </content-group>
</template>

<script>
import { categoryRoute } from '@/router.js'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import DialogCategoryEdit from './DialogCategoryEdit.vue'
import DialogCategoryCreate from './DialogCategoryCreate.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'

export default {
  name: 'CampCategories',
  components: {
    ButtonAdd,
    ButtonEdit,
    ButtonDelete,
    ContentGroup,
    CategoryChip,
    DialogCategoryEdit,
    DialogCategoryCreate,
    DialogEntityDelete,
  },
  mixins: [dateHelperUTCFormatted],
  props: {
    camp: { type: Function, required: true },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {}
  },
  computed: {
    categories() {
      return this.camp().categories()
    },
  },
  methods: {
    categoryRoute,
    findActivities(category) {
      const activities = this.camp().activities()
      return activities.items.filter((a) => a.category().id === category.id)
    },
  },
}
</script>

<style scoped></style>
