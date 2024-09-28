<template>
  <content-card title="Checklists" toolbar>
    <template #title-actions>
      <ChecklistCreate :camp="camp" />
    </template>
    <v-card-text>
      <v-list class="mx-n2 py-0">
        <v-list-item
          v-for="checklist in checklists"
          :key="checklist._meta.self"
          :to="checklistRoute(camp, checklist)"
          class="px-2 rounded"
        >
          <v-list-item-content>
            <v-list-item-title>
              {{ checklist.name }}
            </v-list-item-title>
          </v-list-item-content>

          <v-list-item-action style="display: inline">
            <v-item-group>
              <ButtonEdit color="primary--text" text class="my-n1 v-btn--has-bg" />
            </v-item-group>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import ChecklistCreate from '@/components/checklist/ChecklistCreate.vue'
import { checklistRoute } from '@/router.js'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'

export default {
  name: 'CampAdminChecklists',
  components: {
    ButtonEdit,
    ChecklistCreate,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
  },
  computed: {
    checklists() {
      return this.camp.checklists().items
    },
  },
  methods: { checklistRoute },
}
</script>
