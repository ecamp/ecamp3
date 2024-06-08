<template>
  <content-group
    :title="$tc('components.campAdmin.campCategories.title')"
    icon="mdi-shape"
  >
    <template #title-actions>
      <DialogCategoryCreate v-if="!disabled" :camp="camp">
        <template #activator="{ on }">
          <ButtonAdd
            color="secondary"
            text
            :hide-label="$vuetify.breakpoint.xsOnly"
            class="my-n2"
            v-on="on"
          >
            {{ $tc('components.campAdmin.campCategories.create') }}
          </ButtonAdd>
        </template>
      </DialogCategoryCreate>
    </template>
    <v-skeleton-loader
      v-if="camp.categories()._meta.loading"
      type="list-item@3"
      class="mx-n4"
    />
    <v-list class="mx-n2">
      <v-list-item
        v-for="category in categories.items"
        :key="category._meta.self"
        class="px-2 rounded"
        :to="categoryRoute(camp, category)"
      >
        <v-list-item-content>
          <v-list-item-title>
            <CategoryChip :category="category" class="pointer-events-none">
              (1.{{ category.numberingStyle }}) {{ category.short }}: {{ category.name }}
            </CategoryChip>
          </v-list-item-title>
        </v-list-item-content>

        <v-list-item-action v-if="!disabled" style="display: inline">
          <v-item-group>
            <ButtonEdit color="primary--text" text class="my-n1 v-btn--has-bg" />
          </v-item-group>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </content-group>
</template>

<script>
import { categoryRoute } from '@/router.js'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import DialogCategoryCreate from './DialogCategoryCreate.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'

export default {
  name: 'CampCategories',
  components: {
    ButtonAdd,
    ButtonEdit,
    ContentGroup,
    CategoryChip,
    DialogCategoryCreate,
  },
  mixins: [dateHelperUTCFormatted],
  props: {
    camp: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {}
  },
  computed: {
    categories() {
      return this.camp.categories()
    },
  },
  methods: {
    categoryRoute,
  },
}
</script>
