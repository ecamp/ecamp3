<template>
  <content-node-content :content-node="contentNode" :icon-path="mdiClipboardListOutline">
    <generic-error-message v-if="error" :error="error" />

    <div v-for="entry in checklistsWithItems" :key="entry.checklist.id" class="tw-mb-2">
      <div class="tw-font-bold">{{ entry.checklist.name }}</div>
      <ul class="tw-list-disc tw-list-inside">
        <li v-for="item in entry.items" :key="item.id">
          <span class="tw-mr-1">{{ item.number }}</span> {{ item.text }}
        </li>
      </ul>
    </div>
  </content-node-content>
</template>

<script setup>
import uniqWith from 'lodash/uniqWith.js'
import sortBy from 'lodash/sortBy.js'

const props = defineProps({
  contentNode: { type: Object, required: true },
})

const { error } = await useAsyncData(
  `ContentNodeChecklist-${props.contentNode._meta.self}`,
  async () => {
    return await props.contentNode.checklistItems().$loadItems()
  }
)

async function calculateItemNumber(item) {
  if (!item.parent) {
    return item.position + 1
  }

  const parent = await item.parent()._meta.load
  return (await calculateItemNumber(parent)) + '.' + (item.position + 1)
}

const items = await Promise.all(
  props.contentNode.checklistItems().items.map(async (item) => {
    const number = await calculateItemNumber(item)
    return {
      ...item,
      number,
    }
  })
)

const checklists = uniqWith(
  props.contentNode
    .checklistItems()
    .items.map((checklistItem) => checklistItem.checklist()),
  function (checklist1, checklist2) {
    return checklist1._meta.self === checklist2._meta.self
  }
)

const checklistsWithItems = checklists.map((checklist) => ({
  checklist,
  items: sortBy(
    items.filter((item) => item.checklist()._meta.self === checklist._meta.self),
    'number'
  ),
}))
</script>

<script>
import ContentNodeContent from './ContentNodeContent.vue'
import { mdiClipboardListOutline } from '@mdi/js'

export default {
  components: {
    ContentNodeContent,
  },
  data() {
    return {
      mdiClipboardListOutline,
    }
  },
}
</script>

<style scoped lang="scss"></style>
