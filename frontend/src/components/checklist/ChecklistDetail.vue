<template>
  <content-card
    v-if="checklist"
    :key="checklist._meta.self"
    class="ec-checklist"
    toolbar
    back
  >
    <template #title>
      <v-toolbar-title v-if="!editChecklistName" tag="h1" class="font-weight-bold">
        {{ checklist.name }}
      </v-toolbar-title>
      <v-btn
        v-if="!editChecklistName"
        icon
        class="ml-1 visible-on-hover"
        width="24"
        height="24"
        @click="makeChecklistNameEditable()"
      >
        <v-icon small>mdi-pencil</v-icon>
      </v-btn>
      <api-form v-if="editChecklistName" :entity="checklist" class="mx-2 flex-grow-1">
        <api-text-field
          path="name"
          :disabled="layoutMode"
          dense
          autofocus
          :auto-save="false"
          @finished="editChecklistName = false"
        />
      </api-form>
    </template>
    <template #title-actions>
      <ChecklistItemCreate :checklist="checklist" />
    </template>
    <v-list>
      <SortableChecklist :parent="null" :checklist="checklist" />
    </v-list>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ChecklistItemCreate from '@/components/checklist/ChecklistItemCreate.vue'
import SortableChecklist from '@/components/checklist/SortableChecklist.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'

export default {
  name: 'ChecklistDetail',
  components: {
    SortableChecklist,
    ChecklistItemCreate,
    ContentCard,
    ApiForm,
  },
  props: {
    camp: {
      type: Object,
      default: null,
      required: false,
    },
    checklist: {
      type: Object,
      default: null,
      required: false,
    },
  },
  data() {
    return { dragging: false, editChecklistName: false }
  },
  computed: {
    items() {
      return this.checklist.checklistItems().items.filter((item) => !item.parent)
    },
  },
  methods: {
    makeChecklistNameEditable() {
      this.editChecklistName = true
    },
  },
}
</script>
