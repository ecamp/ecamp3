<template>
  <div class="w-100">
    <template v-for="{ checklist, items } in activeChecklists">
      <h3 :key="checklist._meta.self" class="px-4">{{ checklist.name }}</h3>
      <v-list-item
        v-for="{ item, parents } in items"
        :key="item._meta.self"
        class="min-h-0"
        :disabled="layoutMode"
      >
        <v-list-item-content class="py-2">
          <v-list-item-subtitle v-if="parents.length > 0" class="d-flex gap-1">
            <template v-for="(parent, index) in parents">
              <span v-if="index" :key="parent._meta.self + 'divider'">/</span>
              <span :key="parent._meta.self" class="e-checklist-item-parent-name">{{
                parent.text
              }}</span>
            </template>
          </v-list-item-subtitle>
          <v-list-item-title class="ec-checklist--item-title">
            {{ parents.map(({ position }) => position + 1 + '.').join('')
            }}{{ item.position + 1 }}. {{ item.text }}
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </template>
  </div>
</template>
<script>
export default {
  name: 'ChecklistItems',
  props: {
    activeChecklists: {
      type: Array,
      required: true,
    },
    layoutMode: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
<style scoped>
.e-checklist-item-parent-name {
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ec-checklist--item-title {
  white-space: normal;
  line-height: 1.33;
}
</style>
