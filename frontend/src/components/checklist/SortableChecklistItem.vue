<template>
  <div class="e-sortable-checklist-item" @dragstart="gugus">
    <ChecklistItemEdit :checklist="checklist" :checklist-item="item">
      <template #activator="{ on }">
        <v-list-item class="px-2 rounded min-h-0 py-1 drag-and-drop-handle" v-on="on">
          <v-btn text plain icon class="my-n1 ml-n1 pointer-events-none">
            <v-icon>mdi-drag</v-icon>
          </v-btn>
          <v-avatar color="rgba(0,0,0,0.12)" class="mr-2" size="32">{{
            itemPosition + 1
          }}</v-avatar>
          <v-list-item-content class="py-0">
            <v-list-item-title :class="{ 'font-weight-bold': item?.parent == null }">{{
              item.text
            }}</v-list-item-title>
          </v-list-item-content>
          <ButtonEdit
            color="primary--text"
            dense
            text
            class="e-sortable-checklist-item__edit my-n1"
          />
        </v-list-item>
      </template>
    </ChecklistItemEdit>
    <SortableChecklist
      class="ml-8"
      :checklist="checklist"
      :parent="item"
      @drag-start="$emit('drag-start')"
      @drag-end="$emit('drag-end')"
    />
  </div>
</template>

<script>
import ChecklistItemEdit from '@/components/checklist/ChecklistItemEdit.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'

export default {
  name: 'SortableChecklistItem',
  components: {
    ButtonEdit,
    ChecklistItemEdit,
    SortableChecklist: () => import('@/components/checklist/SortableChecklist.vue'),
  },
  props: {
    itemPosition: { type: Number, required: true },
    item: { type: Object, required: true },
    checklist: { type: Object, required: true },
  },
  data() {
    return {
      hover: false,
    }
  },
  methods: {
    gugus(event) {
      event.target.classList.add('e-sortable-checklist-item--drag-preview')

      requestAnimationFrame(() => {
        event.target.classList.remove('e-sortable-checklist-item--drag-preview')
      })
    },
  },
}
</script>

<style scoped>
.e-sortable-checklist-item--drag-preview {
  background: white;
  border-radius: 4px;
}
.e-sortable-checklist-item--drag-preview:deep(.e-sortable-checklist-item__add) {
  display: none;
}
.e-sortable-checklist-item--drag-preview:deep(.e-checklist-dragarea) {
  padding: 0;
  min-height: 0;
}
.e-sortable-checklist-item--drag-preview .e-sortable-checklist-item__edit {
  display: none !important;
}
.e-sortable-checklist-item__edit {
  display: none;
}
.e-sortable-checklist-item:is(:hover, :focus-visible):not(
    :has(.e-sortable-checklist-item:hover)
  ):not(:has(.e-sortable-checklist-item__add:hover))
  > .v-list-item
  > .e-sortable-checklist-item__edit {
  display: block;
}
</style>
