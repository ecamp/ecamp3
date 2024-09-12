<template>
  <li>
    <div
      class="d-flex gap-2 align-baseline pb-2"
      @click="checked ? $emit('remove-item', item.id) : $emit('add-item', item.id)"
    >
      <component :is="checked ? 'strong' : 'span'" class="flex-grow-1"
        >{{ item.text }}
      </component>
      <v-switch inset dense :input-value="checked" hide-details class="mt-0" />
    </div>
    <ol v-if="children.length > 0">
      <ChecklistItem
        v-for="child in children"
        :key="child._meta.self"
        :checklist="checklist"
        :item="child"
        v-on="$listeners"
      />
    </ol>
  </li>
</template>

<script>
import { filter, sortBy } from 'lodash'

export default {
  name: 'ChecklistItem',
  inject: ['checkedItems'],
  props: {
    checklist: {
      type: Object,
      default: null,
      required: false,
    },
    item: {
      type: Object,
      default: null,
      required: false,
    },
  },
  emits: ['add-item', 'remove-item'],
  computed: {
    children() {
      return sortBy(
        filter(
          this.checklist.checklistItems().items,
          ({ parent }) => parent?.()._meta.self === this.item?._meta.self
        ),
        'position'
      )
    },
    checked() {
      return this.checkedItems.includes(this.item.id)
    },
  },
}
</script>

<style scoped></style>
