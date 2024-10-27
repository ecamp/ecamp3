<template>
  <content-card :title="$tc('entity.checklist.name', 2)" toolbar max-width="800">
    <template #title-actions>
      <ChecklistCreate :camp="camp" :checklist-collection="checklistCollection" />
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
              <DialogEntityDelete
                :entity="checklist"
                :warning-text-entity="checklist.name"
                :error-handler="deleteErrorHandler"
              >
                <template #activator="{ on }">
                  <ButtonDelete
                    text
                    class="my-n1 ml-2 v-btn--has-bg"
                    @click.prevent="on.click"
                  />
                </template>
              </DialogEntityDelete>
            </v-item-group>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ChecklistCreate from '@/components/checklist/ChecklistCreate.vue'
import { checklistRoute } from '@/router.js'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'

export default {
  name: 'ChecklistOverview',
  components: {
    DialogEntityDelete,
    ButtonDelete,
    ButtonEdit,
    ChecklistCreate,
    ContentCard,
  },
  props: {
    camp: { type: Object, required: false, default: null },
    checklistCollection: { type: Object, required: true },
  },
  computed: {
    checklists() {
      return this.checklistCollection.items
    },
  },
  methods: {
    checklistRoute,
    deleteErrorHandler(e) {
      if (e?.response?.status === 422 /* Validation Error */) {
        this.$toast.error(this.$tc('components.checklist.checklistOverview.deleteError'))
      }
      return null
    },
  },
}
</script>
